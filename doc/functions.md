# Funktionsreferenz

Automatisch generiert am: 2026-02-21 13:28:18

## `./composer-setup.php`

### `setupEnvironment()`

- Zeile: 21
- Rueckgabe: `mixed|null`

```php
/**
 * Initializes various values
 *
 * @throws RuntimeException If uopz extension prevents exit calls
 */
```

### `process($argv)`

- Zeile: 48
- Rueckgabe: `mixed|null`

```php
/**
 * Processes the installer
 */
```

### `displayHelp()`

- Zeile: 114
- Rueckgabe: `mixed|null`

```php
/**
 * Displays the help
 */
```

### `setUseAnsi($argv)`

- Zeile: 145
- Rueckgabe: `mixed|null`

```php
/**
 * Sets the USE_ANSI define for colorizing output
 *
 * @param array $argv Command-line arguments
 */
```

### `outputSupportsColor()`

- Zeile: 162
- Rueckgabe: `mixed|null`

```php
/**
 * Returns whether color output is supported
 *
 * @return bool
 */
```

### `getOptValue($opt, $argv, $default)`

- Zeile: 202
- Rueckgabe: `mixed|null`

```php
/**
 * Returns the value of a command-line option
 *
 * @param string $opt The command-line option to check
 * @param array $argv Command-line arguments
 * @param mixed $default Default value to be returned
 *
 * @return mixed The command-line value or the default
 */
```

### `checkParams($installDir, $version, $cafile)`

- Zeile: 229
- Rueckgabe: `mixed|null`

```php
/**
 * Checks that user-supplied params are valid
 *
 * @param mixed $installDir The required istallation directory
 * @param mixed $version The required composer version to install
 * @param mixed $cafile Certificate Authority file
 *
 * @return bool True if the supplied params are okay
 */
```

### `checkPlatform($warnings, $quiet, $disableTls, $install)`

- Zeile: 262
- Rueckgabe: `mixed|null`

```php
/**
 * Checks the platform for possible issues running Composer
 *
 * Errors are written to the output, warnings are saved for later display.
 *
 * @param array $warnings Populated by method, to be shown later
 * @param bool $quiet Quiet mode
 * @param bool $disableTls Bypass tls
 * @param bool $install If we are installing, rather than diagnosing
 *
 * @return bool True if there are no errors
 */
```

### `getPlatformIssues($errors, $warnings, $install)`

- Zeile: 295
- Rueckgabe: `mixed|null`

```php
/**
 * Checks platform configuration for common incompatibility issues
 *
 * @param array $errors Populated by method
 * @param array $warnings Populated by method
 * @param bool $install If we are installing, rather than diagnosing
 *
 * @return bool If any errors or warnings have been found
 */
```

### `outputIssues($issues)`

- Zeile: 501
- Rueckgabe: `mixed|null`

```php
/**
 * Outputs an array of issues
 *
 * @param array $issues
 */
```

### `showWarnings($warnings)`

- Zeile: 514
- Rueckgabe: `mixed|null`

```php
/**
 * Outputs any warnings found
 *
 * @param array $warnings
 */
```

### `showSecurityWarning($disableTls)`

- Zeile: 528
- Rueckgabe: `mixed|null`

```php
/**
 * Outputs an end of process warning if tls has been bypassed
 *
 * @param bool $disableTls Bypass tls
 */
```

### `out($text, $color, $newLine)`

- Zeile: 539
- Rueckgabe: `mixed|null`

```php
/**
 * colorize output
 */
```

### `getHomeDir()`

- Zeile: 565
- Rueckgabe: `mixed|null`

```php
/**
 * Returns the system-dependent Composer home location, which may not exist
 *
 * @return string
 */
```

### `getUserDir()`

- Zeile: 609
- Rueckgabe: `mixed|null`

```php
/**
 * Returns the location of the user directory from the environment
 * @throws RuntimeException If the environment value does not exists
 *
 * @return string
 */
```

### `useXdg()`

- Zeile: 624
- Rueckgabe: `mixed|null`

```php
/**
 * @return bool
 */
```

### `validateCaFile($contents)`

- Zeile: 645
- Rueckgabe: `mixed|null`

```php
/**
 * Kurzbeschreibung Funktion validateCaFile
 *
 * @param mixed $contents
 * @return mixed|null
 */
```

### `getIniMessage()`

- Zeile: 665
- Rueckgabe: `mixed|null`

```php
/**
 * Returns php.ini location information
 *
 * @return string
 */
```

### `public __construct($quiet, $disableTls, $caFile)`

- Zeile: 715
- Rueckgabe: `mixed|null`

```php
/**
     * Constructor - must not do anything that throws an exception
     *
     * @param bool $quiet Quiet mode
     * @param bool $disableTls Bypass tls
     * @param mixed $cafile Path to CA bundle, or false
     */
```

### `public run($version, $installDir, $filename, $channel)`

- Zeile: 736
- Rueckgabe: `mixed|null`

```php
/**
     * Runs the installer
     *
     * @param mixed $version Specific version to install, or false
     * @param mixed $installDir Specific installation directory, or false
     * @param string $filename Specific filename to save to, or composer.phar
     * @param string $channel Specific version channel to use
     * @throws Exception If anything other than a RuntimeException is caught
     *
     * @return bool If the installation succeeded
     */
```

### `protected initTargets($installDir, $filename)`

- Zeile: 777
- Rueckgabe: `mixed|null`

```php
/**
     * Initialization methods to set the required filenames and composer url
     *
     * @param mixed $installDir Specific installation directory, or false
     * @param string $filename Specific filename to save to, or composer.phar
     * @throws RuntimeException If the installation directory is not writable
     */
```

### `protected initTls()`

- Zeile: 797
- Rueckgabe: `mixed|null`

```php
/**
     * A wrapper around methods to check tls and write public keys
     * @throws RuntimeException If SHA384 is not supported
     */
```

### `protected getComposerHome()`

- Zeile: 826
- Rueckgabe: `mixed|null`

```php
/**
     * Returns the Composer home directory, creating it if required
     * @throws RuntimeException If the directory cannot be created
     *
     * @return string
     */
```

### `protected installKey($data, $path, $filename)`

- Zeile: 856
- Rueckgabe: `mixed|null`

```php
/**
     * Writes public key data to disc
     *
     * @param string $data The public key(s) in pem format
     * @param string $path The directory to write to
     * @param string $filename The name of the file
     * @throws RuntimeException If the file cannot be written
     *
     * @return string The path to the saved data
     */
```

### `protected install($version, $channel)`

- Zeile: 886
- Rueckgabe: `mixed|null`

```php
/**
     * The main install function
     *
     * @param mixed $version Specific version to install, or false
     * @param string $channel Version channel to use
     *
     * @return bool If the installation succeeded
     */
```

### `protected getVersion($channel, $version, $url, $error)`

- Zeile: 941
- Rueckgabe: `mixed|null`

```php
/**
     * Sets the version url, downloading version data if required
     *
     * @param string $channel Version channel to use
     * @param false|string $version Version to install, or set by method
     * @param null|string $url The versioned url, set by method
     * @param null|string $error Set by method on failure
     *
     * @return bool If the operation succeeded
     */
```

### `protected downloadVersionData($data, $error)`

- Zeile: 970
- Rueckgabe: `mixed|null`

```php
/**
     * Downloads and json-decodes version data
     *
     * @param null|array $data Downloaded version data, set by method
     * @param null|string $error Set by method on failure
     *
     * @return bool If the operation succeeded
     */
```

### `protected downloadToTmp($url, $signature, $error)`

- Zeile: 996
- Rueckgabe: `mixed|null`

```php
/**
     * A wrapper around the methods needed to download and save the phar
     *
     * @param string $url The versioned download url
     * @param null|string $signature Set by method on successful download
     * @param null|string $error Set by method on failure
     *
     * @return bool If the operation succeeded
     */
```

### `protected verifyAndSave($version, $signature, $error)`

- Zeile: 1029
- Rueckgabe: `mixed|null`

```php
/**
     * Verifies the downloaded file and saves it to the target location
     *
     * @param string $version The composer version downloaded
     * @param string $signature The digital signature to check
     * @param null|string $error Set by method on failure
     *
     * @return bool If the operation succeeded
     */
```

### `protected parseVersionData($data, $channel, $version, $url)`

- Zeile: 1060
- Rueckgabe: `mixed|null`

```php
/**
     * Parses an array of version data to match the required channel
     *
     * @param array $data Downloaded version data
     * @param mixed $channel Version channel to use
     * @param false|string $version Set by method
     * @param mixed $url The versioned url, set by method
     */
```

### `protected getSignature($url, $signature)`

- Zeile: 1090
- Rueckgabe: `mixed|null`

```php
/**
     * Downloads the digital signature of required phar file
     *
     * @param string $url The signature url
     * @param null|string $signature Set by method on success
     *
     * @return bool If the download succeeded
     */
```

### `protected verifySignature($version, $signature, $file)`

- Zeile: 1114
- Rueckgabe: `mixed|null`

```php
/**
     * Verifies the signature of the downloaded phar
     *
     * @param string $version The composer versione
     * @param string $signature The downloaded digital signature
     * @param string $file The temp phar file
     *
     * @return bool If the operation succeeded
     */
```

### `protected validatePhar($pharFile, $error)`

- Zeile: 1144
- Rueckgabe: `mixed|null`

```php
/**
     * Validates the downloaded phar file
     *
     * @param string $pharFile The temp phar file
     * @param null|string $error Set by method on failure
     *
     * @return bool If the operation succeeded
     */
```

### `protected getJsonError()`

- Zeile: 1172
- Rueckgabe: `mixed|null`

```php
/**
     * Returns a string representation of the last json error
     *
     * @return string The error string or code
     */
```

### `protected cleanUp($result)`

- Zeile: 1186
- Rueckgabe: `mixed|null`

```php
/**
     * Cleans up resources at the end of the installation
     *
     * @param bool $result If the installation succeeded
     */
```

### `protected outputErrors($errors)`

- Zeile: 1209
- Rueckgabe: `mixed|null`

```php
/**
     * Outputs unique errors when in quiet mode
     *
     */
```

### `protected uninstall()`

- Zeile: 1225
- Rueckgabe: `mixed|null`

```php
/**
     * Uninstalls newly-created files and directories on failure
     *
     */
```

### `public static getPKDev()`

- Zeile: 1245
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion getPKDev
     *
     * @return mixed|null
     */
```

### `public static getPKTags()`

- Zeile: 1270
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion getPKTags
     *
     * @return mixed|null
     */
```

### `public handleError($code, $msg)`

- Zeile: 1302
- Rueckgabe: `mixed|null`

```php
/**
     * Handle php errors
     *
     * @param mixed $code The error code
     * @param mixed $msg The error message
     */
```

### `public start()`

- Zeile: 1315
- Rueckgabe: `mixed|null`

```php
/**
     * Starts error-handling if not already active
     *
     * Any message is cleared
     */
```

### `public stop()`

- Zeile: 1329
- Rueckgabe: `mixed|null`

```php
/**
     * Stops error-handling if active
     *
     * Any message is preserved until the next call to start()
     */
```

### `public __construct($pattern)`

- Zeile: 1349
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $pattern
     * @return mixed|null
     */
```

### `public test($url)`

- Zeile: 1372
- Rueckgabe: `mixed|null`

```php
/**
     * Returns true if NO_PROXY contains getcomposer.org
     *
     * @param string $url http(s)://getcomposer.org
     *
     * @return bool
     */
```

### `public __construct($disableTls, $cafile)`

- Zeile: 1407
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $disableTls
     * @param mixed $cafile
     * @return mixed|null
     */
```

### `public get($url)`

- Zeile: 1427
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion get
     *
     * @param mixed $url
     * @return mixed|null
     */
```

### `protected getStreamContext($url)`

- Zeile: 1467
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion getStreamContext
     *
     * @param mixed $url
     * @return mixed|null
     */
```

### `protected getTlsStreamContextDefaults($cafile)`

- Zeile: 1484
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion getTlsStreamContextDefaults
     *
     * @param mixed $cafile
     * @return mixed|null
     */
```

### `protected getMergedStreamContext($url)`

- Zeile: 1579
- Rueckgabe: `mixed|null`

```php
/**
     * function copied from Composer\Util\StreamContextFactory::initOptions
     *
     * Any changes should be applied there as well, or backported here.
     *
     * @param string $url URL the context is to be used for
     * @return resource Default context
     * @throws \RuntimeException if https proxy required and OpenSSL uninstalled
     */
```

### `public static getSystemCaRootBundlePath()`

- Zeile: 1699
- Rueckgabe: `mixed|null`

```php
/**
    * This method was adapted from Sslurp.
    * https://github.com/EvanDotPro/Sslurp
    *
    * (c) Evan Coury <me@evancoury.com>
    *
    * For the full copyright and license information, please see below:
    *
    * Copyright (c) 2013, Evan Coury
    * All rights reserved.
    *
    * Redistribution and use in source and binary forms, with or without modification,
    * are permitted provided that the following conditions are met:
    *
    *     * Redistributions of source code must retain the above copyright notice,
    *       this list of conditions and the following disclaimer.
    *
    *     * Redistributions in binary form must reproduce the above copyright notice,
    *       this list of conditions and the following disclaimer in the documentation
    *       and/or other materials provided with the distribution.
    *
    * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
    * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
    * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
    * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR
    * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
    * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
    * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
    * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
    * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
    * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
    */
```

### `public static getPackagedCaFile()`

- Zeile: 1767
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion getPackagedCaFile
     *
     * @return mixed|null
     */
```

## `./src/Core/ConfigService.php`

### `public __construct($basePath)`

- Zeile: 16
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $basePath
     * @return mixed|null
     */
```

### `public listSystems()`

- Zeile: 27
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion listSystems
     *
     * @return array
     */
```

### `public getConfig($system)`

- Zeile: 57
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion getConfig
     *
     * @param mixed $system
     * @return string
     */
```

### `public saveConfig($system, $content)`

- Zeile: 74
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion saveConfig
     *
     * @param mixed $system
     * @param mixed $content
     * @return array
     */
```

### `public getHistoryList($system)`

- Zeile: 113
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion getHistoryList
     *
     * @param mixed $system
     * @return array
     */
```

### `public getDiffStatsAgainstCurrent($system)`

- Zeile: 144
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion getDiffStatsAgainstCurrent
     *
     * @param mixed $system
     * @return array
     */
```

### `public diffHistoryFile($system, $historyFile)`

- Zeile: 166
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion diffHistoryFile
     *
     * @param mixed $system
     * @param mixed $historyFile
     * @return array
     */
```

### `private getHistoryContent($system, $historyFile)`

- Zeile: 181
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion getHistoryContent
     *
     * @param mixed $system
     * @param mixed $historyFile
     * @return string
     */
```

### `private buildDiffData($oldText, $newText)`

- Zeile: 199
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion buildDiffData
     *
     * @param mixed $oldText
     * @param mixed $newText
     * @return array
     */
```

### `private diffOps($a, $b)`

- Zeile: 235
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion diffOps
     *
     * @param mixed $a
     * @param mixed $b
     * @return array
     */
```

### `private clientPath($system)`

- Zeile: 288
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion clientPath
     *
     * @param mixed $system
     * @return string
     */
```

### `private historyDir($system)`

- Zeile: 300
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion historyDir
     *
     * @param mixed $system
     * @return string
     */
```

### `private sanitizeSystem($system)`

- Zeile: 312
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion sanitizeSystem
     *
     * @param mixed $system
     * @return string
     */
```

## `./src/Core/DataController.php`

### `public handle()`

- Zeile: 28
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion handle
     *
     * @return void
     */
```

### `private handleGet($select)`

- Zeile: 54
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion handleGet
     *
     * @param mixed $select
     * @return void
     */
```

### `private handlePost($select)`

- Zeile: 98
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion handlePost
     *
     * @param mixed $select
     * @return void
     */
```

### `private getUsers()`

- Zeile: 129
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion getUsers
     *
     * @return array
     */
```

### `private getLogs()`

- Zeile: 148
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion getLogs
     *
     * @return array
     */
```

### `private getDashboardStats()`

- Zeile: 166
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion getDashboardStats
     *
     * @return array
     */
```

### `private handleUserAction()`

- Zeile: 202
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion handleUserAction
     *
     * @return void
     */
```

### `private handleAccountAction()`

- Zeile: 305
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion handleAccountAction
     *
     * @return void
     */
```

### `private handleProfileAction()`

- Zeile: 343
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion handleProfileAction
     *
     * @return void
     */
```

### `private handleConfigGet()`

- Zeile: 370
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion handleConfigGet
     *
     * @return void
     */
```

### `private handleConfigPost()`

- Zeile: 418
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion handleConfigPost
     *
     * @return void
     */
```

### `private handleSettingsGet()`

- Zeile: 469
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion handleSettingsGet
     *
     * @return void
     */
```

### `private handleSettingsPost()`

- Zeile: 511
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion handleSettingsPost
     *
     * @return void
     */
```

### `private requireAdminJson()`

- Zeile: 550
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion requireAdminJson
     *
     * @return void
     */
```

### `private msg($key)`

- Zeile: 563
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion msg
     *
     * @param mixed $key
     * @return string
     */
```

### `private msgf($key, $arg)`

- Zeile: 575
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion msgf
     *
     * @param mixed $key
     * @param mixed $arg
     * @return string
     */
```

### `private assertValidUsername($username)`

- Zeile: 586
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion assertValidUsername
     *
     * @param mixed $username
     * @return void
     */
```

### `private getLoginDiagnostics()`

- Zeile: 598
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion getLoginDiagnostics
     *
     * @return array
     */
```

### `private json($data, $status)`

- Zeile: 711
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion json
     *
     * @param mixed $data
     * @param mixed $status
     * @return void
     */
```

## `./src/Core/Database.php`

### `private __construct($config)`

- Zeile: 35
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $config
     * @return mixed|null
     */
```

### `public static getInstance($config)`

- Zeile: 54
- Rueckgabe: `Database`

```php
/**
     * Kurzbeschreibung Funktion getInstance
     *
     * @param mixed $config
     * @return Database
     */
```

### `public getConnection()`

- Zeile: 70
- Rueckgabe: `PDO`

```php
/**
     * Kurzbeschreibung Funktion getConnection
     *
     * @return PDO
     */
```

## `./src/Core/Debug.php`

### `public static init($env, $logfile, $debug)`

- Zeile: 32
- Rueckgabe: `void`

```php
/**
     * Initialisiert Debug-Umgebung
     */
```

### `public static debug($vars)`

- Zeile: 45
- Rueckgabe: `void`

```php
/**
     * Debug-Ausgabe auf der Seite (Bootstrap Collapse)
     */
```

### `public static log($vars)`

- Zeile: 101
- Rueckgabe: `void`

```php
/**
     * Debug-Ausgabe in Logfile
     */
```

### `public static dd($vars)`

- Zeile: 121
- Rueckgabe: `void`

```php
/**
     * Debug + exit
     */
```

### `public static handleException($e)`

- Zeile: 130
- Rueckgabe: `void`

```php
/**
     * Exception-Handler
     */
```

### `public static handleError($errno, $errstr, $errfile, $errline)`

- Zeile: 156
- Rueckgabe: `bool`

```php
/**
     * Error-Handler
     */
```

### `public static render()`

- Zeile: 172
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion render
     *
     * @return void
     */
```

## `./src/Core/GoRequest.php`

### `public set_value($key, $val)`

- Zeile: 42
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion set_value
     *
     * @param mixed $key
     * @param mixed $val
     * @return void
     */
```

### `public main()`

- Zeile: 52
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion main
     *
     * @return void
     */
```

### `private showLogin()`

- Zeile: 130
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion showLogin
     *
     * @return void
     */
```

### `private checkLogin()`

- Zeile: 140
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion checkLogin
     *
     * @return void
     */
```

### `private baseTemplateData($activeOp)`

- Zeile: 151
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion baseTemplateData
     *
     * @param mixed $activeOp
     * @return array
     */
```

### `private renderPage($title, $content, $activeOp)`

- Zeile: 169
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion renderPage
     *
     * @param mixed $title
     * @param mixed $content
     * @param mixed $activeOp
     * @return void
     */
```

### `private showMain()`

- Zeile: 180
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion showMain
     *
     * @return void
     */
```

### `private showUsers()`

- Zeile: 191
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion showUsers
     *
     * @return void
     */
```

### `private showLogs()`

- Zeile: 202
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion showLogs
     *
     * @return void
     */
```

### `private showConfig()`

- Zeile: 213
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion showConfig
     *
     * @return void
     */
```

### `private showSettings()`

- Zeile: 224
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion showSettings
     *
     * @return void
     */
```

### `private showProfiles()`

- Zeile: 235
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion showProfiles
     *
     * @return void
     */
```

### `private showAccount()`

- Zeile: 246
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion showAccount
     *
     * @return void
     */
```

### `private showLive()`

- Zeile: 257
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion showLive
     *
     * @return void
     */
```

### `private logout()`

- Zeile: 268
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion logout
     *
     * @return void
     */
```

### `private ensureAdminOrForbidden()`

- Zeile: 295
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion ensureAdminOrForbidden
     *
     * @return void
     */
```

### `private downloadProfileZip()`

- Zeile: 309
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion downloadProfileZip
     *
     * @return void
     */
```

### `private showError()`

- Zeile: 335
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion showError
     *
     * @return void
     */
```

### `private setLanguage()`

- Zeile: 351
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion setLanguage
     *
     * @return void
     */
```

### `private enforceAccessPolicy()`

- Zeile: 372
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion enforceAccessPolicy
     *
     * @return void
     */
```

### `private serveLoginAsset()`

- Zeile: 430
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion serveLoginAsset
     *
     * @return void
     */
```

### `private enforceDataAccessPolicy($method)`

- Zeile: 490
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion enforceDataAccessPolicy
     *
     * @param mixed $method
     * @return void
     */
```

### `private verifyStateChangingRequest()`

- Zeile: 508
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion verifyStateChangingRequest
     *
     * @return void
     */
```

### `private isSameOriginRequest()`

- Zeile: 525
- Rueckgabe: `bool`

```php
/**
     * Kurzbeschreibung Funktion isSameOriginRequest
     *
     * @return bool
     */
```

### `private denyRequest($status, $message)`

- Zeile: 554
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion denyRequest
     *
     * @param mixed $status
     * @param mixed $message
     * @return void
     */
```

### `private getRequestCsrfToken()`

- Zeile: 574
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion getRequestCsrfToken
     *
     * @return string
     */
```

## `./src/Core/Lang.php`

### `public static init($lang)`

- Zeile: 29
- Rueckgabe: `void`

```php
/**
     * Initialisiert die Sprache (Session muss vorher gestartet sein)
     */
```

### `public static get($key, $default)`

- Zeile: 55
- Rueckgabe: `string`

```php
/**
     * Einzeltext holen
     */
```

### `public static getAll()`

- Zeile: 63
- Rueckgabe: `array`

```php
/**
     * Komplettes Message-Array (für Templates / Backwards-Compatibility)
     */
```

### `public static getCurrent()`

- Zeile: 71
- Rueckgabe: `string`

```php
/**
     * Aktuelle Sprache
     */
```

### `public static setCurrent($lang)`

- Zeile: 79
- Rueckgabe: `void`

```php
/**
     * Sprache setzen und neu laden
     */
```

### `public static getAvailableLanguages()`

- Zeile: 92
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion getAvailableLanguages
     *
     * @return array
     */
```

### `public static getLanguageMeta($code)`

- Zeile: 122
- Rueckgabe: `array`

```php
/**
     * Liefert Label + Flagge für Sprach-Auswahl.
     */
```

## `./src/Core/LogModel.php`

### `public __construct()`

- Zeile: 33
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @return mixed|null
     */
```

### `public getAllLogs($limit, $offset, $search)`

- Zeile: 46
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion getAllLogs
     *
     * @param mixed $limit
     * @param mixed $offset
     * @param mixed $search
     * @return array
     */
```

### `public countLogs($search)`

- Zeile: 76
- Rueckgabe: `int`

```php
/**
     * Kurzbeschreibung Funktion countLogs
     *
     * @param mixed $search
     * @return int
     */
```

## `./src/Core/LoginController.php`

### `public showLogin()`

- Zeile: 34
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion showLogin
     *
     * @return void
     */
```

### `public handleLogin()`

- Zeile: 52
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion handleLogin
     *
     * @return void
     */
```

### `private redirect($url)`

- Zeile: 145
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion redirect
     *
     * @param mixed $url
     * @return void
     */
```

## `./src/Core/ProfileService.php`

### `public __construct($basePath)`

- Zeile: 15
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $basePath
     * @return mixed|null
     */
```

### `public listSystems()`

- Zeile: 25
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion listSystems
     *
     * @return array
     */
```

### `public buildZip($system)`

- Zeile: 76
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion buildZip
     *
     * @param mixed $system
     * @return string
     */
```

### `public getZipPath($system)`

- Zeile: 118
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion getZipPath
     *
     * @param mixed $system
     * @return string
     */
```

### `private sanitizeSystem($system)`

- Zeile: 136
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion sanitizeSystem
     *
     * @param mixed $system
     * @return string
     */
```

### `private zipPathFor($system)`

- Zeile: 152
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion zipPathFor
     *
     * @param mixed $system
     * @return string
     */
```

### `private isAllowedSystemDir($system, $dir)`

- Zeile: 164
- Rueckgabe: `bool`

```php
/**
     * Kurzbeschreibung Funktion isAllowedSystemDir
     *
     * @param mixed $system
     * @param mixed $dir
     * @return bool
     */
```

## `./src/Core/ServerSettingsService.php`

### `public __construct($cfg)`

- Zeile: 15
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $cfg
     * @return mixed|null
     */
```

### `public getCurrent()`

- Zeile: 35
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion getCurrent
     *
     * @return array
     */
```

### `public save($content)`

- Zeile: 74
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion save
     *
     * @param mixed $content
     * @return array
     */
```

### `public getHistoryList()`

- Zeile: 104
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion getHistoryList
     *
     * @return array
     */
```

### `public diffHistoryFileAgainstCurrent($historyFile, $current)`

- Zeile: 135
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion diffHistoryFileAgainstCurrent
     *
     * @param mixed $historyFile
     * @param mixed $current
     * @return array
     */
```

### `private getDiffStatsAgainstCurrent($current)`

- Zeile: 153
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion getDiffStatsAgainstCurrent
     *
     * @param mixed $current
     * @return array
     */
```

### `private fetchFromUrl($url)`

- Zeile: 174
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion fetchFromUrl
     *
     * @param mixed $url
     * @return string
     */
```

### `private writeCache($content)`

- Zeile: 207
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion writeCache
     *
     * @param mixed $content
     * @return void
     */
```

### `private archiveFile($file)`

- Zeile: 228
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion archiveFile
     *
     * @param mixed $file
     * @return void
     */
```

### `private readSavePath()`

- Zeile: 246
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion readSavePath
     *
     * @return string
     */
```

### `private readFile($path)`

- Zeile: 269
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion readFile
     *
     * @param mixed $path
     * @return string
     */
```

### `private ensureSaveFileExists()`

- Zeile: 283
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion ensureSaveFileExists
     *
     * @return void
     */
```

### `private buildDiffData($oldText, $newText)`

- Zeile: 308
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion buildDiffData
     *
     * @param mixed $oldText
     * @param mixed $newText
     * @return array
     */
```

### `private diffOps($a, $b)`

- Zeile: 344
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion diffOps
     *
     * @param mixed $a
     * @param mixed $b
     * @return array
     */
```

## `./src/Core/Session.php`

### `public __construct($db, $lifetime)`

- Zeile: 48
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $db
     * @param mixed $lifetime
     * @return mixed|null
     */
```

### `public open($savePath, $sessionName)`

- Zeile: 77
- Rueckgabe: `bool`

```php
/**
     * Kurzbeschreibung Funktion open
     *
     * @param mixed $savePath
     * @param mixed $sessionName
     * @return bool
     */
```

### `public close()`

- Zeile: 87
- Rueckgabe: `bool`

```php
/**
     * Kurzbeschreibung Funktion close
     *
     * @return bool
     */
```

### `public read($id)`

- Zeile: 98
- Rueckgabe: `string|false`

```php
/**
     * Kurzbeschreibung Funktion read
     *
     * @param mixed $id
     * @return string|false
     */
```

### `public write($id, $data)`

- Zeile: 126
- Rueckgabe: `bool`

```php
/**
     * Kurzbeschreibung Funktion write
     *
     * @param mixed $id
     * @param mixed $data
     * @return bool
     */
```

### `public destroy($id)`

- Zeile: 158
- Rueckgabe: `bool`

```php
/**
     * Kurzbeschreibung Funktion destroy
     *
     * @param mixed $id
     * @return bool
     */
```

### `public gc($max_lifetime)`

- Zeile: 178
- Rueckgabe: `int|false`

```php
/**
     * Kurzbeschreibung Funktion gc
     *
     * @param mixed $max_lifetime
     * @return int|false
     */
```

### `public static start($db, $lifetime)`

- Zeile: 198
- Rueckgabe: `void`

```php
/**
     * Initialisiert und startet die Session.
     */
```

### `public static setVar($key, $value)`

- Zeile: 227
- Rueckgabe: `void`

```php
/**
     * Setzt eine Session-Variable.
     */
```

### `public static getVar($key, $default)`

- Zeile: 235
- Rueckgabe: `mixed`

```php
/**
     * Holt eine Session-Variable.
     */
```

### `public static removeVar($key)`

- Zeile: 243
- Rueckgabe: `void`

```php
/**
     * Entfernt eine Session-Variable.
     */
```

### `public static isUser()`

- Zeile: 251
- Rueckgabe: `bool`

```php
/**
     * Prüft, ob ein Benutzer angemeldet ist.
     */
```

### `public static isAdmin()`

- Zeile: 259
- Rueckgabe: `bool`

```php
/**
     * Prüft, ob aktueller Benutzer Admin ist.
     */
```

### `public static getUser()`

- Zeile: 281
- Rueckgabe: `?array`

```php
/**
     * Gibt den aktuellen Benutzer zurück.
     */
```

### `public static setUser($user)`

- Zeile: 289
- Rueckgabe: `void`

```php
/**
     * Setzt den angemeldeten Benutzer.
     */
```

### `public static destroyAll()`

- Zeile: 297
- Rueckgabe: `void`

```php
/**
     * Zerstört die komplette Session inkl. Cookie.
     */
```

### `public static getCsrfToken()`

- Zeile: 317
- Rueckgabe: `string`

```php
/**
     * CSRF token for state-changing requests.
     */
```

### `public static verifyCsrfToken($token)`

- Zeile: 332
- Rueckgabe: `bool`

```php
/**
     * Kurzbeschreibung Funktion verifyCsrfToken
     *
     * @param mixed $token
     * @return bool
     */
```

## `./src/Core/UserController.php`

### `public index()`

- Zeile: 34
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion index
     *
     * @return void
     */
```

## `./src/Core/UserModel.php`

### `public __construct()`

- Zeile: 32
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @return mixed|null
     */
```

### `public getAllUsers($limit, $offset, $search)`

- Zeile: 45
- Rueckgabe: `array`

```php
/**
     * Kurzbeschreibung Funktion getAllUsers
     *
     * @param mixed $limit
     * @param mixed $offset
     * @param mixed $search
     * @return array
     */
```

### `public countUsers($search)`

- Zeile: 87
- Rueckgabe: `int`

```php
/**
     * Kurzbeschreibung Funktion countUsers
     *
     * @param mixed $search
     * @return int
     */
```

### `public countOnlineUsers()`

- Zeile: 111
- Rueckgabe: `int`

```php
/**
     * Kurzbeschreibung Funktion countOnlineUsers
     *
     * @return int
     */
```

### `public userExists($username)`

- Zeile: 123
- Rueckgabe: `bool`

```php
/**
     * Kurzbeschreibung Funktion userExists
     *
     * @param mixed $username
     * @return bool
     */
```

### `public createUser($username, $password, $isAdmin)`

- Zeile: 138
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion createUser
     *
     * @param mixed $username
     * @param mixed $password
     * @param mixed $isAdmin
     * @return void
     */
```

### `public setUserEnabled($username, $enabled)`

- Zeile: 162
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion setUserEnabled
     *
     * @param mixed $username
     * @param mixed $enabled
     * @return void
     */
```

### `public setUserRole($username, $isAdmin)`

- Zeile: 178
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion setUserRole
     *
     * @param mixed $username
     * @param mixed $isAdmin
     * @return void
     */
```

### `public setUserPasswordByName($username, $newPassword)`

- Zeile: 195
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion setUserPasswordByName
     *
     * @param mixed $username
     * @param mixed $newPassword
     * @return void
     */
```

### `public setUserPasswordById($uid, $newPassword)`

- Zeile: 212
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion setUserPasswordById
     *
     * @param mixed $uid
     * @param mixed $newPassword
     * @return void
     */
```

### `public setUserLimits($username, $startDate, $endDate)`

- Zeile: 230
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion setUserLimits
     *
     * @param mixed $username
     * @param mixed $startDate
     * @param mixed $endDate
     * @return void
     */
```

### `public deleteUser($username)`

- Zeile: 253
- Rueckgabe: `void`

```php
/**
     * Kurzbeschreibung Funktion deleteUser
     *
     * @param mixed $username
     * @return void
     */
```

### `public verifyPassword($uid, $password)`

- Zeile: 266
- Rueckgabe: `bool`

```php
/**
     * Kurzbeschreibung Funktion verifyPassword
     *
     * @param mixed $uid
     * @param mixed $password
     * @return bool
     */
```

### `private resolveGroupId($isAdmin)`

- Zeile: 284
- Rueckgabe: `int`

```php
/**
     * Kurzbeschreibung Funktion resolveGroupId
     *
     * @param mixed $isAdmin
     * @return int
     */
```

## `./src/Templates/Account.php`

### `public index()`

- Zeile: 12
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
```

## `./src/Templates/Config.php`

### `public index()`

- Zeile: 12
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
```

## `./src/Templates/Dashboard.php`

### `public index()`

- Zeile: 28
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
```

## `./src/Templates/Log.php`

### `public index()`

- Zeile: 28
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
```

## `./src/Templates/Profiles.php`

### `public index()`

- Zeile: 12
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
```

## `./src/Templates/Settings.php`

### `public index()`

- Zeile: 12
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
```

## `./src/Templates/Template.php`

### `public __construct($title, $content, $data)`

- Zeile: 37
- Rueckgabe: `mixed|null`

```php
/**
     * Kurzbeschreibung Funktion __construct
     *
     * @param mixed $title
     * @param mixed $content
     * @param mixed $data
     * @return mixed|null
     */
```

### `private loadPart($name, $vars)`

- Zeile: 47
- Rueckgabe: `string`

```php
/**
     * Lädt eine Layout-Datei aus /src/Layout/
     */
```

### `public render()`

- Zeile: 65
- Rueckgabe: `string`

```php
/**
     * Baut die komplette Seite zusammen
     */
```

## `./src/Templates/User.php`

### `public index()`

- Zeile: 28
- Rueckgabe: `string`

```php
/**
     * Kurzbeschreibung Funktion index
     *
     * @return string
     */
```

