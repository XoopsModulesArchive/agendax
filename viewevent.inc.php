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
// viewevent.inc.php file

global $myts;     //text sanitisor

$query = 'select picture,id,title,cat_name,description,contact,date,approved,url,email,type,submit_by,cat_id from ' . XOOPS_DB_PREFIX . '_agendax_events ';
$query .= 'left join ' . XOOPS_DB_PREFIX . '_agendax_cat on ' . XOOPS_DB_PREFIX . '_agendax_events.cat=' . XOOPS_DB_PREFIX . "_agendax_cat.cat_id where id='$id'";
$result = $GLOBALS['xoopsDB']->queryF($query);
$row = $GLOBALS['xoopsDB']->fetchObject($result);

if (('R' == $row->type) && ('' != $on)) {
    $showdate = $on;

    $year = (int)mb_substr($on, 0, 4);

    $month = (int)mb_substr($on, 4, 2);

    $day = (int)mb_substr($on, 6, 2);
} else {
    $showdate = $row->date;

    $year = (int)mb_substr($row->date, 0, 4);

    $month = (int)mb_substr($row->date, 4, 2);

    $day = (int)mb_substr($row->date, 6, 2);
}

if ($row->picture) {
    $agendax['image'] = '<img src="' . XOOPS_URL . '/uploads/' . $row->picture . '">';
} else {
    $agendax['image'] = '';
}
$agendax['title']['caption'] = _('Title');
$agendax['title']['body'] = htmlspecialchars($row->title, ENT_QUOTES | ENT_HTML5);

$agendax['edate']['caption'] = _('Date');

if (_SHORTDATESTRING == 'n/j/Y') {
    $agendax['edate']['body'] = _($maand[$month]) . ' ' . $day . $dateSuffix['ri'] . ' ' . $year . $dateSuffix['nian'];
} elseif (_SHORTDATESTRING == 'j/n/Y') {
    $agendax['edate']['body'] = $day . $dateSuffix['ri'] . ' ' . _($maand[$month]) . ' ' . $year . $dateSuffix['nian'];
} else {          //default to "Y/n/j"
    $agendax['edate']['body'] = $year . $dateSuffix['nian'] . ' ' . _($maand[$month]) . ' ' . $day . $dateSuffix['ri'];
}
$agendax['edate']['body'] = agendax_showdate($showdate, 'long');

$agendax['category']['caption'] = _('Category');
$agendax['category']['body'] = "<a href=$agendax_url/index.php?op=cat&id=$row->cat_id>" . $row->cat_name . '</a>';

if ($row->contact) {
    $agendax['contact']['caption'] = _('Contact');

    $agendax['contact']['body'] = '<a href=mailto:' . $row->email . '>' . htmlspecialchars($row->contact, ENT_QUOTES | ENT_HTML5) . '</a>';
}
$agendax['description']['caption'] = _('Description');
$agendax['description']['body'] = $myts->displayTarea($row->description);

$agendax['url']['caption'] = _('More info');
if ($row->url) {
    $agendax['url']['body'] = '<a href=http://' . $row->url . ' target=_blank>' . $row->url . '</a>';
}

$agendax['email']['caption'] = _('Email');
if ($row->email) {
    $agendax['email']['body'] = '<a href=mailto:' . $row->email . '>' . $row->email . '</a>';
}

$rpt_str = agendax_event_type_string($row, $showdate);

$agendax['rpt'] = $rpt_str;

$agendax['backLink'] = back();

//if ($searchdayok == 1)  $agendax['searchform'] = search();

$GLOBALS['xoopsOption']['template_main'] = 'agendax_viewevent.html';

function agendax_event_type_string($row, $on)
{
    // Load event info now.

    global $xoopsDB, $userTimestamp;

    global $days_per_month, $ldays_per_month;

    //$sql = 'SELECT submit_by, date, time, modif_date, ' .

    //  'modif_time, duration, priority, type, access, ' .

    //  'title, description, cat FROM '.$xoopsDB->prefix("agendax_events").' WHERE id = ' . $id;

    //$res = $xoopsDB->queryF( $sql );

    //if ( ! $res ) {

    //  echo _("Invalid entry id") . ": $id";

    //  exit;

    //$row = $xoopsDB->fetchRow ( $res );

    $submit_by = $row->submit_by;

    $orig_date = $row->date;

    $event_time = 0; // non used for the moment

    $title = $row->title;

    $description = $row->description;

    $id = $row->id;

    $rep_str = '';

    // Set alterted date

    $tz_date = $on;

    // save date so the trailer links are for the same time period

    $thisyear = (int)($tz_date / 10000);

    $thismonth = ($tz_date / 100) % 100;

    $thisday = $tz_date % 100;

    $thistime = mktime(3, 0, 0, $thismonth, $thisday, $thisyear);

    $thisdow = date('w', $thistime);

    $event_repeats = false;

    // build info string for repeating events and end date

    $sql = 'SELECT event_rpt_type, event_end, frequency, event_repeaton_days ' . 'FROM ' . $xoopsDB->prefix('agendax_event_repeats') . ' WHERE event_id = ' . $id;

    $res = $xoopsDB->queryF($sql);

    if ($res) {
        if ($tmprow = $xoopsDB->fetchRow($res)) {
            $event_repeats = true;

            $event_rpt_type = $tmprow[0];

            $event_end = $tmprow[1];

            $frequency = $tmprow[2];

            $event_repeaton_days = $tmprow[3];

            if ($event_end) {
                $rep_str .= agendax_showdate($row->date, 'middle') . ' - ';

                $rep_str .= agendax_showdate($event_end, 'middle');
            }

            $rep_str .= '&nbsp; (' . _('every') . ' ';

            if ($frequency > 1) {
                switch ($frequency) {
                    case 2:
                        $rep_str .= _('2nd');

                       break;
                    case 3:
                        $rep_str .= _('3rd');

                       break;
                    case 4:
                        $rep_str .= _('4th');

                       break;
                    case 5:
                        $rep_str .= _('5th');

                       break;
                    case 12:
                        if ('monthlyByDay' == $event_rpt_type
                            || 'monthlyByDayR' == $event_rpt_type) {
                            break;
                        }
                        // no break
                    default:
                        $rep_str .= $frequency;

                       break;
                }
            }

            $rep_str .= ' ';

            switch ($event_rpt_type) {
                case 'daily':
                    $rep_str .= _('Day');

                   break;
                case 'weekly':
                    $rep_str .= _('Week');
                    for ($i = 0; $i <= 7; $i++) {
                        if ('y' == mb_substr($event_repeaton_days, $i, 1)) {
                            $rep_str .= ', ' . weekday_short_name($i);
                        }
                    }

                   break;
                case 'monthlyByDay':
                    case 'monthlyByDayR':
                    if (12 == $frequency) {
                        $rep_str .= month_name($thismonth - 1) . ' / ';
                    } else {
                        $rep_str .= _('Month') . ' / ';
                    }
                    $days_this_month = 0 == $thisyear % 4 ? $ldays_per_month[$thismonth] : $days_per_month[$thismonth];
                    if ('monthlyByDay' == $event_rpt_type) {
                        $dow1 = date('w', mktime(3, 0, 0, $thismonth, 1, $thisyear));

                        $days_in_first_week = (7 - $dow1) % 7;

                        if (0 == $days_in_first_week) {
                            $days_in_first_week = 7;
                        }

                        $whichWeek = floor(($thisday - $days_in_first_week - 0.1) / 7);

                        if ($thisdow >= $dow1) {
                            $whichWeek++;
                        }
                    } else {
                        $whichWeek = floor(($days_this_month - $thisday) / 7);
                    }
                    $rep_str .= ' ';
                    switch ($whichWeek) {
                        case 0:
                                                                                       if ('monthlyByDay' == $event_rpt_type) {
                                                                                           $rep_str .= _('1st');
                                                                                       }

                                                                                      break;
                        case 1:
                                                                                       $rep_str .= _('2nd');

                                                                                      break;
                        case 2:
                                                                                       $rep_str .= _('3rd');

                                                                                      break;
                        case 3:
                                                                                       $rep_str .= _('4th');

                                                                                      break;
                        case 4:
                                                                                       $rep_str .= _('5th');

                                                                                      break;
                    }

                    if ('monthlyByDayR' == $event_rpt_type) {
                        $rep_str .= ' ' . _('last');
                    }
                    $rep_str .= ' ' . weekday_name($thisdow);

                   break;
                case 'monthlyByDate':
                    $rep_str .= _('Month') . '/' . _('by date');

                   break;
                case 'yearly':
                    $rep_str .= _('Year');

                   break;
            }

            $rep_str .= ')';
        } else {
            $rep_str = '';
        }
    }

    return $rep_str;
}
