<?php

declare(strict_types=1);

if ($argc < 2) {
    fwrite(STDERR, "Usage: php doc/normalize_docblocks.php <file1.php> [file2.php ...]\n");
    exit(1);
}

foreach (array_slice($argv, 1) as $file) {
    if (!is_file($file)) {
        continue;
    }

    $content = file_get_contents($file);
    if ($content === false) {
        continue;
    }

    $new = $content;

    // Move generated docblocks before modifiers/signature.
    $new = preg_replace(
        '/((?:(?:public|protected|private|static|final|abstract)\s+)+)(\/\*\*\R\s*\* Kurzbeschreibung Funktion[\s\S]*?\*\/\R)\s*function/s',
        '$2$1function',
        $new
    );

    // If a manual docblock already exists, drop the generated generic one.
    $new = preg_replace(
        '/(\/\*\*(?:(?!\*\/)[\s\S])*?\*\/\R\s*)(\/\*\*\R\s*\* Kurzbeschreibung Funktion[\s\S]*?\*\/\R)\s*((?:(?:public|protected|private|static|final|abstract)\s+)*function)/s',
        '$1$3',
        $new
    );

    // Collapse excessive spaces between visibility/modifier tokens and function.
    $new = preg_replace(
        '/\b(public|protected|private|static|final|abstract)\h+(?=(?:public|protected|private|static|final|abstract|function)\b)/',
        '$1 ',
        $new
    );

    // Re-indent docblocks directly above function signatures to match signature indentation.
    $new = normalizeFunctionDocblocks($new);

    if ($new !== $content) {
        file_put_contents($file, $new);
    }
}

/**
 * Normalize docblock line prefixes and set a specific base indentation.
 *
 * @param string $doc    Raw docblock content including trailing newline.
 * @param string $indent Base indentation that should be used.
 * @return string Normalized docblock.
 */
function normalizeDocblockIndentation(string $doc, string $indent): string
{
    $trimmed = rtrim($doc, "\r\n");
    $lines = preg_split('/\R/', $trimmed) ?: [];
    if ($lines === []) {
        return $doc;
    }

    $out = [];
    $lastIndex = count($lines) - 1;

    foreach ($lines as $i => $line) {
        if ($i === 0) {
            $out[] = $indent . '/**';
            continue;
        }
        if ($i === $lastIndex) {
            $out[] = $indent . ' */';
            continue;
        }

        $t = ltrim($line);
        if ($t === '') {
            $out[] = $indent . ' *';
            continue;
        }
        if ($t[0] === '*') {
            $out[] = $indent . ' ' . $t;
            continue;
        }

        $out[] = $indent . ' * ' . $t;
    }

    return implode("\n", $out) . "\n";
}

/**
 * Normalize function docblocks line-based to avoid cross-block regex corruption.
 *
 * @param string $content Full PHP file content.
 * @return string Updated file content.
 */
function normalizeFunctionDocblocks(string $content): string
{
    $lines = preg_split('/\R/', $content) ?: [];
    if ($lines === []) {
        return $content;
    }

    for ($i = 0; $i < count($lines); $i++) {
        if (!preg_match('/^(?P<indent>[\t ]*)(?:(?:public|protected|private|static|final|abstract)\s+)*function\b/', $lines[$i], $m)) {
            continue;
        }

        $indent = $m['indent'];
        $end = $i - 1;
        while ($end >= 0 && trim($lines[$end]) === '') {
            $end--;
        }
        if ($end < 0 || trim($lines[$end]) !== '*/') {
            continue;
        }

        $start = $end - 1;
        $scan = 0;
        while ($start >= 0 && $scan < 80 && trim($lines[$start]) !== '/**') {
            $start--;
            $scan++;
        }
        if ($start < 0 || trim($lines[$start]) !== '/**') {
            continue;
        }

        $rawDoc = implode("\n", array_slice($lines, $start, $end - $start + 1)) . "\n";
        $normalized = normalizeDocblockIndentation($rawDoc, $indent);
        $replacement = preg_split('/\R/', rtrim($normalized, "\r\n")) ?: [];
        array_splice($lines, $start, $end - $start + 1, $replacement);
        $i = $start + count($replacement);
    }

    return implode("\n", $lines);
}
