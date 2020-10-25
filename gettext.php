<?php

/**
 * gettext.php
 *
 * Copyright (c) 1999-2005 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * Alternate to the system's built-in gettext.
 * relies on .po files (can't read .mo easily).
 * Uses the session for caching (speed increase)
 *
 * $Id: gettext.php,v 1.1 2006/03/16 02:50:02 mikhail Exp $
 */
global $gettext_php_domain, $gettext_php_dir, $gettext_php_loaded, $gettext_php_translateStrings, $gettext_php_loaded_language, $gettext_php_short_circuit, $gettext_debug;

if (!isset($gettext_php_loaded)) {
    $gettext_php_loaded = false;

    gtsession_register($gettext_php_loaded, 'gettext_php_loaded');
}
if (!isset($gettext_php_domain)) {
    $gettext_php_domain = '';

    gtsession_register($gettext_php_domain, 'gettext_php_domain');
}
if (!isset($gettext_php_dir)) {
    $gettext_php_dir = '';

    gtsession_register($gettext_php_dir, 'gettext_php_dir');
}
if (!isset($gettext_php_translateStrings)) {
    $gettext_php_translateStrings = [];

    gtsession_register($gettext_php_translateStrings, 'gettext_php_translateStrings');
}
if (!isset($gettext_php_loaded_language)) {
    $gettext_php_loaded_language = '';

    gtsession_register($gettext_php_loaded_language, 'gettext_php_loaded_language');
}
if (!isset($gettext_php_short_circuit)) {
    $gettext_php_short_circuit = false;

    gtsession_register($gettext_php_short_circuit, 'gettext_php_short_circuit');
}

function gtsession_register($var, $name)
{
    //    session_start();

    $_SESSION[(string)$name] = $var;

    session_register((string)$name);
}

function gettext_php_load_strings()
{
    global $gettext_debug, $agendax_language, $gettext_php_translateStrings, $gettext_php_domain, $gettext_php_dir, $gettext_php_loaded, $gettext_php_loaded_language, $gettext_php_short_circuit;

    /*
     * $agendax_language gives 'en_US' for English, 'de_DE' for German,
     * etc.
     */

    $gettext_php_translateStrings = [];

    $gettext_php_short_circuit = false;  /* initialization */

    $filename = $gettext_php_dir;

    if ('/' != mb_substr($filename, -1)) {
        $filename .= '/';
    }

    $filename .= $agendax_language . '/LC_MESSAGES/' . $gettext_php_domain . '.po';

    $file = @fopen($filename, 'rb');

    if (false === $file) {
        /* Uh-ho -- we can't load the file.  Just fake it.  :-)
           This is also for English, which doesn't use translations */

        $gettext_php_loaded = true;

        $gettext_php_loaded_language = $agendax_language;

        /* Avoid fuzzy matching when we didn't load strings */

        $gettext_php_short_circuit = true;

        return;
    }

    $key = '';

    $SkipRead = false;

    while (!feof($file)) {
        if (!$SkipRead) {
            $line = trim(fgets($file, 4096));
        } else {
            $SkipRead = false;
        }

        if (preg_match('^msgid "(.*)"$', $line, $match)) {
            if ('' == $match[1]) {
                /*
                 * Potential multi-line
                 * msgid ""
                 * "string string "
                 * "string string"
                 */

                $key = '';

                $line = trim(fgets($file, 4096));

                while (preg_match('^[ ]*"(.*)"[ ]*$', $line, $match)) {
                    $key .= $match[1];

                    $line = trim(fgets($file, 4096));
                }

                $SkipRead = true;
            } else {
                /* msgid "string string" */

                $key = $match[1];
            }
        } elseif (preg_match('^msgstr "(.*)"$', $line, $match)) {
            if ('' == $match[1]) {
                /*
                 * Potential multi-line
                 * msgstr ""
                 * "string string "
                 * "string string"
                 */

                $gettext_php_translateStrings[$key] = '';

                $line = trim(fgets($file, 4096));

                while (preg_match('^[ ]*"(.*)"[ ]*$', $line, $match)) {
                    $gettext_php_translateStrings[$key] .= $match[1];

                    $line = trim(fgets($file, 4096));
                }

                $SkipRead = true;
            } else {
                /* msgstr "string string" */

                $gettext_php_translateStrings[$key] = $match[1];
            }

            $gettext_php_translateStrings[$key] = stripslashes($gettext_php_translateStrings[$key]);

            /* If there is no translation, just use the untranslated string */

            if ('' == $gettext_php_translateStrings[$key]) {
                $gettext_php_translateStrings[$key] = $key;
            }

            $key = '';
        }
    }

    fclose($file);

    $gettext_php_loaded = true;

    $gettext_php_loaded_language = $agendax_language;

    //register the session variable so we don't need to reload again

    if (!$gettext_debug) {
        gtsession_register($gettext_php_loaded, 'gettext_php_loaded');

        gtsession_register($gettext_php_domain, 'gettext_php_domain');

        gtsession_register($gettext_php_dir, 'gettext_php_dir');

        gtsession_register($gettext_php_translateStrings, 'gettext_php_translateStrings');

        gtsession_register($gettext_php_loaded_language, 'gettext_php_loaded_language');

        gtsession_register($gettext_php_short_circuit, 'gettext_php_short_circuit');
    }
}

function _($str)
{
    global $gettext_php_loaded, $gettext_php_translateStrings, $agendax_language, $gettext_php_loaded_language, $gettext_php_short_circuit;

    // for test should be removed , if used the next trois lignes should be commented

    //      gettext_php_load_strings();

    if (!$gettext_php_loaded
        || $gettext_php_loaded_language != $agendax_language) {
        gettext_php_load_strings();
    }

    /* Try finding the exact string */

    if (isset($gettext_php_translateStrings[$str])) {
        return $gettext_php_translateStrings[$str];
    }

    /* See if we should short-circuit */

    if ($gettext_php_short_circuit) {
        $gettext_php_translateStrings[$str] = $str;

        return $str;
    }

    /* Look for a string that is very close to the one we want
       Very computationally expensive */

    $oldPercent = 0;

    $oldStr = '';

    $newPercent = 0;

    foreach ($gettext_php_translateStrings as $k => $v) {
        similar_text($str, $k, $newPercent);

        if ($newPercent > $oldPercent) {
            $oldStr = $v;

            $oldPercent = $newPercent;
        }
    }

    /* Require 80% match or better
       Adjust to suit your needs */

    if ($oldPercent > 80) {
        /* Remember this so we don't need to search again */

        $gettext_php_translateStrings[$str] = $oldStr;

        return $oldStr;
    }

    /* Remember this so we don't need to search again */

    $gettext_php_translateStrings[$str] = $str;

    return $str;
}

function bindtextdomain($name, $dir)
{
    global $gettext_php_domain, $gettext_php_dir, $gettext_php_loaded;

    if ($gettext_php_domain != $name) {
        $gettext_php_domain = $name;

        $gettext_php_loaded = false;
    }

    if ($gettext_php_dir != $dir) {
        $gettext_php_dir = $dir;

        $gettext_php_loaded = false;
    }

    return $dir;
}

function textdomain($name = false)
{
    global $gettext_php_domain, $gettext_php_loaded;

    if (false !== $name && $gettext_php_domain != $name) {
        $gettext_php_domain = $name;

        $gettext_php_loaded = false;
    }

    return $gettext_php_domain;
}
