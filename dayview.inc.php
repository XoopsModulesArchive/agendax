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

$agendax['eventsOf'] = _('Events of');
$agendax['eventdate'] = agendax_showdate($kdate, 'long');

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

# nr of days in month
$nr = date('t', mktime(12, 0, 0, $pm, 1, $py));
# previous day
$pd = ($day <= $nr) ? $day : $nr;

# nr of days in month
$nr = date('t', mktime(12, 0, 0, $nm, 1, $ny));
# next day
$nd = ($day <= $nr) ? $day : $nr;

$ppdate = date('Ymd', mktime(3, 0, 0, $pm, $pd, $py));
$pdate = date('Ymd', mktime(3, 0, 0, $month, $day, $year) - $ONE_DAY);

$nndate = date('Ymd', mktime(3, 0, 0, $nm, $nd, $ny));
$ndate = date('Ymd', mktime(3, 0, 0, $month, $day, $year) + $ONE_DAY);

//Navigation
$agendax['navleft'] = '<a href="'
                       . $agendax_url
                       . '/index.php?op=day&kdate='
                       . $ppdate
                       . '" style="vertical-align:middle;"><img src="'
                       . $arrowleftleft
                       . '" border="0" title="'
                       . _('Previous month')
                       . '" alt="'
                       . _('Previous month')
                       . '"></a>&nbsp;&nbsp;'
                       . '<a href="'
                       . $agendax_url
                       . '/index.php?op=day&kdate='
                       . $pdate
                       . '" style="vertical-align:middle;"><img src="'
                       . $arrowleft
                       . '" border="0" title="'
                       . _('Previous day')
                       . '" alt="'
                       . _('Previous day')
                       . '"></a>';
$agendax['navright'] = '<a href="'
                       . $agendax_url
                       . '/index.php?op=day&kdate='
                       . $ndate
                       . '" style="vertical-align:middle;"><img src="'
                       . $arrowright
                       . '" border="0" title="'
                       . _('Next day')
                       . '" alt="'
                       . _('Next day')
                       . '"></a>&nbsp;&nbsp;'
                       . '<a href="'
                       . $agendax_url
                       . '/index.php?op=day&kdate='
                       . $nndate
                       . '" style="vertical-align:middle;"><img src="'
                       . $arrowrightright
                       . '" border="0" title="'
                       . _('Next month')
                       . '" alt="'
                       . _('Next month')
                       . '"></a>';

$agendax['navcenter'] = agendax_showdate($kdate, 'long');

# print results of query
$agendax['title'] = [];
$agendax['category'] = [];
$agendax['categoryName'] = [];
$agendax['description'] = [];
$agendax['readmore'] = [];

$events = [];
$repeated_events = [];
$TZ_OFFSET = 0;
$events = agendax_read_events($kdate);
$repeated_events = read_repeated_events('', 0);
fill_date_entries($kdate, 0, AGENDAX_SHOW_BRIEF);

$agendax['backLink'] = back();

$GLOBALS['xoopsOption']['template_main'] = 'agendax_dayview.html';
