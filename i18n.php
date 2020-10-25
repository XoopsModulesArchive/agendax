<?php

/**
 * i18n.php
 *
 * Copyright (c) 1999-2005 The SquirrelMail Project Team
 * Licensed under the GNU GPL. For full terms see the file COPYING.
 *
 * This file contains variuos functions that are needed to do
 * internationalization of Agenda-X.
 *
 * Internally the output character set is used. Other characters are
 * encoded using Unicode entities according to HTML 4.0.
 */

/**
 * Default language
 *   This is the default language. It is used as a last resort
 *   if Agenda-X can't figure out which language to display.
 *   Use the two-letter code.
 */

//global $agendax_default_language;
$agendax_default_language = 'en_US';

/*
 * Set up the language to be output
 * if $do_search is true, then scan the browser information
 * for a possible language that we know
 */
function set_up_language($sm_language = 'en_US', $do_search = true)
{
    static $SetupAlready = 0;

    global $use_gettext, $languages, $agendax_language, $agendax_default_language, $sm_notAlias, $agendax_path;

    if ($SetupAlready) {
        return;
    }

    $SetupAlready = true;

    //  We use the same language as XOOPS

    if ($do_search) {
        //should be such as en_US, zh_CN, zh_TW, .... but we will accomodate with the legacy

        //many Xoops version's language global.php file defines _LANCODE as two letter language code as 'en', 'fr', 'tw'

        $xps_lang_code = mb_strtolower(_LANGCODE);

        if ('tw' == $xps_lang_code) {
            $sm_language = 'zh_TW';
        } elseif ('cn' == $xps_lang_code) {
            $sm_language = 'zh_CN';
        } elseif ('ja' == $xps_lang_code) {
            $sm_language = 'ja_JP';
        } elseif ('en' == $xps_lang_code) {
            $sm_language = 'en_US';
        } elseif (2 == mb_strlen($xps_lang_code)) {
            $sm_language = $xps_lang_code . '_' . mb_strtoupper($xps_lang_code);
        } else {
            $sm_language = mb_strtolower(mb_substr(_LANGCODE, 0, 2)) . '_' . mb_strtoupper(mb_substr(_LANGCODE, 3, 2)); //_LANGCODE defined in Xoops is as en_us, zh_tw, etc.
        }
    }

    //    if (!$sm_language && isset($agendax_default_language)) {

    //        $agendax_language = $agendax_default_language;

    //        $sm_language = $agendax_default_language;

    //    }

    $sm_notAlias = $sm_language;

    //    while (isset($languages[$sm_notAlias]['ALIAS'])) {

    //        $sm_notAlias = $languages[$sm_notAlias]['ALIAS'];

    //    }

    if (isset($sm_language) && $use_gettext) {
        bindtextdomain('agendax', $agendax_path . '/locale/');

        textdomain('agendax');

        if (!ini_get('safe_mode')
            && getenv('LC_ALL') != $sm_notAlias) {
            putenv("LC_ALL=$sm_notAlias");

            putenv("LANG=$sm_notAlias");

            putenv("LANGUAGE=$sm_notAlias");
        }

        setlocale(LC_ALL, $sm_notAlias);

        $agendax_language = $sm_notAlias;

        //        if ($agendax_language == 'ja_JP' && function_exists('mb_detect_encoding') ) {
        //            header ('Content-Type: text/html; charset=EUC-JP');
        //            if (!function_exists('mb_internal_encoding')) {
        //                echo _("You need to have php4 installed with the multibyte string function enabled (using configure option --enable-mbstring).");
        //            }
        //            if (function_exists('mb_language')) {
        //                mb_language('Japanese');
        //            }
        //            mb_internal_encoding('EUC-JP');
        //            mb_http_output('pass');
        //        } else {
        //        header( 'Content-Type: text/html; charset=' . $languages[$sm_notAlias]['CHARSET'] );
    }
}

function set_my_charset()
{
    /*
     * There can be a $default_charset setting in the
     * config.php file, but the user may have a different language
     * selected for a user interface. This function checks the
     * language selected by the user and tags the outgoing messages
     * with the appropriate charset corresponding to the language
     * selection. This is "more right" (tm), than just stamping the
     * message blindly with the system-wide $default_charset.
     */ global $data_dir, $username, $default_charset, $languages, $agendax_default_language;

    $my_language = getPref($data_dir, $username, 'language');

    if (!$my_language) {
        $my_language = $agendax_default_language;
    }

    while (isset($languages[$my_language]['ALIAS'])) {
        $my_language = $languages[$my_language]['ALIAS'];
    }

    $my_charset = $languages[$my_language]['CHARSET'];

    if ($my_charset) {
        $default_charset = $my_charset;
    }
}

/* ------------------------------ main --------------------------- */

global $agendax_language, $languages, $use_gettext, $agendax_path;

if (!isset($agendax_language)) {
    $agendax_language = '';
}

/* This array specifies the available languages. */
/* this array will be used in furture version */

// The glibc locale is ca_ES.
/*
$languages['ca_ES']['NAME']    = 'Catalan';
$languages['ca_ES']['CHARSET'] = 'iso-8859-1';
$languages['ca']['ALIAS'] = 'ca_ES';

$languages['cs_CZ']['NAME']    = 'Czech';
$languages['cs_CZ']['CHARSET'] = 'iso-8859-2';
$languages['cs']['ALIAS']      = 'cs_CZ';

// Danish locale is da_DK.

$languages['da_DK']['NAME']    = 'Danish';
$languages['da_DK']['CHARSET'] = 'iso-8859-1';
$languages['da']['ALIAS'] = 'da_DK';

$languages['de_DE']['NAME']    = 'Deutsch';
$languages['de_DE']['CHARSET'] = 'iso-8859-1';
$languages['de']['ALIAS'] = 'de_DE';

// There is no en_EN! There is en_US, en_BR, en_AU, and so forth,
// but who cares about !US, right? Right? :)

$languages['el_GR']['NAME']    = 'Greek';
$languages['el_GR']['CHARSET'] = 'iso-8859-7';
$languages['el']['ALIAS'] = 'el_GR';

$languages['en_US']['NAME']    = 'English';
$languages['en_US']['CHARSET'] = 'iso-8859-1';
$languages['en']['ALIAS'] = 'en_US';

$languages['es_ES']['NAME']    = 'Spanish';
$languages['es_ES']['CHARSET'] = 'iso-8859-1';
$languages['es']['ALIAS'] = 'es_ES';

$languages['et_EE']['NAME']    = 'Estonian';
$languages['et_EE']['CHARSET'] = 'iso-8859-15';
$languages['et']['ALIAS'] = 'et_EE';

$languages['fi_FI']['NAME']    = 'Finnish';
$languages['fi_FI']['CHARSET'] = 'iso-8859-1';
$languages['fi']['ALIAS'] = 'fi_FI';

$languages['fr_FR']['NAME']    = 'French';
$languages['fr_FR']['CHARSET'] = 'iso-8859-1';
$languages['fr']['ALIAS'] = 'fr_FR';

$languages['hr_HR']['NAME']    = 'Croatian';
$languages['hr_HR']['CHARSET'] = 'iso-8859-2';
$languages['hr']['ALIAS'] = 'hr_HR';

$languages['hu_HU']['NAME']    = 'Hungarian';
$languages['hu_HU']['CHARSET'] = 'iso-8859-2';
$languages['hu']['ALIAS'] = 'hu_HU';

$languages['id_ID']['NAME']    = 'Indonesian';
$languages['id_ID']['CHARSET'] = 'iso-8859-1';
$languages['id']['ALIAS'] = 'id_ID';

$languages['is_IS']['NAME']    = 'Icelandic';
$languages['is_IS']['CHARSET'] = 'iso-8859-1';
$languages['is']['ALIAS'] = 'is_IS';

$languages['it_IT']['NAME']    = 'Italian';
$languages['it_IT']['CHARSET'] = 'iso-8859-1';
$languages['it']['ALIAS'] = 'it_IT';

$languages['ja_JP']['NAME']    = 'Japanese';
$languages['ja_JP']['CHARSET'] = 'iso-2022-jp';
$languages['ja_JP']['XTRA_CODE'] = 'japanese_charset_xtra';
$languages['ja']['ALIAS'] = 'ja_JP';

$languages['ko_KR']['NAME']    = 'Korean';
$languages['ko_KR']['CHARSET'] = 'euc-KR';
$languages['ko_KR']['XTRA_CODE'] = 'korean_charset_xtra';
$languages['ko']['ALIAS'] = 'ko_KR';

$languages['nl_NL']['NAME']    = 'Dutch';
$languages['nl_NL']['CHARSET'] = 'iso-8859-1';
$languages['nl']['ALIAS'] = 'nl_NL';

$languages['no_NO']['NAME']    = 'Norwegian (Bokm&aring;l)';
$languages['no_NO']['CHARSET'] = 'iso-8859-1';
$languages['no']['ALIAS'] = 'no_NO';
$languages['nn_NO']['NAME']    = 'Norwegian (Nynorsk)';
$languages['nn_NO']['CHARSET'] = 'iso-8859-1';

$languages['pl_PL']['NAME']    = 'Polish';
$languages['pl_PL']['CHARSET'] = 'iso-8859-2';
$languages['pl']['ALIAS'] = 'pl_PL';

$languages['pt_PT']['NAME'] = 'Portuguese (Portugal)';
$languages['pt_PT']['CHARSET'] = 'iso-8859-1';
$languages['pt_BR']['NAME']    = 'Portuguese (Brazil)';
$languages['pt_BR']['CHARSET'] = 'iso-8859-1';
$languages['pt']['ALIAS'] = 'pt_PT';

$languages['ru_RU']['NAME']    = 'Russian';
$languages['ru_RU']['CHARSET'] = 'koi8-r';
$languages['ru']['ALIAS'] = 'ru_RU';

$languages['sr_YU']['NAME']    = 'Serbian';
$languages['sr_YU']['CHARSET'] = 'iso-8859-2';
$languages['sr']['ALIAS'] = 'sr_YU';

$languages['sv_SE']['NAME']    = 'Swedish';
$languages['sv_SE']['CHARSET'] = 'iso-8859-1';
$languages['sv']['ALIAS'] = 'sv_SE';

$languages['tr_TR']['NAME']    = 'Turkish';
$languages['tr_TR']['CHARSET'] = 'iso-8859-9';
$languages['tr']['ALIAS'] = 'tr_TR';

$languages['zh_TW']['NAME']    = 'Chinese Trad';
$languages['zh_TW']['CHARSET'] = 'big5';
$languages['tw']['ALIAS'] = 'zh_TW';

$languages['zh_CN']['NAME']    = 'Chinese Simp';
$languages['zh_CN']['CHARSET'] = 'gb2312';
$languages['cn']['ALIAS'] = 'zh_CN';

$languages['sk_SK']['NAME']     = 'Slovak';
$languages['sk_SK']['CHARSET']  = 'iso-8859-2';
$languages['sk']['ALIAS']       = 'sk_SK';

$languages['ro_RO']['NAME']    = 'Romanian';
$languages['ro_RO']['CHARSET'] = 'iso-8859-2';
$languages['ro']['ALIAS'] = 'ro_RO';

$languages['th_TH']['NAME']    = 'Thai';
$languages['th_TH']['CHARSET'] = 'tis-620';
$languages['th']['ALIAS'] = 'th_TH';

$languages['lt_LT']['NAME']    = 'Lithuanian';
$languages['lt_LT']['CHARSET'] = 'windows-1257';
$languages['lt']['ALIAS'] = 'lt_LT';

$languages['sl_SI']['NAME']    = 'Slovenian';
$languages['sl_SI']['CHARSET'] = 'iso-8859-2';
$languages['sl']['ALIAS'] = 'sl_SI';

$languages['bg_BG']['NAME']    = 'Bulgarian';
$languages['bg_BG']['CHARSET'] = 'windows-1251';
$languages['bg']['ALIAS'] = 'bg_BG';

$languages['uk_UA']['NAME']    = 'Ukrainian';
$languages['uk_UA']['CHARSET'] = 'koi8-u';
$languages['uk']['ALIAS'] = 'uk_UA';

// Right to left languages

$languages['ar']['NAME']    = 'Arabic';
$languages['ar']['CHARSET'] = 'windows-1256';
$languages['ar']['DIR']     = 'rtl';

$languages['he_IL']['NAME']    = 'Hebrew';
$languages['he_IL']['CHARSET'] = 'windows-1255';
$languages['he_IL']['DIR']     = 'rtl';
$languages['he']['ALIAS']      = 'he_IL';

$languages['vi_VN']['NAME']    = 'Vietnamese';
$languages['vi_VN']['CHARSET'] = 'utf-8';
$languages['vi']['ALIAS'] = 'vi_VN';

*/

/* Detect whether gettext is installed. */
$gettext_flags = 0;
if (function_exists('_')) {
    $gettext_flags += 1;
}
if (function_exists('bindtextdomain')) {
    $gettext_flags += 2;
}
if (function_exists('textdomain')) {
    $gettext_flags += 4;
}

/* If gettext is fully loaded, cool */
if (7 == $gettext_flags) {
    $use_gettext = true;
} /* If we can fake gettext, try that */ elseif (0 == $gettext_flags) {
    $use_gettext = true;

    require_once __DIR__ . '/gettext.php';
} else {
    /* Uh-ho.  A weird install */

    if (!$gettext_flags & 1) {
        function _($str)
        {
            return $str;
        }
    }

    if (!$gettext_flags & 2) {
        function bindtextdomain()
        {
        }
    }

    if (!$gettext_flags & 4) {
        function textdomain()
        {
        }
    }
}

// transitional function, should be removed soon
function translate($text)
{
    //  return _( trans_temp($text) );

    return _(trans_temp($text));
}
