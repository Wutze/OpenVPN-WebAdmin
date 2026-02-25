<?php

declare(strict_types=1);

if ($argc < 2) {
    fwrite(STDERR, "Usage: php doc/apply_function_docblocks.php <file1.php> [file2.php ...]\n");
    exit(1);
}

foreach (array_slice($argv, 1) as $file) {
    if (!is_file($file)) {
        continue;
    }

    $code = file_get_contents($file);
    if ($code === false) {
        continue;
    }

    $lines = preg_split("/\r\n|\n|\r/", $code) ?: [];
    $tokens = token_get_all($code);
    $out = '';
    $count = count($tokens);

    for ($i = 0; $i < $count; $i++) {
        $tok = $tokens[$i];

        if (!is_array($tok) || $tok[0] !== T_FUNCTION) {
            $out .= is_array($tok) ? $tok[1] : $tok;
            continue;
        }

        if (hasDocComment($tokens, $i)) {
            $out .= $tok[1];
            continue;
        }

        if (isAnonymousFunction($tokens, $i)) {
            $out .= $tok[1];
            continue;
        }

        $name = getFunctionName($tokens, $i) ?? 'funktion';
        $params = getParameterVariables($tokens, $i);
        $returnType = getReturnType($tokens, $i) ?? 'mixed|null';
        $lineNo = $tok[2] ?? 1;
        $indent = getIndentation($lines, $lineNo);

        $doc = buildDocblock($indent, $name, $params, $returnType);
        $out .= $doc;
        $out .= $tok[1];
    }

    if ($out !== $code) {
        file_put_contents($file, $out);
    }
}

function hasDocComment(array $tokens, int $index): bool
{
    for ($i = $index - 1; $i >= 0; $i--) {
        $tok = $tokens[$i];
        if (is_array($tok) && in_array($tok[0], [T_WHITESPACE, T_COMMENT], true)) {
            continue;
        }
        if (is_array($tok) && in_array($tok[0], [T_PUBLIC, T_PROTECTED, T_PRIVATE, T_STATIC, T_FINAL, T_ABSTRACT], true)) {
            continue;
        }
        if (is_array($tok) && $tok[0] === T_ATTRIBUTE) {
            continue;
        }
        if ($tok === ']') {
            continue;
        }
        if (is_array($tok) && $tok[0] === T_DOC_COMMENT) {
            return true;
        }
        return false;
    }

    return false;
}

function isAnonymousFunction(array $tokens, int $index): bool
{
    for ($i = $index + 1, $max = count($tokens); $i < $max; $i++) {
        $tok = $tokens[$i];
        if (is_array($tok) && $tok[0] === T_WHITESPACE) {
            continue;
        }
        if (is_array($tok) && $tok[0] === T_AMPERSAND_NOT_FOLLOWED_BY_VAR_OR_VARARG) {
            continue;
        }
        if ($tok === '&') {
            continue;
        }
        if ($tok === '(') {
            return true;
        }
        return !(is_array($tok) && $tok[0] === T_STRING);
    }

    return false;
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

function getParameterVariables(array $tokens, int $index): array
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
            $name = $tok[1];
            if (!in_array($name, $params, true)) {
                $params[] = $name;
            }
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

function getIndentation(array $lines, int $lineNo): string
{
    $idx = max(0, $lineNo - 1);
    $line = $lines[$idx] ?? '';
    if (preg_match('/^(\s*)/', $line, $m) === 1) {
        return $m[1];
    }
    return '';
}

function buildDocblock(string $indent, string $name, array $params, string $returnType): string
{
    $doc = $indent . "/**\n";
    $doc .= $indent . " * Kurzbeschreibung Funktion {$name}\n";
    $doc .= $indent . " *\n";
    foreach ($params as $param) {
        $doc .= $indent . " * @param mixed {$param}\n";
    }
    $doc .= $indent . " * @return {$returnType}\n";
    $doc .= $indent . " */\n";
    return $doc;
}
