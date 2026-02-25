# Dokumentation & Wartung

Dieses Verzeichnis enthaelt die automatische Funktionsdokumentation und die Generator-Skripte.

## Enthaltene Dateien

- `doc/functions.md`: automatisch erzeugte Referenz aller gefundenen PHP-Funktionen/Methoden.
- `doc/apply_function_docblocks.php`: fuegt generische Docblocks zu Funktionen ohne vorhandenen Docblock hinzu.
- `doc/normalize_docblocks.php`: bereinigt die automatisch eingefuegten Docblocks (Position/Format).
- `doc/generate_function_reference.php`: erzeugt die Markdown-Referenzdatei.

## Erneut ausfuehren

```bash
files=$(find . -type f -name '*.php' -not -path './vendor/*' -not -path './storage/*' -not -path './doc/*' | sort)
php doc/apply_function_docblocks.php $files
php doc/normalize_docblocks.php $files
php doc/generate_function_reference.php doc/functions.md $files
```

## Zusatzpunkte (was oft vergessen wird)

- Nach jeder Massenanpassung immer Syntaxcheck laufen lassen (`php -l`).
- Bei neuen Funktionen direkt Docblock pflegen, sonst beim naechsten Lauf automatisch generiert.
- `*.md` und `info.txt` bleiben als optionale Doku-Dateien im Projekt bestehen.
