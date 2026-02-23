<?php
    /**
     * this File is part of OpenVPN-WebAdmin - (c) 2020/2025 OpenVPN-WebAdmin
     *
     * NOTICE OF LICENSE
     *
     * GNU AFFERO GENERAL PUBLIC LICENSE V3
     * that is bundled with this package in the file LICENSE.md.
     * It is also available through the world-wide-web at this URL:
     * https://www.gnu.org/licenses/agpl-3.0.en.html
     *
     * @author      Wutze
     * @copyright   2025 OpenVPN-WebAdmin
     * @link        https://github.com/Wutze/OpenVPN-WebAdmin
     * @see         Internal Documentation ~/doc/
     * @version     2.0.0
     */

namespace Micro\OpenvpnWebadmin\Core;

class Debug
{
private static int $counter = 0;
private static string $logfile = __DIR__ . '/../../storage/logs/debug.log';
private static string $exceptionsLogfile = __DIR__ . '/../../storage/logs/exceptions.log';
private static string $env = 'development'; // default environment
private static bool $debug = false;          // default debug on
private static array $buffer = [];

    /**
     * Initialisiert Debug-Umgebung
     */
    public static function init(string $env = null, string $logfile = null, ?bool $debug = null): void
    {
        self::$env = $env ?? ($_ENV['APP_ENV'] ?? 'development');
        self::$logfile = $logfile ?? ($_ENV['DEBUG_LOGFILE'] ?? __DIR__ . '/../../storage/logs/debug.log');
        self::$exceptionsLogfile = $_ENV['DEBUG_EXCEPTIONS_LOGFILE'] ?? __DIR__ . '/../../storage/logs/exceptions.log';
        self::$debug = $debug ?? (isset($_ENV['DEBUG']) ? filter_var($_ENV['DEBUG'], FILTER_VALIDATE_BOOL) : true);

        if (self::$debug) {
            self::ensureLogTargets();
        }

        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
    }

    /**
     * Debug-Ausgabe auf der Seite (Bootstrap Collapse)
     */
    public static function debug(...$vars): void
    {
        if (!self::$debug || self::$env !== 'development') return;

        if (count($vars) === 1 && is_object($vars[0])) {
            $vars[] = self::buildAutoContextForObject($vars[0]);
        }

        self::$counter++;
        $counter = self::$counter;
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0] ?? [];
        $callerInfo = ($backtrace['file'] ?? 'unknown') . ':' . ($backtrace['line'] ?? '?');
        $mainId = "debug-$counter";

        $HTML = <<<HTML
<div class="container mt-1 p-0" style="font-family: monospace; font-size: 0.85rem;">
    <div class="card border-dark mb-1">
        <div class="card-header py-1 px-2">
            <a class="text-decoration-none" data-bs-toggle="collapse" href="#$mainId" role="button" aria-expanded="true" aria-controls="$mainId">
                <i class="bi bi-crosshair mini-led-green"></i> Debug #$counter – $callerInfo
            </a>
        </div>
        <div id="$mainId" class="collapse show">
            <div class="card-body py-1 px-2">
HTML;

        foreach ($vars as $i => $value) {
            $type = gettype($value);
            $varId = "debug-var-{$counter}-{$i}";
            $safe = htmlspecialchars(print_r($value, true));

            $HTML .= <<<HTML
<div class="card mb-2">
    <div class="card-header p-2 bg-light">
        <a class="text-dark small text-decoration-none" data-bs-toggle="collapse" href="#$varId" role="button" aria-expanded="false" aria-controls="$varId">
            <i class="bi bi-arrow-right-short"></i>#$i (Typ: $type)
        </a>
    </div>
    <div id="$varId" class="collapse">
        <div class="card-body bg-light" style="overflow-x:auto;">
            <pre class="mb-0">$safe</pre>
        </div>
    </div>
</div>
HTML;
        }

        $HTML .= <<<HTML
            </div>
        </div>
    </div>
</div>
HTML;
self::$buffer[] = $HTML;

    }

    /**
     * Baut automatisch einen zweiten Debug-Kontext fuer Objekt-Calls auf.
     *
     * @param object $object
     * @return array<string,mixed>
     */
    private static function buildAutoContextForObject(object $object): array
    {
        return [
            'class' => get_class($object),
            'object_properties' => self::extractObjectProperties($object),
            'request_method' => (string)($_SERVER['REQUEST_METHOD'] ?? 'GET'),
            'get' => $_GET,
            'post' => $_POST,
            'session' => $_SESSION ?? [],
        ];
    }

    /**
     * Extrahiert alle Objekt-Properties inklusive private/protected per Reflection.
     *
     * @param object $object
     * @return array<string,mixed>
     */
    private static function extractObjectProperties(object $object): array
    {
        $result = [];
        $ref = new \ReflectionObject($object);

        do {
            foreach ($ref->getProperties() as $property) {
                if ($property->isStatic()) {
                    continue;
                }
                $property->setAccessible(true);
                $key = $property->getName();
                if (array_key_exists($key, $result)) {
                    continue;
                }
                $result[$key] = $property->getValue($object);
            }
            $ref = $ref->getParentClass();
        } while ($ref instanceof \ReflectionClass);

        return $result;
    }

    /**
     * Debug-Ausgabe in Logfile
     */
    public static function log(...$vars): void
    {
        if (!self::$debug || self::$env !== 'development') return;

        $timestamp = date('Y-m-d H:i:s');
        $output = "[$timestamp]\n";

        foreach ($vars as $i => $value) {
            $output .= "Argument " . ($i + 1) . " (".gettype($value)."):\n";
            $output .= print_r($value, true) . "\n";
        }

        $output .= str_repeat('-', 80) . "\n";

        self::ensureLogTargets();
        @file_put_contents(self::$logfile, $output, FILE_APPEND);
    }

    /**
     * Schreibt Exceptions strukturiert in exceptions.log (nur bei DEBUG=true).
     *
     * @param mixed $e
     * @param mixed $context
     * @return void
     */
    public static function logException(\Throwable $e, string $context = ''): void
    {
        if (!self::$debug) {
            return;
        }

        self::ensureLogTargets();
        $payload = [
            'time' => date('Y-m-d H:i:s'),
            'context' => $context,
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString(),
        ];

        @file_put_contents(
            self::$exceptionsLogfile,
            print_r($payload, true) . PHP_EOL . str_repeat('-', 80) . PHP_EOL,
            FILE_APPEND
        );
    }

    /**
     * Debug + exit
     */
    public static function dd(...$vars): void
    {
        self::debug(...$vars);
        exit;
    }

    /**
     * Exception-Handler
     */
    public static function handleException(\Throwable $e): void
    {
        $error = [
            'Exception' => $e->getMessage(),
            'File'      => $e->getFile(),
            'Line'      => $e->getLine(),
            'Trace'     => $e->getTraceAsString()
        ];

        // Sicher ins Log schreiben (nur wenn DEBUG=true)
        self::logException($e, 'uncaught_exception');

        // Nur im Development direkt ausgeben (ohne Debug-Container-Rendering)
        if (self::$debug && self::$env === 'development') {
            self::renderExceptionScreen($error);
        } else {
            echo "Ein interner Fehler ist aufgetreten. Bitte den Administrator kontaktieren.";
        }

        exit(1);
    }

    /**
     * Error-Handler
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        if (self::$debug) {
            self::ensureLogTargets();
            $payload = [
                'time' => date('Y-m-d H:i:s'),
                'context' => 'php_error',
                'code' => $errno,
                'message' => $errstr,
                'file' => $errfile,
                'line' => $errline,
            ];
            @file_put_contents(
                self::$exceptionsLogfile,
                print_r($payload, true) . PHP_EOL . str_repeat('-', 80) . PHP_EOL,
                FILE_APPEND
            );
        }

        // PHP-Fehler nur in exceptions.log schreiben, nicht im Runtime-Debug-Modal anzeigen.
        return true; // Fehler wurde behandelt
    }

/**
 * Stellt Inhalte fuer die Ausgabe dar.
 *
 * @return void Kein Rueckgabewert.
 */
public static function render(): void
    {
        if (!self::$debug || self::$env !== 'development') {
            return;
        }

        echo '<div style="position:fixed;right:10px;bottom:10px;z-index:99999;padding:6px 10px;border-radius:6px;background:#111;color:#fff;font:12px/1.2 monospace;opacity:.9">DEBUG=true (development)</div>';

        $buffered = self::getBufferedOutput();
        if ($buffered !== '') {
            echo $buffered;
        }
    }

    /**
     * Liefert die gesammelte HTML-Debug-Ausgabe für das Debug-Modal.
     *
     * @return string
     */
    public static function getBufferedOutput(): string
    {
        if (!self::$debug || self::$env !== 'development') {
            return '';
        }

        if (self::$buffer === []) {
            return '';
        }

        $maxItems = 50;
        $chunks = array_slice(self::$buffer, -$maxItems);
        return implode("\n", $chunks);
    }

    /**
     * Gibt ungefangene Exceptions direkt im Browser aus (nur bei DEBUG=true).
     *
     * @param mixed $error
     * @return void
     */
    private static function renderExceptionScreen(array $error): void
    {
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/html; charset=utf-8');
        }

        $safe = htmlspecialchars(print_r($error, true), ENT_QUOTES, 'UTF-8');
        echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Debug Exception</title></head><body style="font-family: monospace; padding: 16px;">';
        echo '<h2 style="margin-top:0;">Unhandled Exception (DEBUG=true)</h2>';
        echo '<p>Details were also written to exceptions.log.</p>';
        echo '<pre style="background:#111;color:#eee;padding:12px;border-radius:6px;overflow:auto;">' . $safe . '</pre>';
        echo '</body></html>';
    }

    /**
     * Stellt sicher, dass die Logziele existieren und beschreibbar sind.
     */
    private static function ensureLogTargets(): void
    {
        $targets = [self::$logfile, self::$exceptionsLogfile];
        foreach ($targets as $target) {
            $dir = dirname($target);
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            if (!is_file($target)) {
                @touch($target);
            }
            @chmod($target, 0664);
        }
    }
}
