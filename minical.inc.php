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

function display_minical($mini_imageUrl = '', $extra = '', $extrabis = '')
{
    global $ONE_DAY;

    //    global $xoopsDB;

    require XOOPS_ROOT_PATH . '/modules/agendax/config.inc.php';

    require_once XOOPS_ROOT_PATH . '/modules/agendax/include/functions.php';

    $userTimestamp = xoops_getUserTimestamp(time());

    $kdate = date('Ymd', $userTimestamp);

    $m = date('n', $userTimestamp);

    $y = date('Y', $userTimestamp);

    $d = date('j', $userTimestamp);

    $month = $m;

    $year = $y;

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

    if ('1' == $nm) {
        $ny++;
    }

    $pdate = date('Ymd', mktime(3, 0, 0, $pm, 1, $py));

    $ndate = date('Ymd', mktime(3, 0, 0, $nm, 1, $ny));

    # get month we want to see + calculate first day

    $askedmonth = $maand[$month];

    $askedyear = $year;

    $firstday = date('w', mktime(12, 0, 0, $month, 1, $year));

    $firstday++;

    # nr of days in askedmonth

    $nr = date('t', mktime(12, 0, 0, $month, 1, $year));

    # header (with links)

    echo "<table align=\"center\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" width=\"98%\">\n";

    echo "<tr><td align=\"center\" bgcolor=\"$mini_bgcolor\">";

    echo "<table align=\"center\" border=\"0\" cellspacing=0 cellpadding=2 width=\"100%\" background=\"$mini_headbackground\">";

    echo '<tr><td align=center height=10 valign="middle">';

    echo "<a href=\"$agendax_url/index.php?op=cal&kdate=" . $pdate . '">';

    echo '<img src=' . "$mini_arrowleft border=0 alt=\"$maand[$pm] $py\" title=\"$maand[$pm] $py\"></a>";

    echo '</td><td align=center height=10 valign="middle" width="98%">';

    echo "<a href=$agendax_url/index.php?op=cal&kdate=" . $kdate . '>';

    echo "<font size=$mini_calfontasked><b>" . $askedmonth . ' ' . $askedyear . '</b></font></a>';

    echo '</td><td align=center height=10 valign="middle">';

    echo "<a href=$agendax_url/index.php?op=cal&kdate=" . $ndate . '>';

    echo '<img src="' . "$mini_arrowright\" border=0 alt=\"$maand[$nm] $ny\" title=\"$maand[$nm] $ny\"></a>";

    echo "</td></tr>\n";

    echo "</table>\n";

    #this is the place to insert an image for our minicalendar

    echo "</td></tr><tr><td align=\"center\">$mini_imageUrl</td></tr><tr><td align=\"center\" bgcolor=\"$mini_bgcolor\">";

    #some extra if anything is provided for our function

    echo "</td><tr><tr><td align=\"center\">$extra</td></tr><tr><td align=\"center\" bgcolor=\"$mini_bgcolor\">";

    echo "</td><tr><tr><td align=\"center\">$extrabis</td></tr><tr><td align=\"center\" bgcolor=\"$mini_bgcolor\">";

    #  make the days of week, consisting of seven <td>'s (=days)

    # check if first is sunday or monday ? move sunday to end of array if day_start = 1

    echo '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%">';

    echo "<tr><td valign=\"top\" background=\"$mini_headbackground\" height=\"24\">";

    echo '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%"><tr>';

    if (0 == $day_start) {
        for ($i = 0; $i <= 6; $i++) {
            echo '<td><img src="' . $mini_weekday . $i . '.gif" alt=""></td>';
        }
    } else {
        for ($i = 1; $i <= 6; $i++) {
            echo '<td><img src="' . $mini_weekday . $i . '.gif" alt=""></td>';
        }

        $i = 0;

        echo '<td><img src="' . $mini_weekday . $i . '.gif" alt=""></td>';
    }

    echo "</tr></table>\n";

    echo "</td></tr></table>\n";

    echo '<table align="center" border="0" cellspacing="0" cellpadding="0" width="100%"><tr>';

    # begin the days

    if (1 == $day_start) {
        $firstday--;
    }

    if ((0 == $firstday) && (1 == $day_start)) {
        for ($i = 1; $i < 7; $i++) {
            echo "<td height=$mini_tdheight ";

            echo '>&nbsp;</td>';
        }
    } else {
        for ($i = 1; $i < $firstday; $i++) {
            echo "<td height=$mini_tdheight ";

            echo '>&nbsp;</td>';
        }
    }

    $a = 0;

    $churi = date('Ymd', mktime(12, 0, 0, $month, 1, $year));

    $weiri = date('Ymd', mktime(12, 0, 0, $month, $nr, $year));

    $events = read_events($churi, $weiri, '', 0);

    $rpt_events = read_repeated_events('', 0);

    $date_i = $churi;

    for ($i = 1; $i <= $nr; $i++) {
        $sizeOfEvents = count($events);

        $sizeOfRptEvents = count($rpt_events);

        $has_event = false;

        for ($k = 0; $k < $sizeOfEvents; $k++) {
            if ($events[$k]['date'] == $date_i) {
                $has_event = true;

                break;
            }
        }

        if (!$has_event) {
            for ($k = 0; $k < $sizeOfRptEvents; $k++) {
                if (repeated_event_matches_date($rpt_events[$k], $date_i)) {
                    // but is there an exception for this $date_i ?

                    $sql = 'SELECT event_date FROM ' . XOOPS_DB_PREFIX . '_agendax_event_repeats_not WHERE event_id=' . $rpt_events[$k]['id'];

                    $res = $GLOBALS['xoopsDB']->queryF($sql) || die('Invalid query: ' . $GLOBALS['xoopsDB']->error());

                    $exception = false;

                    while (false !== ($row = $GLOBALS['xoopsDB']->fetchBoth($res))) {
                        $exception = $exception || (@$row['event_date'] == $date_i);
                    }

                    if ($exception) {
                        $has_event = false;
                    } else {
                        $has_event = true;
                    }

                    break;
                }
            }
        }

        $sun = $i;

        if (1 == $day_start) {
            $sun++;
        }

        echo "<td height=$mini_tdheight class=\"minidigit\" ";

        $backgd = ($i == $d) ? $mini_todaybg : '';

        echo "background=\"$backgd\" ";

        if ($has_event) {
            echo ' style="color:#2266EE; font-size:9px" ';
        } elseif (($sun == (2 - $firstday)) || ($sun == (9 - $firstday)) or ($sun == (16 - $firstday)) or ($sun == (23 - $firstday)) or ($sun == (30 - $firstday)) or ($sun == (37 - $firstday))) {
            echo ' style="color:#AAAAAA;font-size:9px" ';
        } else {
            echo ' style="color:#666666;font-size:9px" ';
        }

        echo '>';

        if ($has_event) {
            echo "<a style=\"color:blue; font-size:9px\" href=$agendax_url/index.php?op=day&kdate=$date_i title=\"" . _AGX_CLICKFORINFO . '">';
        }

        echo $i;

        if ($has_event) {
            echo '</a>';
        }

        echo "</td>\n";

        # closing <tr> for the end of week

        $a++;

        if ((0 == $firstday and 1 == $i) or ($i == (8 - $firstday)) or ($i == (15 - $firstday)) or ($i == (22 - $firstday)) or ($i == (29 - $firstday)) or ($i == (36 - $firstday))) {
            echo "</tr>\n<tr>";

            $a = 0;
        }

        //incrementer $date_i au jour suivant

        $date_i = date('Ymd', mktime(12, 0, 0, $month, 1, $year) + $i * $ONE_DAY);
    }

    # ending stuff (making 'white' td's to fill table)

    if (0 != $a) {
        $last = 7 - $a;

        for ($i = 1; $i <= $last; $i++) {
            echo '<td>&nbsp;</td>';
        }
    }

    echo "</tr>\n";

    echo "</table>\n";

    echo "</td></tr></table>\n";
}
