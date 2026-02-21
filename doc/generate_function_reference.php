<?php

declare(strict_types=1);

if ($argc < 3) {
    fwrite(STDERR, "Usage: php doc/generate_function_reference.php <output.md> <file1.php> [file2.php ...]\n");
    exit(1);
}

$outputFile = $argv[1];
$files = array_slice($argv, 2);

$md = [];
$md[] = '# Funktionsreferenz';
$md[] = '';
$md[] = 'Automatisch generiert am: ' . date('Y-m-d H:i:s');
$md[] = '';

foreach ($files as $file) {
    if (!is_file($file)) {
        continue;
    }

    $code = file_get_contents($file);
    if ($code === false) {
        continue;
    }

    $tokens = token_get_all($code);
    $entries = extractFunctions($tokens);
    if ($entries === []) {
        continue;
    }

    $md[] = "## `{$file}`";
    $md[] = '';

    foreach ($entries as $entry) {
        $visibility = $entry['visibility'] !== '' ? $entry['visibility'] . ' ' : '';
        $static = $entry['static'] ? 'static ' : '';
        $signature = $visibility . $static . $entry['name'] . '(' . implode(', ', $entry['params']) . ')';
        $return = $entry['return'] !== '' ? $entry['return'] : 'mixed|null';
        $doc = $entry['doc'] !== '' ? trim($entry['doc']) : 'Kein Docblock gefunden.';

        $md[] = "### `{$signature}`";
        $md[] = '';
        $md[] = "- Zeile: {$entry['line']}";
        $md[] = "- Rueckgabe: `{$return}`";
        $md[] = '';
        $md[] = '```text';
        $md[] = $doc;
        $md[] = '```';
        $md[] = '';
    }
}

file_put_contents($outputFile, implode("\n", $md) . "\n");

function extractFunctions(array $tokens): array
{
    $entries = [];
    $context = [
        'visibility' => '',
        'static' => false,
        'doc' => '',
    ];

    for ($i = 0, $max = count($tokens); $i < $max; $i++) {
        $tok = $tokens[$i];

        if (!is_array($tok)) {
            continue;
        }

        if ($tok[0] === T_DOC_COMMENT) {
            $context['doc'] = $tok[1];
            continue;
        }

        if ($tok[0] === T_PUBLIC) {
            $context['visibility'] = 'public';
            continue;
        }
        if ($tok[0] === T_PROTECTED) {
            $context['visibility'] = 'protected';
            continue;
        }
        if ($tok[0] === T_PRIVATE) {
            $context['visibility'] = 'private';
            continue;
        }
        if ($tok[0] === T_STATIC) {
            $context['static'] = true;
            continue;
        }

        if ($tok[0] !== T_FUNCTION) {
            if (!in_array($tok[0], [T_WHITESPACE, T_COMMENT], true)) {
                if ($tok[0] !== T_ATTRIBUTE) {
                    $context = ['visibility' => '', 'static' => false, 'doc' => ''];
                }
            }
            continue;
        }

        $name = getFunctionName($tokens, $i);
        if ($name === null) {
            continue;
        }

        $params = getParamNames($tokens, $i);
        $return = getReturnType($tokens, $i) ?? '';
        $entries[] = [
            'name' => $name,
            'params' => $params,
            'return' => $return,
            'visibility' => $context['visibility'],
            'static' => $context['static'],
            'doc' => $context['doc'],
            'line' => $tok[2] ?? 0,
        ];

        $context = ['visibility' => '', 'static' => false, 'doc' => ''];
    }

    return $entries;
}

function getFunctionName(array $tokens, int $index): ?string
{
    for ($i = $index + 1, $max = count($tokens); $i < $max; $i++) {
        $tok = $tokens[$i];
        if (is_array($tok) && in_array($tok[0], [T_WHITESPACE, T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG], true)) {
            continue;
        }
        if ($tok === '&') {
            continue;
        }
        if (is_array($tok) && $tok[0] === T_STRING) {
            return $tok[1];
        }
        return null;
    }
    return null;
}

function getParamNames(array $tokens, int $index): array
{
    $params = [];
    $inParams = false;
    $depth = 0;
    for ($i = $index + 1, $max = count($tokens); $i < $max; $i++) {
        $tok = $tokens[$i];
        $text = is_array($tok) ? $tok[1] : $tok;
        if (!$inParams) {
            if ($text === '(') {
                $inParams = true;
                $depth = 1;
            }
            continue;
        }
        if ($text === '(') {
            $depth++;
            continue;
        }
        if ($text === ')') {
            $depth--;
            if ($depth === 0) {
                break;
            }
            continue;
        }
        if ($depth === 1 && is_array($tok) && $tok[0] === T_VARIABLE) {
            $params[] = $tok[1];
        }
    }
    return $params;
}

function getReturnType(array $tokens, int $index): ?string
{
    $seenParams = false;
    $depth = 0;
    for ($i = $index + 1, $max = count($tokens); $i < $max; $i++) {
        $tok = $tokens[$i];
        $text = is_array($tok) ? $tok[1] : $tok;
        if (!$seenParams) {
            if ($text === '(') {
                $seenParams = true;
                $depth = 1;
            }
            continue;
        }
        if ($text === '(') {
            $depth++;
            continue;
        }
        if ($text === ')') {
            $depth--;
            if ($depth === 0) {
                break;
            }
            continue;
        }
    }

    if (!$seenParams) {
        return null;
    }

    $i++;
    while ($i < $max) {
        $tok = $tokens[$i];
        if (is_array($tok) && in_array($tok[0], [T_WHITESPACE, T_COMMENT], true)) {
            $i++;
            continue;
        }
        break;
    }

    if ($i >= $max || (is_array($tokens[$i]) ? $tokens[$i][1] : $tokens[$i]) !== ':') {
        return null;
    }

    $i++;
    $parts = '';
    while ($i < $max) {
        $tok = $tokens[$i];
        $text = is_array($tok) ? $tok[1] : $tok;
        if ($text === '{' || $text === ';') {
            break;
        }
        if (is_array($tok) && in_array($tok[0], [T_WHITESPACE, T_COMMENT], true)) {
            $i++;
            continue;
        }
        $parts .= $text;
        $i++;
    }

    $parts = trim($parts);
    return $parts !== '' ? $parts : null;
}

