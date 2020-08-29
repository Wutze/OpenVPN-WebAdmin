# Modul Clientload

## Enable Modul

Add in your /include/config.php this entry

````php
define('clientload',TRUE);
````

## find the entrys

You can find the records for the client download and the extended documentation for your users in the folder /data/clientload/.

If you want to offer your own translation with your own links to the client download, you have to create a new file with the following criteria:

````sh
en_EN.clientload.content.php
````

The automatic language selection of the translations available in the system, here marked as de_DE, is essential. (You can find all translations in the folder /include/lang/)

If you have not created your own new file, the file en_EN.clientload.content.example.php will be loaded automatically. You should not edit this file, because it could be overwritten by a possible update. Your self created files will be saved before and copied back after the update.

## Note

You should only use Markdown, as shown in the example file.

## Using the variables

You will find two variables for each system, of which the first entry is needed, the second can be used optionally.

Example:

````php
$out_osx = <<<TEXTOSX
# Main Information
TEXTOSX;
````

The $out_osx_ext entry shown here is only displayed on the website if there is content in it. If the variable remains empty, none of it will appear on the website. You should not delete the variable, just leave it empty.

````php
$out_osx_ext = <<<TEXTOSX

TEXTOSX;
````
