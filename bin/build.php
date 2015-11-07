<?php

define("__DEFAULT_LOCALE__", locale_get_default());

/**
 * Check if a command exists.
 * 
 * @param string $command
 * @return bool
 */
function command_exists($command)
{
    $whereIsCommand = (PHP_OS == 'WINNT') ? 'where' : 'which';

    $process = proc_open(
        "$whereIsCommand $command",
        array(
            0 => array("pipe", "r"), //STDIN
            1 => array("pipe", "w"), //STDOUT
            2 => array("pipe", "w"), //STDERR
        ),
        $pipes
    );

    if ($process !== false) {
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);

        return $stdout != '';
    }

    return false;
}

/**
 * Builds an empty .po file
 * 
 * @param string $filename Full path to the .po file
 * @param string $languageCode The language code
 * @param array $languageList Array of languages
 */
function create_po_file($filename, $languageCode)
{
    global $_LANGUAGES;
    
    $translation_count = 0;
    
    $handle = fopen($filename, 'w');
    fwrite($handle, "# Translation of ISO 639 (language names) to LANGUAGE\n");
    fwrite($handle, "#\n");
    fwrite($handle, "# This file has been auto-generated from \n");
    fwrite($handle, "# http://www.loc.gov/standards/iso639-2/ISO-639-2_utf-8.txt\n");
    fwrite($handle, "#\n");
    fwrite($handle, "msgid \"\"\n");
    fwrite($handle, "msgstr \"\"\n");
    fwrite($handle, "\"Project-Id-Version: iso_639 " . date('YmdHi') . "\\n\"\n");
    fwrite($handle, "\"Last-Translator: Automatically generated\\n\"\n");
    fwrite($handle, "\"Language-Team: none\\n\"\n");
    fwrite($handle, "\"MIME-Version: 1.0\\n\"\n");
    fwrite($handle, "\"Content-Type: text/plain; charset=UTF-8\\n\"\n");
    fwrite($handle, "\"Content-Transfer-Encoding: 8bit\\n\"\n");
    fwrite($handle, "\"POT-Creation-Date: " . date('Y') . "-" . date('m') . "-" . date('d') . " " . date('H'). ":" . date('i') .  date('O') . "\\n\"\n");
    //fwrite($handle, "\"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n\"\n");
    fwrite($handle, "\"POT-Revision-Date: " . date('Y') . "-" . date('m') . "-" . date('d') . " " . date('H'). ":" . date('i') .  date('O') . "\\n\"\n");
    fwrite($handle, "\"Language: " . $languageCode . "\\n\"\n");
    // I use poedit
    fwrite($handle, "\"X-Poedit-SourceCharset: UTF-8\\n\"\n");
    fwrite($handle, "\n");
            
    foreach ($_LANGUAGES as $language) {
        if (!empty($language['iso_639_1_code'])) {
            fwrite($handle, "#. name for " . $language['iso_639_2T_code'] . ", " . $language['iso_639_1_code'] . "\n");
        } else {
            fwrite($handle, "#. name for " . $language['iso_639_2T_code'] . "\n");
        }
        fwrite($handle, "msgid \"" . $language['english_names'] . "\"\n");

        $translatedName = null;
        if (!empty($language['iso_639_1_code'])) {
            $translatedName = translate_locale($language['iso_639_1_code'], $languageCode);
        }

        if (empty($translatedName)) {
            if (!empty($language['iso_639_2T_code'])) {
                $translatedName = translate_locale($language['iso_639_2T_code'], $languageCode);
                if (!empty($language['iso_639_1_code']) && (strcasecmp($translatedName, $language['iso_639_1_code']) === 0)) {
                    $translatedName = null;
                }
            }
        }
        
        if (empty($translatedName)) {
            if (!empty($language['iso_639_2B_code'])) {
                $translatedName = translate_locale($language['iso_639_2B_code'], $languageCode);
                if (!empty($language['iso_639_1_code']) && (strcasecmp($translatedName, $language['iso_639_1_code']) === 0)) {
                    $translatedName = null;
                }
            }
        }

        //if (empty($translatedName) && !empty($language['iso_639_1_code'])) {
        //    $translatedName = gettext_translate($language['english_names'], $language['iso_639_1_code']);
        //}

        if (empty($translatedName)) {
            fwrite($handle, "msgstr \"\"\n\n");
        } else {
            fwrite($handle, "msgstr \"" . $translatedName . "\"\n\n");
            $translation_count = $translation_count + 1;
        }
    }

    fclose($handle);

    // If there are no translations delete the file
    if ($translation_count === 0) {
        unlink($filename);
    }
}

function download_file($url, $file)
{
    if (function_exists('curl_version')) {
        //File to save the contents to
        $fp = fopen ($file, 'w+');
 
        //Here is the file we are downloading, replace spaces with %20
        $ch = curl_init(str_replace(" ","%20",$url));
 
        //curl_setopt($ch, CURLOPT_TIMEOUT, 50);
 
        //give curl the file pointer so that it can write to it
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
        curl_setopt($ch, CURLOPT_HEADER, false);
 
        // Disable PEER SSL Verification: If you are not running with SSL or if you don't have valid SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        // Disable HOST (the site you are sending request to) SSL Verification,
        // if Host can have certificate which is nvalid / expired / not signed by authorized CA.
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        $data = curl_exec($ch);//get curl response
 
        //done
        curl_close($ch);
        fclose($fp);
    } else {
        die("Error: cURL not installed!\n");
    }
}

function gettext_available()
{
    if (defined('__GETTEXT_ENABLED__')) {
        return __GETTEXT_ENABLED__;
    }

    if (!command_exists('msgfmt')) {
        if (PHP_OS == 'WINNT') {
            // Make life easier for Windows users
            if (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'gettext' . DIRECTORY_SEPARATOR . 'bin')) {
                echo "Installing gettext binaries for Windows.\n";
                download_file("http://ftp.gnu.org/pub/gnu/gettext/gettext-runtime-0.13.1.bin.woe32.zip", __DIR__ . DIRECTORY_SEPARATOR . "gettext-runtime-0.13.1.bin.woe32.zip");
                download_file("http://ftp.gnu.org/pub/gnu/gettext/gettext-tools-0.13.1.bin.woe32.zip", __DIR__ . DIRECTORY_SEPARATOR . "gettext-tools-0.13.1.bin.woe32.zip");
                download_file("http://ftp.gnu.org/pub/gnu/libiconv/libiconv-1.9.1.bin.woe32.zip", __DIR__ . DIRECTORY_SEPARATOR . "libiconv-1.9.1.bin.woe32.zip");

                unzip_file(__DIR__ . DIRECTORY_SEPARATOR . "gettext-runtime-0.13.1.bin.woe32.zip", __DIR__ . DIRECTORY_SEPARATOR . 'gettext');
                unzip_file(__DIR__ . DIRECTORY_SEPARATOR . "gettext-tools-0.13.1.bin.woe32.zip", __DIR__ . DIRECTORY_SEPARATOR . 'gettext');
                unzip_file(__DIR__ . DIRECTORY_SEPARATOR . "libiconv-1.9.1.bin.woe32.zip", __DIR__ . DIRECTORY_SEPARATOR . 'gettext');

                unlink(__DIR__ . DIRECTORY_SEPARATOR . "gettext-runtime-0.13.1.bin.woe32.zip");
                unlink(__DIR__ . DIRECTORY_SEPARATOR . "gettext-tools-0.13.1.bin.woe32.zip");
                unlink(__DIR__ . DIRECTORY_SEPARATOR . "libiconv-1.9.1.bin.woe32.zip");
            }

            if (is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'gettext' . DIRECTORY_SEPARATOR . 'bin')) {
                putenv('PATH=' . getenv('PATH') . ';' . __DIR__ . DIRECTORY_SEPARATOR . 'gettext' . DIRECTORY_SEPARATOR . 'bin');
                if (command_exists('msgfmt')) {
                    define('__GETTEXT_ENABLED__', true);
                    return true;
                }
            }
        }
    } else {
        define('__GETTEXT_ENABLED__', true);
        return true;
    }

    define('__GETTEXT_ENABLED__', false);
    return false;
}

function gettext_translate($language, $locale)
{
    if (false === gettext_available()) {
        return null;
    }

    if (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'Locale' . DIRECTORY_SEPARATOR . $locale . DIRECTORY_SEPARATOR . 'LC_MESSAGES' . DIRECTORY_SEPARATOR . 'messages.mo')) {
        return null;
    }
    
    putenv('TEXTDOMAINDIR=' . __DIR__ . DIRECTORY_SEPARATOR . 'Locale');
    putenv('LANG=' . $locale );
    putenv('LC_ALL=' . $locale );

    $languageName = shell_exec('gettext --domain messages "' . $language . '"');
    $languageName = trim($languageName);

    if (strcasecmp($languageName, $language) === 0) {
        return null;
    }

    return $languageName;
}

/**
 * Translate a language using INTL extension
 * 
 * @param string $locale
 * @param string $language
 */
function translate_locale($locale, $language)
{
    if (is_null($language)) {
        return null;
    } elseif (is_array($language)) {
        foreach ($language as $lang) {
            $out = translate_locale($locale, $lang);
            if (!empty($out)) {
                return $out;
            }
        }
    } elseif (is_string($language)) {
        $languageName = locale_get_display_language($locale, $language);
        $languageName = trim($languageName);
        if (empty($languageName) || (strcasecmp($languageName, $locale) === 0)) {
            return null;
        }

        if (strcasecmp($language, 'en') !== 0) {
            $englishName = locale_get_display_language($locale, 'en');
            if (empty($englishName) || (strcasecmp($englishName, $locale) === 0) || (strcasecmp($englishName, $languageName) === 0)) {
                return null;
            }
        }
        
        $defaultLanguage = locale_get_primary_language( __DEFAULT_LOCALE__ );
        if (strcasecmp($language, $defaultLanguage) !== 0) {
            $defaultName = locale_get_display_language($locale, __DEFAULT_LOCALE__);
            if (empty($defaultName) || (strcasecmp($defaultName, $locale) === 0) || (strcasecmp($defaultName, $languageName) === 0)) {
                return null;
            }
        }
        
        $defaultLocale   = locale_get_default();
        $defaultLanguage = locale_get_primary_language( $defaultLocale );
        if (strcasecmp($language, $defaultLanguage) !== 0) {
            $defaultName = locale_get_display_language($locale, $defaultLocale);
            if (empty($defaultName) || (strcasecmp($defaultName, $locale) === 0) || (strcasecmp($defaultName, $languageName) === 0)) {
                return null;
            }
        }

        return $languageName;
    } else {
        echo "Error: \$language must be a string or array.\n";
        exit(1);
    }

    return null;
}

function translate_with_pkg_isocodes()
{
    if (false === gettext_available()) {
        return false;
    }

    if (command_exists('git')) {
        if (is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'iso-codes')) {
            chdir(__DIR__ . DIRECTORY_SEPARATOR . 'iso-codes');
            $result = shell_exec("git pull");
            chdir( __DIR__ );
        } else {
            $result = shell_exec("git clone git://anonscm.debian.org/pkg-isocodes/iso-codes.git --branch master --single-branch");
        }
    }

    if (!is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'iso-codes' . DIRECTORY_SEPARATOR . 'iso_639')) {
        return false;
    }

    // Create the folder structure for gettext
    if (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'Locale')) {
        mkdir(__DIR__ . DIRECTORY_SEPARATOR . 'Locale');
    }

    if (!is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'Locale')) {
        return false;
    }

    $dir_handle = opendir(__DIR__ . DIRECTORY_SEPARATOR . 'iso-codes' . DIRECTORY_SEPARATOR . 'iso_639');
    while(false !== ( $file = readdir($dir_handle)) ) {
        $source_file = __DIR__ . DIRECTORY_SEPARATOR . 'iso-codes' . DIRECTORY_SEPARATOR . 'iso_639' . DIRECTORY_SEPARATOR . $file;
        $pathinfo = pathinfo($source_file);
        if (isset($pathinfo['extension'])) {
            if (strcasecmp($pathinfo['extension'], 'po') === 0) {
                if (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'Locale' . DIRECTORY_SEPARATOR . $pathinfo['filename'])) {
                    mkdir(__DIR__ . DIRECTORY_SEPARATOR . 'Locale' . DIRECTORY_SEPARATOR . $pathinfo['filename']);
                }

                if (is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'Locale' . DIRECTORY_SEPARATOR . $pathinfo['filename'])) {
                    if (!file_exists(__DIR__ . DIRECTORY_SEPARATOR . 'Locale' . DIRECTORY_SEPARATOR . $pathinfo['filename'] . DIRECTORY_SEPARATOR . 'LC_MESSAGES')) {
                        mkdir(__DIR__ . DIRECTORY_SEPARATOR . 'Locale' . DIRECTORY_SEPARATOR . $pathinfo['filename'] . DIRECTORY_SEPARATOR . 'LC_MESSAGES');
                    }

                    if (is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'Locale' . DIRECTORY_SEPARATOR . $pathinfo['filename'] . DIRECTORY_SEPARATOR . 'LC_MESSAGES')) {
                        shell_exec('msgfmt "' . $source_file . '" -o "' . __DIR__ . DIRECTORY_SEPARATOR . 'Locale' . DIRECTORY_SEPARATOR . $pathinfo['filename'] . DIRECTORY_SEPARATOR . 'LC_MESSAGES' . DIRECTORY_SEPARATOR . 'messages.mo"');
                    }
                }
            }
        }
    }
    closedir($dir_handle);
}

function unzip_file($filename, $out)
{
    $zip = new ZipArchive();
    if ($zip->open($filename) !== TRUE) {
        echo("Error: Cannot open <$filename>\n");
        exit(1);
    }
    $zip->extractTo($out);
    $zip->close();
}

/**
 * Script entry point
 */
$_LANGUAGES = array();

echo "Generating iso-639 data.\n\n";
echo "This script takes a long time to complete.\n";
echo "Please wait...\n\n";

// Set the default locale to english
if (false === (locale_set_default('en_US'))) {
    if (false === (locale_set_default('en_US'))) {
        echo "Error: Unable to set the default locale to English.\n";
        exit(1);
    }
}

// Download the ISO-639 file in UTF-8 from
// http://www.loc.gov/standards/iso639-2/ISO-639-2_utf-8.txt
$lines = file_get_contents("http://www.loc.gov/standards/iso639-2/ISO-639-2_utf-8.txt");
// Remove UTF-8 BOM (0xEF,0xBB,0xBF)
$lines = substr($lines, 3);

// Split into single lines
$lines = explode("\n", $lines);

// Allow translations from package isocodes
translate_with_pkg_isocodes();

// Build the array from the file contents
echo "Gathering ISO 639 data\n";
foreach ($lines as $line) {
    $parts = explode("|" , trim($line));
    $language = array(
        'iso_639_2B_code' => !empty($parts[0]) ? $parts[0] : null,
        'iso_639_2T_code' => !empty($parts[1]) ? $parts[1] : (!empty($parts[0]) ? $parts[0] : null),
        'iso_639_1_code'  => !empty($parts[2]) ? $parts[2] : null,
        'english_names'   => !empty($parts[3]) ? $parts[3] : null,
        'french_names'    => !empty($parts[4]) ? $parts[4] : null,
        'native_names'    => null
    );
    
    if (!empty($language['iso_639_1_code'])) {
        $englishName = locale_get_display_language($language['iso_639_1_code'], 'en');
        if (!empty($englishName) && (strcasecmp($language['iso_639_1_code'], $englishName))) {
            $language['english_names'] = $englishName;
        }

        $language['native_names'] = translate_locale($language['iso_639_1_code'], array($language['iso_639_1_code'], $language['iso_639_2T_code'], $language['iso_639_2B_code']));
    } elseif (!empty($language['iso_639_2T_code'])) {
        $englishName = locale_get_display_language($language['iso_639_2T_code'], 'en');
        if (!empty($englishName) && (strcasecmp($language['iso_639_2T_code'], $englishName))) {
            $language['english_names'] = $englishName;
        }

        $language['native_names'] = translate_locale($language['iso_639_2T_code'], array($language['iso_639_2T_code'], $language['iso_639_2B_code']));
    }
    
    //if (empty($language['native_names']) && !empty($language['iso_639_1_code'])) {
    //    $language['native_names'] = gettext_translate($language['english_names'], $language['iso_639_1_code']);
    //}
    
    $_LANGUAGES[] = $language;
}

// Free some memory
unset($lines);

/**
 * Buid a .pot file for gettext
 */
echo "Generating .pot file.\n";
$GETTEXT_POT_FILE = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . 'iso_639.pot';
$handle = fopen($GETTEXT_POT_FILE, 'w');
fwrite($handle, "# Translation of ISO 639 (language names) to LANGUAGE\n");
fwrite($handle, "#\n");
fwrite($handle, "# This file has been auto-generated from INTL module and\n");
fwrite($handle, "# http://www.loc.gov/standards/iso639-2/ISO-639-2_utf-8.txt\n");
fwrite($handle, "#\n");
fwrite($handle, "msgid \"\"\n");
fwrite($handle, "msgstr \"\"\n");
fwrite($handle, "\"Project-Id-Version: iso_639 " . date('YmdHi') . "\\n\"\n");
fwrite($handle, "\"Last-Translator: Automatically generated\\n\"\n");
fwrite($handle, "\"Language-Team: none\\n\"\n");
fwrite($handle, "\"POT-Creation-Date: " . date('Y') . "-" . date('m') . "-" . date('d') . " " . date('H'). ":" . date('i') .  date('O') . "\\n\"\n");
fwrite($handle, "\"PO-Revision-Date: YEAR-MO-DA HO:MI+ZONE\\n\"\n");
fwrite($handle, "\"MIME-Version: 1.0\\n\"\n");
fwrite($handle, "\"Content-Type: text/plain; charset=UTF-8\\n\"\n");
fwrite($handle, "\"Content-Transfer-Encoding: 8bit\\n\"\n");
fwrite($handle, "\n");

foreach ($_LANGUAGES as $language) {
    if (!empty($language['iso_639_1_code'])) {
        fwrite($handle, "#. name for " . $language['iso_639_2T_code'] . ", " . $language['iso_639_1_code'] . "\n");
    } else {
        fwrite($handle, "#. name for " . $language['iso_639_2T_code'] . "\n");
    }
    fwrite($handle, "msgid \"" . $language['english_names'] . "\"\n");
    fwrite($handle, "msgstr \"\"\n\n");
}

fclose($handle);

/**
 * Build the .po files
 */
echo "Generating .po files.\n";
foreach ($_LANGUAGES as $language) {
    if (isset($language['iso_639_1_code'])) {
        echo ".";
        create_po_file(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $language['iso_639_1_code'] . '.po', $language['iso_639_1_code']);
    }

    if (isset($language['iso_639_2T_code'])) {
        echo ".";
        create_po_file(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $language['iso_639_2T_code'] . '.po', $language['iso_639_2T_code']);
    }

    if (isset($language['iso_639_2B_code'])) {
        if (!file_exists(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $language['iso_639_2B_code'] . '.po')) {
            echo ".";
            create_po_file(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $language['iso_639_2B_code'] . '.po', $language['iso_639_2B_code']);
        }
    }
}
echo " done.\n";
/**
 * Compile .po file to .mo
 */
echo "Compiling .po files.\n";
if (gettext_available()) {
    $dir_handle = opendir(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'language');
    while(false !== ( $file = readdir($dir_handle)) ) {
        $source_file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'language' . DIRECTORY_SEPARATOR . $file;
        $pathinfo = pathinfo($source_file);
        if (isset($pathinfo['extension'])) {
            if (strcasecmp($pathinfo['extension'], 'po') === 0) {
                shell_exec("msgfmt \"" . $source_file . "\" -o \"" . substr($source_file, 0, strlen($source_file) - 2) . "mo\"");
            }
        }
    }
    closedir($dir_handle);
}

/**
 * Write the PHP array data.
 */
echo "Building languages array.\n";
$handle = fopen(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'iso_639.php', 'w');
fwrite($handle, "<?php\n");
fwrite($handle, "/**\n");
fwrite($handle, " * This file has been auto-generated from \n");
fwrite($handle, " * http://www.loc.gov/standards/iso639-2/ISO-639-2_utf-8.txt\n");
fwrite($handle, " */\n");
fwrite($handle, "return array(\n");
foreach ($_LANGUAGES as $language) {
    fwrite($handle, "    array(\n");
    fwrite($handle, "        'alpha_2'     => " . (isset($language['iso_639_1_code']) ? "'" . $language['iso_639_1_code'] . "'" : "null") . ",\n");
    fwrite($handle, "        'alpha_3b'    => " . (isset($language['iso_639_2B_code']) ? "'" . $language['iso_639_2B_code'] . "'" : "null") . ",\n");
    fwrite($handle, "        'alpha_3t'    => " . (isset($language['iso_639_2T_code']) ? "'" . $language['iso_639_2T_code'] . "'" : "null") . ",\n");
    fwrite($handle, "        'name'        => " . (isset($language['english_names']) ? "'" . str_replace("'", "\'", $language['english_names']) . "'" : "null") . ",\n");
    fwrite($handle, "        'native_name' => " . (isset($language['native_names']) ? "'" . str_replace("'", "\'", $language['native_names']) . "'" : "null") . ",\n");
    fwrite($handle, "    ),\n");
}
fwrite($handle, ");");
fclose($handle);

/**
 * Generate the dump for MySQL
 */
$handle = fopen(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'iso_639_mysql.sql', 'w');

fwrite($handle, "CREATE TABLE IF NOT EXISTS `iso_639` (\n");
fwrite($handle, "  `alpha_2` char(2) DEFAULT NULL,\n");
fwrite($handle, "  `alpha_3b` char(3) DEFAULT NULL,\n");
fwrite($handle, "  `alpha_3t` char(3) DEFAULT NULL,\n");
fwrite($handle, "  `name` varchar(255) NOT NULL,\n");
fwrite($handle, "  KEY `IDX_ALPHA_2` (`alpha_2`),\n");
fwrite($handle, "  KEY `IDX_ALPHA_3B` (`alpha_3b`),\n");
fwrite($handle, "  KEY `IDX_ALPHA_3T` (`alpha_3t`)\n");
fwrite($handle, ") ENGINE=InnoDB DEFAULT CHARSET=utf8;\n\n");

fwrite($handle, "INSERT INTO `iso_639` (`alpha_2`, `alpha_3b`, `alpha_3t`, `name`, `native_name`) VALUES\n");

$is_first_record = true;
foreach ($_LANGUAGES as $language) {
    if ($is_first_record) {
        fwrite($handle, "\n(");
        $is_first_record = false;
    } else {
        fwrite($handle, ",\n(");
    }
    fwrite($handle, (isset($language['iso_639_1_code']) ? "'" . $language['iso_639_1_code'] . "'" : "NULL") . ", ");
    fwrite($handle, (isset($language['iso_639_2B_code']) ? "'" . $language['iso_639_2B_code'] . "'" : "null") . ", ");
    fwrite($handle, (isset($language['iso_639_2T_code']) ? "'" . $language['iso_639_2T_code'] . "'" : "null") . ", ");
    fwrite($handle, (isset($language['english_names']) ? "'" . str_replace("'", "\'", $language['english_names']) . "'" : "null") . ", ");
    fwrite($handle, (isset($language['native_names']) ? "'" . str_replace("'", "\'", $language['native_names']) . "'" : "null") . ")");
}
fwrite($handle, ";");
fclose($handle);