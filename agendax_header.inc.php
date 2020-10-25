<?php

// ------------------------------------------------------------------------- //
//                           Agendax-X for Xoops                             //
//                              Version:  2.0                                //
// ------------------------------------------------------------------------- //
// Author: Wang Jue (alias wjue)                                             //
// Purpose: Very Flexible Calendar and Event Module                          //
// email: wjue@wjue.org                                                      //
// URLs: http://www.China-Offshore.com,  http://www.guanxiCRM.com                      //
//---------------------------------------------------------------------------//
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//---------------------------------------------------------------------------//

require 'config.inc.php';

define('AGENDAX_SHOW_BRIEF', 0);
define('AGENDAX_SHOW_COMPLET', 1);
//define("AGENDAX_SHOW_MONTHLY_CALENDAR", 2);

$ONE_DAY = 86400;
// how many days in a month (regular and leap year)
$days_per_month = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
$ldays_per_month = [0, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

#agendax internationalization
$gettext_php_domain = 'agendax';
$gettext_php_dir = $agendax_path . '/locale';

#group permission constants
define('AGENDAX_PERM_SUBMIT', 1);
define('AGENDAX_PERM_EDITDELETE_EVENTS', 2);
define('AGENDAX_PERM_MANAGE_CATEGORIES', 4);
define('AGENDAX_PERM_APPROVE', 8);
define('AGENDAX_PERM_VIEW_OTHERS_EVENTS', 16);

require __DIR__ . '/i18n.php';

//$agendax_language = _LANGCODE; //to force a language (_LANGCODE: language_used_in_Xoops)/global.php
//set_up_language('en_US', false);
set_up_language();

$xoops_module_header = '<link rel="stylesheet" type="text/css" media="all" href="' . "$agendax_url/templates/style.css\">\n";

if (('zh_TW' == $agendax_language) || ('zh_CN' == $agendax_language)) {
    $dateSuffix['nian'] = _('Year');

    $dateSuffix['ri'] = _('Day');
} else {
    $dateSuffix['nian'] = '';

    $dateSuffix['ri'] = '';
}

// UserTimestamp so no problem for Time zone if user is logged in
$userTimestamp = xoops_getUserTimestamp(time());

/**
 * some settings of vars
 */
$op    = $_GET['op'] ?? '';
$kdate = $_GET['kdate'] ?? date('Ymd', $userTimestamp); //format yyyymmdd
$date  = $_GET['date'] ?? '';
//if (!isset($_GET['ask'])) $ask = ''; else $ask = $_GET['ask'];
$next     = $_GET['next'] ?? '';
$prev     = $_GET['prev'] ?? '';
$id       = $_GET['id'] ?? '';
$on       = $_GET['on'] ?? '';
$override = $_GET['override'] ?? '';

#initialize global template output $agendax
$agendax = [];

/* ----------------------------------------
** navbar at the top
*/

ob_start();

$m = date('n', $userTimestamp);
$y = date('Y', $userTimestamp);
$d = date('j', $userTimestamp);

echo "<!-- Agenda-X version $agd_version, Copyright Wang Jue (wjue) 2005, 2004 http://www.China-Offshore.com -->\n<div class=axNavbar>";

/*
** The unique id of an item to check permissions for.
*/
$perm_itemid = AGENDAX_PERM_SUBMIT;

/*
** check permission
** Specify the permission we are going to check for. This must be one of the permission names created on the admin side.
*/
$perm_name = 'Global Permission';

/*
** Get group ids that the current user belongs to.
*/
if ($xoopsUser) {
    $groups = $xoopsUser->getGroups();
} else {
    $groups = XOOPS_GROUP_ANONYMOUS;
}

/*
** Get the current module ID.
*/
$module_id = $xoopsModule->getVar('mid');

/*
** Get the group permission handler.
*/
$gpermHandler = xoops_getHandler('groupperm');

if ($gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
    if ('addevent' == $op) {
        echo '<b>';
    }

    if ('eventform' == $op) {
        echo '<b>';
    }

    echo "<a href=$agendax_url/index.php?op=eventform>" . _('Add event') . '</a>';

    if ('eventform' == $op) {
        echo '</b>';
    }

    if ('addevent' == $op) {
        echo '</b>';
    }

    echo " $delimiter ";
}

if ('cats' == $op) {
    echo '<b>';
}
echo "<a href=$agendax_url/index.php?op=cats>" . _('Categories') . '</a>';
if ('cats' == $op) {
    echo '</b>';
}

if ('day' == $op) {
    echo '<b>';
}
echo " $delimiter <a href=$agendax_url/index.php?op=day>" . _('Day view') . '</a>';
if ('day' == $op) {
    echo '</b>';
}

if ('week' == $op) {
    echo '<b>';
}
echo " $delimiter <a href=$agendax_url/index.php?op=week>" . _('Week view') . '</a>';
if ('week' == $op) {
    echo '</b>';
}

if ('cal' == $op) {
    echo '<b>';
}
echo " $delimiter <a href=$agendax_url/index.php?op=cal>" . _('Month view') . '</a>';
if ('cal' == $op) {
    echo '</b>';
}

if ('flat' == $op) {
    echo '<b>';
}
echo " $delimiter <a href=$agendax_url/index.php?op=flat>" . _('Flat view') . '</a>';
if ('flat' == $op) {
    echo '</b>';
}

//echo " $delimiter".'<a href="http://www.wjue.org/support/">'._("Support").'</a>';
# search

if ('search' == $op) {
    echo '<b>';
}
echo " $delimiter <a href=\"$agendax_url/index.php?op=search\">" . _('Search') . '</a>';
if ('search' == $op) {
    echo '</b>';
}

echo "<br><br></div>\n";

$agendax['navbar'] = ob_get_contents();
ob_end_clean();

//text sanitisor
$myts = MyTextSanitizer::getInstance();
