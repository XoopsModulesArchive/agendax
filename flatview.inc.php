<?php

// ------------------------------------------------------------------------- //
//                           Agendax-X for Xoops                             //
//                              Version:  1.0                               //
// ------------------------------------------------------------------------- //
// Author: Wang Jue (alias wjue)                                             //
// Purpose: Very Flexible Calendar and Event Module                          //
// email: wjue@wjue.org                                                      //
// URLs: http://www.wjue.org,  http://www.guanxiCRM.com                      //
// Parts of this software is based on EXTcalendar                       //
// of Kristof De Jaeger<br>sweaty@urgent.ugent.be                          //
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
// flatview.inc.php file

global $agendax;
global $dateSuffix;

$year = (int)mb_substr($kdate, 0, 4);
$month = (int)mb_substr($kdate, 4, 2);
$day = (int)mb_substr($kdate, 6, 2);

# previous month
$pm = $month;
if ('1' == $month) {
    $pm = '12';
} else {
    $pm--;
}
//$pm = ($pm <10)? '0'.$pm : $pm;

# previous year
$py = $year;
if ('12' == $pm) {
    $py--;
}

# next month
$nm = $month;
if ('12' == $month) {
    $nm = '1';
} else {
    $nm++;
}
# next year
$ny = $year;
if (1 == $nm) {
    $ny++;
}

$pdate = date('Ymd', mktime(3, 0, 0, $pm, $day, $py));
$ndate = date('Ymd', mktime(3, 0, 0, $nm, $day, $ny));

# get month we want to see
$askedmonth = $maand[$month];
$askedyear = $year;
$firstday = strftime('%w', mktime(12, 0, 0, $month, 1, $year));
$firstday++;
# nr of days in askedmonth
$nr = date('t', mktime(12, 0, 0, $month, 1, $year));

# link to previous month
if ($month != date('n') || $year != date('Y')) {
    $agendax['previous'] = '<a href="' . $agendax_url . '/index.php?op=flat&kdate=' . $pdate . '">';

    if (1 == $usearrows) {
        $agendax['previous'] .= "<img src=$arrowleft border=0 alt=\"_($maand[$pm]) $py\" title=\"_($maand[$pm]) $py\">";
    } else {
        $agendax['previous'] .= ' <= ';
    }

    if (1 == $displaylink) {
        $agendax['previous'] .= _($maand[$pm]) . ' - ' . $py . $dateSuffix['nian'];
    }

    $agendax['previous'] .= '</a>';
} elseif (1 == $archive) {
    $agendax['previous'] = '<a href="' . $agendax_url . '/index.php?op=flat&kdate=' . $pdate . '">';

    if (1 == $usearrows) {
        $agendax['previous'] .= '<img src="' . $arrowleft . '" border="0" alt="' . _($maand[$pm]) . ' ' . $py . '" title="' . _($maand[$pm]) . ' ' . $py . '">';
    } else {
        $agendax['previous'] .= ' <= ';
    }

    if (1 == $displaylink) {
        $agendax['previous'] .= _($maand[$pm]) . ' - ' . $py . $dateSuffix['nian'];
    }

    $agendax['previous'] .= '</a>';
}

# this month
$agendax['current'] = _($askedmonth) . ' ' . _($askedyear) . $dateSuffix['nian'];

# link to next month
$agendax['next'] = '<a href="' . $agendax_url . '/index.php?op=flat&kdate=' . $ndate . '">';
if (1 == $displaylink) {
    $agendax['next'] .= _($maand[$nm]) . ' - ' . $ny . $dateSuffix['nian'];
}
if (1 == $usearrows) {
    $agendax['next'] .= "<img src=$arrowright border=0 title=\"_($maand[$nm]) $ny\" alt=\"_($maand[$nm]) $ny\">";
} else {
    $agendax['next'] .= ' => ';
}
$agendax['next'] .= '</a>';

$events = [];
$repeated_events = [];
$TZ_OFFSET = 0;
$repeated_events = read_repeated_events('', 0);
global $ONE_DAY;

$agendax['edate'] = [];
$agendax['adminmenu'] = [];
$agendax['title'] = [];
$agendax['description'] = [];
$agendax['contact'] = [];
$agendax['email_caption'] = [];
$agendax['email'] = [];
$agendax['url_caption'] = [];
$agendax['url'] = [];
$agendax['picture'] = [];

$contact_caption = _('Contact') . ' : ';
$desc_caption = _('Description') . ' : ';
$xoopsTpl->assign('contact_caption', $contact_caption);
$xoopsTpl->assign('desc_caption', $desc_caption);

$firstdate = mktime(3, 0, 0, $month, 1, $year);

$date = date('Ymd', $firstdate);

for ($i = 1; $i <= $nr; $i++) {
    $events = agendax_read_events($date);

    fill_date_entries($date, 0, AGENDAX_SHOW_COMPLET);

    $date = date('Ymd', $firstdate + $i * $ONE_DAY);
}

$GLOBALS['xoopsOption']['template_main'] = 'agendax_flatview.html';
