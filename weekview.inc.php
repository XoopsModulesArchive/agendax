<?php

// ------------------------------------------------------------------------- //
//                           Agendax-X for Xoops                             //
//                              Version:  2.0                                //
// ------------------------------------------------------------------------- //
// Author: Wang Jue (alias wjue)                                             //
// Purpose: Very Flexible Calendar and Event Module                          //
// email: wjue@wjue.org                                                      //
// URLs: http://www.wjue.org,  http://www.guanxiCRM.com                      //
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
// weekview.inc.php file

# check date
$year = (int)mb_substr($kdate, 0, 4);
$month = (int)mb_substr($kdate, 4, 2);
$day = (int)mb_substr($kdate, 6, 2);

$date = $kdate;

# function to calculate weeknumber
function weekNumber($dag, $maand, $jaar)
{
    $a = (14 - $maand) / 12;

    $a = (int) $a;

    $y = $jaar + 4800 - $a;

    $y = (int) $y;

    $m = $maand + 12 * $a - 3;

    $m = (int) $m;

    $J = $dag + (153 * $m + 2) / 5 + $y * 365 + $y / 4 - $y / 100 + $y / 400 - 32045;

    $d4 = ($J + 31741 - ($J % 7)) % 146097 % 36524 % 1461;

    $L = $d4 / 1460;

    $d1 = (($d4 - $L) % 365) + $L;

    $WeekNumber = ($d1 / 7) + 1;

    $WeekNumber = (int) $WeekNumber;

    return $WeekNumber;
}

$deesweek = mktime(0, 0, 0, date('d'), date('m'), date('Y'));
$weeknummer = weekNumber($day, $month, $year);
$laatsteweek = ($weeknummer + 10);
if ($laatsteweek > 52) {
    $laatsteweek -= 52;
}

# first day of week function
function firstDayOfWeek($year, $month, $day)
{
    $dayOfWeek = date('w');

    $sunday_offset = $dayOfWeek * 60 * 60 * 24;

    $fd = date('Ymd', mktime(0, 0, 0, $month, $day + 1, $year) - $sunday_offset);

    return $fd;
}

$fd = firstDayOfWeek($year, $month, $day);

# last day of week
function lastDayOfWeek($year, $month, $day)
{
    $dayOfWeek = date('w');

    $saturday_offset = (6 - $dayOfWeek) * 60 * 60 * 24;

    $ld = date('Ymd', mktime(0, 0, 0, $month, $day + 1, $year) + $saturday_offset);

    return $ld;
}

$ld = lastDayOfWeek($year, $month, $day);

# From week to week
$agendax['eventsFrom'] = _('Events from');

$agendax['dateFrom'] = agendax_showdate($fd, 'long');

$agendax['till'] = _('till');

$agendax['dateTo'] = agendax_showdate($ld, 'long');

$ldy = mb_substr($ld, 0, 4);
$ldm = mb_substr($ld, 4, 2);
if ('0' == mb_substr($ldm, 0, 1)) {
    $ldm = str_replace('0', '', $ldm);
}
$ldd = mb_substr($ld, 6, 2);
if ('0' == mb_substr($ldd, 0, 1)) {
    $ldd = str_replace('0', '', $ldd);
}

# previous and next week links
if (($date) && ($date != date('Ymd'))) {
    $agendax['prevWeek'] = "<a href=$agendax_url/index.php?op=week&kdate=" . date('Ymd', mktime(0, 0, 0, $month, $day - 7, $year)) . '>';

    if (1 == $usearrows) {
        $agendax['prevWeek'] .= "<img src=$arrowleft border=0 alt=\"" . _('Previous week') . '" title="' . _('Previous week') . '">';
    } else {
        $agendax['prevWeek'] = '<== ';
    }

    if (1 == $displaylink) {
        $agendax['prevWeek'] .= _('Previous week');
    }

    $agendax['prevWeek'] .= '</a>';
} elseif (1 == $archive) {
    $agendax['prevWeek'] = "<a href=$agendax_url/index.php?op=week&kdate=" . date('Ymd', mktime(0, 0, 0, $month, $day - 7, $year)) . '>';

    if (1 == $usearrows) {
        $agendax['prevWeek'] .= "<img src=$arrowleft border=0 alt=\"" . _('Previous week') . '" title="' . _('Previous week') . '">';
    } else {
        $agendax['prevWeek'] .= '<== ';
    }

    if (1 == $displaylink) {
        $agendax['prevWeek'] .= _('Previous week');
    }

    $agendax['prevWeek'] .= '</a>';
}
$agendax['nextWeek'] = "<a href=$agendax_url/index.php?op=week&kdate=" . date('Ymd', mktime(0, 0, 0, $month, $day + 7, $year)) . '>';

if (1 == $displaylink) {
    $agendax['nextWeek'] .= _('Next week');
}
if (1 == $usearrows) {
    $agendax['nextWeek'] .= "<img src=$arrowright border=0 alt=\"" . _('Next week') . '" title="' . _('Next week') . '">';
} else {
    $agendax['nextWeek'] .= ' ==> ';
}
$agendax['nextWeek'] .= '</a>';

# now let's get the results
$ld = date('Ymd', mktime(0, 0, 0, $ldm, $ldd + 1, $ldy));

$agendax['title'] = [];
$agendax['edate'] = [];
$agendax['category'] = [];
$agendax['categoryName'] = [];
$agendax['description'] = [];
$agendax['readmore'] = [];

$events = [];
$repeated_events = [];
$TZ_OFFSET = 0;
$repeated_events = read_repeated_events('', 0);
global $ONE_DAY;

$fyear = mb_substr($fd, 0, 4);
$fmonth = mb_substr($fd, 4, 2);
$fday = mb_substr($fd, 6, 2);
$firstdate = mktime(3, 0, 0, $fmonth, $fday, $fyear);

$lyear = mb_substr($ld, 0, 4);
$lmonth = mb_substr($ld, 4, 2);
$lday = mb_substr($ld, 6, 2);
$lastdate = mktime(3, 0, 0, $lmonth, $lday, $lyear);

$nbofdays = ($lastdate - $firstdate) / $ONE_DAY;

$date = $fd;
for ($i = 1; $i < $nbofdays; $i++) {
    $events = agendax_read_events($date);

    fill_date_entries($date, 0, AGENDAX_SHOW_BRIEF);

    $date = date('Ymd', $firstdate + $i * $ONE_DAY);
}

$agendax['backLink'] = back();

$GLOBALS['xoopsOption']['template_main'] = 'agendax_weekview.html';
