<?php

declare(strict_types=1);

if ($argc < 2) {
    fwrite(STDERR, "Usage: php doc/enrich_function_docblocks.php <file1.php> [file2.php ...]\n");
    exit(1);
}

foreach (array_slice($argv, 1) as $file) {
    if (!is_file($file)) {
        continue;
    }

    $src = file_get_contents($file);
    if ($src === false) {
        continue;
    }

    $new = preg_replace_callback(
        '/\/\*\*\R(?P<body>[\s\S]*?)\R(?P<indent>\h*)\*\//m',
        static function (array $m): string {
            $body = $m['body'];
            $indent = $m['indent'];
            $lines = preg_split('/\R/', $body) ?: [];
            if ($lines === []) {
                return $m[0];
            }

            $first = trim($lines[0]);
            if (!preg_match('/^\*\s+Kurzbeschreibung Funktion\s+([A-Za-z0-9_]+)\s*$/', $first, $mm)) {
                return $m[0];
            }

            $fn = $mm[1];
            $summary = buildSummary($fn);

            $out = [];
            $out[] = $indent . '/**';
            $out[] = $indent . ' * ' . $summary;
            $out[] = $indent . ' *';

            $sawTag = false;
            foreach (array_slice($lines, 1) as $line) {
                $trim = trim($line);
                if ($trim === '*') {
                    continue;
                }

                if (preg_match('/^\*\s+@param\s+([^\s]+)\s+(\$[A-Za-z_][A-Za-z0-9_]*)\s*(.*)$/', $trim, $pm)) {
                    $sawTag = true;
                    $type = $pm[1];
                    $name = $pm[2];
                    $desc = trim($pm[3]);
                    if ($desc === '' || str_starts_with($desc, 'Kurzbeschreibung')) {
                        $desc = buildParamDescription($type, $name);
                    }
                    $out[] = $indent . ' * @param ' . $type . ' ' . $name . ' ' . $desc;
                    continue;
                }

                if (preg_match('/^\*\s+@return\s+([^\s]+)\s*(.*)$/', $trim, $rm)) {
                    $sawTag = true;
                    $type = $rm[1];
                    $desc = trim($rm[2]);
                    if ($desc === '' || str_starts_with($desc, 'Kurzbeschreibung')) {
                        $desc = buildReturnDescription($type);
                    }
                    $out[] = $indent . ' * @return ' . $type . ' ' . $desc;
                    continue;
                }

                if (str_starts_with($trim, '* @')) {
                    $sawTag = true;
                    $out[] = $indent . ' ' . $trim;
                    continue;
                }
            }

            if (!$sawTag) {
                $out[] = $indent . ' * @return mixed Rueckgabewert der Funktion.';
            }

            $out[] = $indent . ' */';
            return implode("\n", $out);
        },
        $src
    );

    if ($new !== null && $new !== $src) {
        file_put_contents($file, $new);
    }
}

function splitFunctionName(string $name): array
{
    $name = trim($name);
    if ($name === '__construct') {
        return ['construct'];
    }

    $name = str_replace('_', ' ', $name);
    $name = preg_replace('/([a-z0-9])([A-Z])/', '$1 $2', $name) ?? $name;
    $parts = preg_split('/\s+/', strtolower($name)) ?: [];
    return array_values(array_filter($parts, static fn(string $p): bool => $p !== ''));
}

function buildSummary(string $fn): string
{
    if ($fn === '__construct') {
        return 'Initialisiert die Klasse und setzt die benoetigten Startwerte.';
    }

    $parts = splitFunctionName($fn);
    if ($parts === []) {
        return 'Fuehrt die Funktion aus.';
    }

    $prefix = $parts[0];
    $rest = implode(' ', array_slice($parts, 1));
    $rest = trim($rest);

    return match ($prefix) {
        'get' => $rest !== ''
            ? 'Liest ' . $rest . ' und gibt den Wert zurueck.'
            : 'Liest einen Wert und gibt ihn zurueck.',
        'set' => $rest !== ''
            ? 'Setzt ' . $rest . ' auf den uebergebenen Wert.'
            : 'Setzt einen Wert.',
        'is', 'has', 'can' => $rest !== ''
            ? 'Prueft, ob ' . $rest . ' zutrifft.'
            : 'Prueft eine Bedingung.',
        'build', 'create' => $rest !== ''
            ? 'Erzeugt ' . $rest . ' auf Basis der Eingabedaten.'
            : 'Erzeugt einen neuen Wert oder eine neue Struktur.',
        'save' => $rest !== ''
            ? 'Speichert ' . $rest . ' persistent.'
            : 'Speichert Daten persistent.',
        'load', 'read' => $rest !== ''
            ? 'Laedt ' . $rest . ' aus der Quelle.'
            : 'Laedt Daten aus der Quelle.',
        'write' => $rest !== ''
            ? 'Schreibt ' . $rest . ' in das Zielsystem.'
            : 'Schreibt Daten in das Zielsystem.',
        'delete', 'remove' => $rest !== ''
            ? 'Entfernt ' . $rest . '.'
            : 'Entfernt Daten.',
        'render', 'show' => $rest !== ''
            ? 'Stellt ' . $rest . ' fuer die Ausgabe dar.'
            : 'Stellt Inhalte fuer die Ausgabe dar.',
        'handle' => $rest !== ''
            ? 'Verarbeitet ' . $rest . ' entsprechend der Logik.'
            : 'Verarbeitet die Anfrage entsprechend der Logik.',
        'list' => $rest !== ''
            ? 'Liefert eine Liste von ' . $rest . '.'
            : 'Liefert eine Liste von Eintraegen.',
        'diff' => $rest !== ''
            ? 'Vergleicht ' . $rest . ' und liefert die Unterschiede.'
            : 'Vergleicht Daten und liefert die Unterschiede.',
        default => 'Fuehrt ' . implode(' ', $parts) . ' entsprechend der internen Logik aus.',
    };
}

function buildParamDescription(string $type, string $name): string
{
    $clean = ltrim($name, '$');
    if ($type === 'bool') {
        return 'Steuert den booleschen Zustand fuer ' . $clean . '.';
    }
    if ($type === 'array') {
        return 'Enthaelt die Daten fuer ' . $clean . '.';
    }
    if ($type === 'string') {
        return 'Textwert fuer ' . $clean . '.';
    }
    if ($type === 'int' || $type === 'float') {
        return 'Numerischer Wert fuer ' . $clean . '.';
    }

    return 'Eingabewert fuer ' . $clean . '.';
}

function buildReturnDescription(string $type): string
{
    if ($type === 'void') {
        return 'Kein Rueckgabewert.';
    }
    if ($type === 'bool') {
        return 'True bei Erfolg, sonst false.';
    }
    if ($type === 'array') {
        return 'Rueckgabe als Array mit den ermittelten Daten.';
    }
    if ($type === 'string') {
        return 'Rueckgabe als Text.';
    }

    return 'Rueckgabewert der Funktion.';
}
