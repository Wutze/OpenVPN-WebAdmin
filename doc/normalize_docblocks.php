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

    if ($new !== $content) {
        file_put_contents($file, $new);
    }
}
