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
        self::$debug = $debug ?? (isset($_ENV['DEBUG']) ? filter_var($_ENV['DEBUG'], FILTER_VALIDATE_BOOL) : true);

        set_exception_handler([self::class, 'handleException']);
        set_error_handler([self::class, 'handleError']);
    }

    /**
     * Debug-Ausgabe auf der Seite (Bootstrap Collapse)
     */
    public static function debug(...$vars): void
    {
        if (!self::$debug || self::$env !== 'development') return;

        self::$counter++;
        $counter = self::$counter;
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1)[0] ?? [];
        $callerInfo = ($backtrace['file'] ?? 'unknown') . ':' . ($backtrace['line'] ?? '?');
        $mainId = "debug-$counter";

        $HTML = <<<HTML
<div class="container mt-1 p-0" style="font-family: monospace; font-size: 0.85rem;">
    <div class="card border-dark mb-1">
        <div class="card-header bg-dark text-white py-1 px-2">
            <a class="text-white text-decoration-none" data-bs-toggle="collapse" href="#$mainId" role="button" aria-expanded="true" aria-controls="$mainId">
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

        @file_put_contents(self::$logfile, $output, FILE_APPEND);
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

        // Sicher ins Log schreiben
        $logMessage = "[" . date('Y-m-d H:i:s') . "] " . print_r($error, true) . PHP_EOL;
        @file_put_contents(__DIR__ . '/../../storage/logs/exceptions.log', $logMessage, FILE_APPEND);

        // Nur im Development schön ausgeben
        if (self::$debug && self::$env === 'development') {
            self::debug($error); 
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
        self::debug([
            'Error' => $errstr,
            'File' => $errfile,
            'Line' => $errline,
            'Code' => $errno
        ]);
        return true; // Fehler wurde behandelt
    }

    /**
     * Kurzbeschreibung Funktion render
     *
     * @return void
     */
public static function render(): void
    {
        if (empty(self::$buffer)) return;

        echo '<div id="debug-container" class="container-fluid mt-2">';
        foreach (self::$buffer as $html) {
            echo $html;
        }
        echo '</div>';
    }
}