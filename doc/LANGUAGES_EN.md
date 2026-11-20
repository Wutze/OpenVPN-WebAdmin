# Language and Help System

## Language

`Micro\OpenvpnWebadmin\Util\Lang\` loads `Lang/<locale>/lang.php`. Existing locale directories are detected dynamically by their ISO language codes. The application starts the session in `src/load.php`, then reads `$_SESSION['lang']` and passes the value to `Lang::init()`.

When the language is changed, the router stores the selected language code in the existing session and reinitializes the class. The reusable language class itself does not handle cookies or sessions.

## Help Texts and Document References

A regular text key can be extended with a marker. The marker is removed when the text is rendered; the modal adds a help icon next to the marked text.

```php
'_WELCOME' => 'Welcome [HELP_DOC/other/:TEXT]',
```

`[HELP_DOC/<section>/:<document>]` refers to `doc/<section>/<document>.md`. Examples:

- `HELP_DOC/other/:TEXT` → `doc/other/TEXT.md`
- `HELP_DOC/system/:HINWEIS` → `doc/system/HINWEIS.md`

The components may contain only letters, digits, underscores, and hyphens. `HELP_*` keys contain labels and messages for the help modal. Missing Markdown files are reported as unavailable; no fallback file from another language is used.

## For Developers

All language files are copied to `/src/Lang`. The directory name is the ISO language code, and the language file itself is always named `lang.php`.

Ensure that individual entries **ALWAYS** appear on the same line number across all language files! If you cannot find a translation for a variable in the language template, use English as the reference.

## Updates

Currently, language files are overwritten with the developer's original versions, meaning custom additions to help texts are lost. The same applies to help documents, which are currently all replaced by the versions from the Git server. A change to address this is planned and has been added to my [Wishlist](../WISHLIST.md).
