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
// This portion of program uses Webcalendar project of Craig Knudsen <cknudsen@radix.net>

$ONE_DAY = 86400;
// how many days in a month (regular and leap year)
$days_per_month = [0, 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
$ldays_per_month = [0, 31, 29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

/* Print out a date selection for use in a form.
** params:
**   $prefix - prefix to use in front of form element names
**   $date - currently selected date (in YYYYMMDD) format
*/
function print_date_selection($prefix, $date)
{
    print date_selection_html($prefix, $date);
}

/* Generate a date selection for use in a form and return in.
** params:
**   $prefix - prefix to use in front of form element names
**   $date - currently selected date (in YYYYMMDD) format
*/
function date_selection_html($prefix, $date)
{
    $ret = '';

    if (8 != mb_strlen($date)) {
        $date = date('Ymd');
    }

    $thisyear = $year = mb_substr($date, 0, 4);

    $thismonth = $month = mb_substr($date, 4, 2);

    $thisday = $day = mb_substr($date, 6, 2);

    $ret .= '<SELECT NAME="' . $prefix . 'day">';

    for ($i = 1; $i <= 31; $i++) {
        $ret .= '<OPTION ' . ($i == $thisday ? ' SELECTED' : '') . ">$i";
    }

    $ret .= "</SELECT>\n<SELECT NAME=\"" . $prefix . 'month">';

    for ($i = 1; $i <= 12; $i++) {
        $m = month_short_name($i - 1);

        $ret .= "<OPTION VALUE=\"$i\"" . ($i == $thismonth ? ' SELECTED' : '') . ">$m";
    }

    $ret .= "</SELECT>\n<SELECT NAME=\"" . $prefix . 'year">';

    for ($i = -1; $i < 5; $i++) {
        $y = date('Y') + $i;

        $ret .= "<OPTION VALUE=\"$y\"" . ($y == $thisyear ? ' SELECTED' : '') . ">$y";
    }

    $ret .= "</SELECT>\n";

    $ret .= "<INPUT TYPE=\"button\" ONCLICK=\"selectDate('" . $prefix . "day','" . $prefix . "month','" . $prefix . "year',$date)\" VALUE=\"" . _('Select') . '...">';

    return $ret;
}

/* Read all the events for a user for the specified range of dates.
** This is only called once per page request to improve performance.
** All the events get loaded into the array $events sorted by
** time of day (not date).
** params:
**   $user - userid
**   $startdate - start date range, inclusive (in YYYYMMDD format)
**   $enddate - end date range, inclusive (in YYYYMMDD format)
**   $cat_id - category ID to filter on
*/
function read_events($startdate, $enddate, $cat_id = '', $user = 0)
{
    global $TZ_OFFSET;

    global $xoopsDB;

    $sy = mb_substr($startdate, 0, 4);

    $sm = mb_substr($startdate, 4, 2);

    $sd = mb_substr($startdate, 6, 2);

    $ey = mb_substr($enddate, 0, 4);

    $em = mb_substr($enddate, 4, 2);

    $ed = mb_substr($enddate, 6, 2);

    if ($startdate == $enddate) {
        if (0 == $TZ_OFFSET) {
            $date_filter = ' AND ' . $xoopsDB->prefix('agendax_events') . ".date = $startdate";
        } elseif ($TZ_OFFSET > 0) {
            $prev_day = mktime(3, 0, 0, $sm, $sd - 1, $sy);

            $cutoff = 24 - $TZ_OFFSET . '0000';

            $date_filter = ' AND ( ( ' . $xoopsDB->prefix('agendax_events') . ".date = $startdate AND " . '( ' . $xoopsDB->prefix('agendax_events') . ".time <= $cutoff OR " . $xoopsDB->prefix('agendax_events') . '.time = -1 ) ) OR ' . '( ' . $xoopsDB->prefix('agendax_events') . '.date = ' . date(
                'Ymd',
                $prev_day
            ) . ' AND ' . $xoopsDB->prefix('agendax_events') . ".time >= $cutoff ) )";
        } else {
            $next_day = mktime(3, 0, 0, $sm, $sd + 1, $sy);

            $cutoff = (0 - $TZ_OFFSET) * 10000;

            $date_filter = ' AND ( ( ' . $xoopsDB->prefix('agendax_events') . ".date = $startdate AND " . '( ' . $xoopsDB->prefix('agendax_events') . ".time > $cutoff OR " . $xoopsDB->prefix('agendax_events') . '.time = -1 ) ) OR ' . '( ' . $xoopsDB->prefix('agendax_events') . '.date = ' . date(
                'Ymd',
                $next_day
            ) . ' AND ' . $xoopsDB->prefix('agendax_events') . ".time <= $cutoff ) )";
        }
    } else {
        if (0 == $TZ_OFFSET) {
            $date_filter = ' AND ' . $xoopsDB->prefix('agendax_events') . ".date >= $startdate " . 'AND ' . $xoopsDB->prefix('agendax_events') . ".date <= $enddate";
        } elseif ($TZ_OFFSET > 0) {
            $prev_day = date(('Ymd'), mktime(3, 0, 0, $sm, $sd - 1, $sy));

            $enddate_minus1 = date(('Ymd'), mktime(3, 0, 0, $em, $ed - 1, $ey));

            $cutoff = 24 - $TZ_OFFSET . '0000';

            $date_filter = ' AND ( ( '
                              . $xoopsDB->prefix('agendax_events')
                              . ".date >= $startdate "
                              . 'AND '
                              . $xoopsDB->prefix('agendax_events')
                              . ".date <= $enddate AND "
                              . $xoopsDB->prefix('agendax_events')
                              . '.time = -1 ) OR '
                              . '( '
                              . $xoopsDB->prefix('agendax_events')
                              . ".date = $prev_day AND "
                              . $xoopsDB->prefix('agendax_events')
                              . ".time >= $cutoff ) OR "
                              . '( '
                              . $xoopsDB->prefix('agendax_events')
                              . ".date = $enddate AND "
                              . $xoopsDB->prefix('agendax_events')
                              . ".time < $cutoff ) OR "
                              . '( '
                              . $xoopsDB->prefix('agendax_events')
                              . ".date >= $startdate AND "
                              . $xoopsDB->prefix('agendax_events')
                              . ".date <= $enddate_minus1 ) )";
        } else {
            // TZ_OFFSET < 0

            $next_day = date(('Ymd'), mktime(3, 0, 0, $sm, $sd + 1, $sy));

            $enddate_plus1 = date(('Ymd'), mktime(3, 0, 0, $em, $ed + 1, $ey));

            $cutoff = (0 - $TZ_OFFSET) * 10000;

            $date_filter = ' AND ( ( '
                             . $xoopsDB->prefix('agendax_events')
                             . ".date >= $startdate "
                             . 'AND '
                             . $xoopsDB->prefix('agendax_events')
                             . ".date <= $enddate AND "
                             . $xoopsDB->prefix('agendax_events')
                             . '.time = -1 ) OR '
                             . '( '
                             . $xoopsDB->prefix('agendax_events')
                             . ".date = $startdate AND "
                             . $xoopsDB->prefix('agendax_events')
                             . ".time > $cutoff ) OR "
                             . '( '
                             . $xoopsDB->prefix('agendax_events')
                             . ".date = $enddate_plus1 AND "
                             . $xoopsDB->prefix('agendax_events')
                             . ".time <= $cutoff ) OR "
                             . '( '
                             . $xoopsDB->prefix('agendax_events')
                             . ".date > $startdate AND "
                             . $xoopsDB->prefix('agendax_events')
                             . ".date < $enddate ) )";
        }
    }

    return query_events($user, false, $date_filter, $cat_id);
}

/* Get all the events for a specific date from the array of pre-loaded
** events (which was loaded all at once to improve performance).
** The returned events will be sorted by time of day.
** params:
**   $user - username
**   $date - date to get events for in YYYYMMDD format
*/
function get_entries($user, $date)
{
    global $events, $TZ_OFFSET;

    $n = 0;

    $ret = [];

    //echo "<P>Checking " . count ( $events ) . " events.  TZ_OFFSET = $TZ_OFFSET<P>";

    for ($i = 0, $iMax = count($events); $i < $iMax; $i++) {
        if (0 == $TZ_OFFSET) {
            if ($events[$i]['date'] == $date) {
                $ret[$n++] = $events[$i];
            }
        } elseif ($TZ_OFFSET > 0) {
            $cutoff = (24 - $TZ_OFFSET) * 10000;

            $sy = mb_substr($date, 0, 4);

            $sm = mb_substr($date, 4, 2);

            $sd = mb_substr($date, 6, 2);

            $prev_day = date(('Ymd'), mktime(3, 0, 0, $sm, $sd - 1, $sy));

            if ($events[$i]['date'] == $date && -1 == $events[$i]['time']) {
                $ret[$n++] = $events[$i];
            } elseif ($events[$i]['date'] == $date && $events[$i]['time'] < $cutoff) {
                $ret[$n++] = $events[$i];
            } elseif ($events[$i]['date'] == $prev_day && $events[$i]['time'] >= $cutoff) {
                $ret[$n++] = $events[$i];
            }
        } else {
            //TZ < 0

            $cutoff = (0 - $TZ_OFFSET) * 10000;

            $sy = mb_substr($date, 0, 4);

            $sm = mb_substr($date, 4, 2);

            $sd = mb_substr($date, 6, 2);

            $next_day = date(('Ymd'), mktime(3, 0, 0, $sm, $sd + 1, $sy));

            if (-1 == $events[$i]['time']) {
                if ($events[$i]['date'] == $date) {
                    $ret[$n++] = $events[$i];
                }
            } else {
                if ($events[$i]['date'] == $date && $events[$i]['time'] > $cutoff) {
                    $ret[$n++] = $events[$i];
                } elseif ($events[$i]['date'] == $next_day && $events[$i]['time'] <= $cutoff) {
                    $ret[$n++] = $events[$i];
                }
            }
        }
    }

    return $ret;
}

// Read events visible to a user (including layers); return results
// in an array sorted by time of day.
// params:
//   $user - userid
//   $want_repeated - true to get repeating events; false to get
//     non-repeating.
//   $date_filter - SQL phrase starting with AND, to be appended to
//     the WHERE clause.  May be empty string.
//   $cat_id - category ID to filter on.  May be empty.
// return:
//   array of events

function query_events($user, $want_repeated, $date_filter, $cat_id = '')
{
    $result = [];

    global $xoopsDB;

    $sql = 'SELECT title, description, ' . 'contact, url, email, picture, cat, ' . 'date, time, ' . 'id, ext_for_id, ' . 'priority, ' . 'access, duration, ' . 'cat_name, type ';

    if ($want_repeated) {
        $sql .= ', '
                . ' event_rpt_type, event_end, '
                . ' frequency, event_repeaton_days '
                . ' FROM '
                . $xoopsDB->prefix('agendax_events')
                . ', '
                . $xoopsDB->prefix('agendax_event_repeats')
                . ' LEFT JOIN '
                . $xoopsDB->prefix('agendax_cat')
                . ' ON cat = cat_id '
                . ' WHERE id = event_id AND approved = 1 ';
    } else {
        $sql .= ' FROM ' . $xoopsDB->prefix('agendax_events') . ' LEFT JOIN ' . $xoopsDB->prefix('agendax_cat') . ' ON cat = cat_id ' . ' WHERE approved = 1 ';
    }

    if ('' != $cat_id) {
        $sql .= " AND cat = $cat_id ";
    }

    $sql .= $date_filter;

    // now order the results by time and by entry id.

    $sql .= ' ORDER BY time, id';

    $res = $xoopsDB->queryF($sql);

    if ($res) {
        $i = 0;

        $checkdup_id = -1;

        $first_i_this_id = -1;

        while (false !== ($row = $xoopsDB->fetchRow($res))) {
            $item = [
                'title' => $row[0],
'description' => $row[1],
'contact' => $row[2],
'url' => $row[3],
'email' => $row[4],
'picture' => $row[5],
'cat' => $row[6],
'date' => $row[7],
'time' => $row[8],
'id' => $row[9],
'ext_for_id' => $row[10],
'priority' => $row[11],
'access' => $row[12],
'duration' => $row[13],
'cat_name' => $row[14],
'type' => $row[15],
'exceptions' => [],
            ];

            if ($want_repeated && !empty($row[16])) {
                $item['event_rpt_type'] = empty($row[16]) ? '' : $row[16];

                $item['event_end'] = empty($row[17]) ? '' : $row[17];

                $item['frequency'] = empty($row[18]) ? '' : $row[18];

                $item['event_repeaton_days'] = empty($row[19]) ? '' : $row[19];
            }

            $result[$i++] = $item;
        }

        //    dbi_free_result ( $res );
    }

    // Now load event exceptions and store as array in 'exceptions' field

    if ($want_repeated) {
        for ($i = 0, $iMax = count($result); $i < $iMax; $i++) {
            if (!empty($result[$i]['id'])) {
                $res = $xoopsDB->queryF(
                    'SELECT event_date FROM ' . $xoopsDB->prefix('agendax_event_repeats_not') . ' WHERE event_id = ' . $result[$i]['id']
                );

                while (false !== ($row = $xoopsDB->fetchRow($res))) {
                    $result[$i]['exceptions'][] = $row[0];
                }
            }
        }
    }

    return $result;
}

/* Read all the repeated events for a user.  This is only called once
** per page request to improve performance.  All the events get loaded
** into the array $repeated_events sorted by time of day (not date).
** params:
**   $user - userid
**   $cat_id - category ID to filter on.  May be empty.
*/
function read_repeated_events($cat_id = '', $user = 0)
{
    return query_events($user, true, '', $cat_id);
}

//Returns all the dates a specific event will fall on accounting for
//the repeating.  Any event with no end will be assigned one.
//params:
//  $date - initial date in timestamp format
//  $rpt_type - repeating type as stored in the database
//  $end  - end date
//  $days - days events occurs on (for weekly)
//  $ex_dates - array of exception dates for this event in YYYYMMDD format
//  $freq - frequency of repetition
function get_all_dates($date, $rpt_type, $end, $days, $ex_days, $freq = 1)
{
    global $conflict_repeat_months, $days_per_month, $ldays_per_month;

    global $ONE_DAY;

    $currentdate = floor($date / $ONE_DAY) * $ONE_DAY;

    $realend = floor($end / $ONE_DAY) * $ONE_DAY;

    $dateYmd = date('Ymd', $date);

    if ('NULL' == $end) {
        // Check for $conflict_repeat_months months into future for conflicts

        $thismonth = mb_substr($dateYmd, 4, 2);

        $thisyear = mb_substr($dateYmd, 0, 4);

        $thisday = mb_substr($dateYmd, 6, 2);

        $thismonth += $conflict_repeat_months;

        if ($thismonth > 12) {
            $thisyear++;

            $thismonth -= 12;
        }

        $realend = mktime(3, 0, 0, $thismonth, $thisday, $thisyear);
    }

    $ret = [];

    $ret[0] = $date;

    //do iterative checking here.

    //I floored the $realend so I check it against the floored date

    if ($rpt_type && $currentdate < $realend) {
        $cdate = $date;

        if (!$freq) {
            $freq = 1;
        }

        $n = 1;

        if ('daily' == $rpt_type) {
            //we do inclusive counting on end dates.

            $cdate += $ONE_DAY * $freq;

            while ($cdate <= $realend + $ONE_DAY) {
                if (!is_exception($cdate, $ex_days)) {
                    $ret[$n++] = $cdate;
                }

                $cdate += $ONE_DAY * $freq;
            }
        } elseif ('weekly' == $rpt_type) {
            $daysarray = [];

            $r = 0;

            $dow = date('w', $date);

            $cdate = $date - ($dow * $ONE_DAY);

            for ($i = 0; $i < 7; $i++) {
                $isDay = mb_substr($days, $i, 1);

                if (0 == strcmp($isDay, 'y')) {
                    $daysarray[$r++] = $i * $ONE_DAY;
                }
            }

            //we do inclusive counting on end dates.

            while ($cdate <= $realend + $ONE_DAY) {
                //add all of the days of the week.

                for ($j = 0; $j < $r; $j++) {
                    $td = $cdate + $daysarray[$j];

                    if ($td >= $date) {
                        if (!is_exception($cdate, $ex_days)) {
                            $ret[$n++] = $td;
                        }
                    }
                }

                //skip to the next week in question.

                $cdate += ($ONE_DAY * 7) * $freq;
            }
        } elseif ('monthlyByDay' == $rpt_type) {
            $dow = date('w', $date);

            $thismonth = mb_substr($dateYmd, 4, 2);

            $thisyear = mb_substr($dateYmd, 0, 4);

            $week = floor(date('d', $date) / 7);

            $thismonth += $freq;

            $dow1 += date('w', mktime(3, 0, 0, $thismonth, 1, $thisyear));

            $t = $dow - $dow1;

            if ($t < 0) {
                $t += 7;
            }

            $day = 7 * $week + $t + 1;

            $cdate = mktime(3, 0, 0, $thismonth, $day, $thisyear);

            while ($cdate <= $realend + $ONE_DAY) {
                if (!is_exception($cdate, $ex_days)) {
                    $ret[$n++] = $cdate;
                }

                $thismonth += $freq;

                $dow1 += date('w', mktime(3, 0, 0, $thismonth, 1, $thisyear));

                $t = $dow - $dow1;

                if ($t < 0) {
                    $t += 7;
                }

                $day = 7 * $week + $t + 1;

                $cdate = mktime(3, 0, 0, $thismonth, $day, $thisyear);
            }
        } elseif ('monthlyByDayR' == $rpt_type) {
            // by weekday of month reversed (i.e., last Monday of month)

            $dow = date('w', $date);

            $thisday = mb_substr($dateYmd, 6, 2);

            $thismonth = mb_substr($dateYmd, 4, 2);

            $thisyear = mb_substr($dateYmd, 0, 4);

            // get number of days in this month

            $daysthismonth = 0 == $thisyear % 4 ? $ldays_per_month[$thismonth] : $days_per_month[$thismonth];

            // how many weekdays like this one remain in the month?

            // 0=last one, 1=one more after this one, etc.

            $whichWeek = floor(($daysthismonth - $thisday) / 7);

            // find first repeat date

            $thismonth += $freq;

            if ($thismonth > 12) {
                $thisyear++;

                $thismonth -= 12;
            }

            // get weekday for last day of month

            $dowLast += date('w', mktime(3, 0, 0, $thismonth + 1, -1, $thisyear));

            if ($dowLast >= $dow) {
                // last weekday is in last week of this month

                $day = $daysinmonth - ($dowLast - $dow) - (7 * $whichWeek);
            } else {
                // last weekday is NOT in last week of this month

                $day = $daysinmonth - ($dowLast - $dow) - (7 * ($whichWeek + 1));
            }

            $cdate = mktime(3, 0, 0, $thismonth, $day, $thisyear);

            while ($cdate <= $realend + $ONE_DAY) {
                if (!is_exception($cdate, $ex_days)) {
                    $ret[$n++] = $cdate;
                }

                $thismonth += $freq;

                if ($thismonth > 12) {
                    $thisyear++;

                    $thismonth -= 12;
                }

                // get weekday for last day of month

                $dowLast += date('w', mktime(3, 0, 0, $thismonth + 1, -1, $thisyear));

                if ($dowLast >= $dow) {
                    // last weekday is in last week of this month

                    $day = $daysinmonth - ($dowLast - $dow) - (7 * $whichWeek);
                } else {
                    // last weekday is NOT in last week of this month

                    $day = $daysinmonth - ($dowLast - $dow) - (7 * ($whichWeek + 1));
                }

                $cdate = mktime(3, 0, 0, $thismonth, $day, $thisyear);
            }
        } elseif ('monthlyByDate' == $rpt_type) {
            $thismonth = mb_substr($dateYmd, 4, 2);

            $thisyear = mb_substr($dateYmd, 0, 4);

            $thisday = mb_substr($dateYmd, 6, 2);

            $hour = date('H', $date);

            $minute = date('i', $date);

            $thismonth += $freq;

            $cdate = mktime(3, 0, 0, $thismonth, $thisday, $thisyear);

            while ($cdate <= $realend + $ONE_DAY) {
                if (!is_exception($cdate, $ex_days)) {
                    $ret[$n++] = $cdate;
                }

                $thismonth += $freq;

                $cdate = mktime(3, 0, 0, $thismonth, $thisday, $thisyear);
            }
        } elseif ('yearly' == $rpt_type) {
            $thismonth = mb_substr($dateYmd, 4, 2);

            $thisyear = mb_substr($dateYmd, 0, 4);

            $thisday = mb_substr($dateYmd, 6, 2);

            $hour = date('H', $date);

            $minute = date('i', $date);

            $thisyear += $freq;

            $cdate = mktime(3, 0, 0, $thismonth, $thisday, $thisyear);

            while ($cdate <= $realend + $ONE_DAY) {
                if (!is_exception($cdate, $ex_days)) {
                    $ret[$n++] = $cdate;
                }

                $thisyear += $freq;

                $cdate = mktime(3, 0, 0, $thismonth, $thisday, $thisyear);
            }
        }
    }

    return $ret;
}

/* Get all the repeating events for the specified data and return them
** in an array (which is sorted by time of day).
** params:
**   $user - userid
**   $dateYmd - date to get events for in YYYYMMDD format
*/
function get_repeating_entries($user, $dateYmd)
{
    global $repeated_events;

    $n = 0;

    $ret = [];

    //echo count($repeated_events)."<br>";

    for ($i = 0, $iMax = count($repeated_events); $i < $iMax; $i++) {
        if (repeated_event_matches_date($repeated_events[$i], $dateYmd)) {
            // make sure this is not an exception date...

            $unixtime = date_to_epoch($dateYmd);

            if (!is_exception($unixtime, $repeated_events[$i]['exceptions'])) {
                $ret[$n++] = $repeated_events[$i];
            }
        }
    }

    return $ret;
}

/* Returns a boolean stating whether or not the event passed
** in will fall on the date passed.
*/
function repeated_event_matches_date($event, $dateYmd)
{
    global $days_per_month, $ldays_per_month, $ONE_DAY;

    // only repeat after the beginning, and if there is an end

    // before the end

    $date = date_to_epoch($dateYmd);

    $thisyear = mb_substr($dateYmd, 0, 4);

    $start = date_to_epoch($event['date']);

    $end = date_to_epoch($event['event_end']);

    $freq = $event['frequency'];

    $thismonth = mb_substr($dateYmd, 4, 2);

    if ($event['event_end'] && $dateYmd > date('Ymd', $end)) {
        return false;
    }

    if ($dateYmd <= date('Ymd', $start)) {
        return false;
    }

    $id = $event['id'];

    if ('daily' == $event['event_rpt_type']) {
        if ((floor(($date - $start) / $ONE_DAY) % $freq)) {
            return false;
        }

        return true;
    } elseif ('weekly' == $event['event_rpt_type']) {
        $dow = date('w', $date);

        $dow1 = date('w', $start);

        $isDay = mb_substr($event['event_repeaton_days'], $dow, 1);

        $wstart = $start - ($dow1 * $ONE_DAY);

        if (floor(($date - $wstart) / 604800) % $freq) {
            return false;
        }

        return (0 == strcmp($isDay, 'y'));
    } elseif ('monthlyByDay' == $event['event_rpt_type']) {
        $dowS = date('w', $start);

        $dow = date('w', $date);

        // do this comparison first in hopes of best performance

        if ($dowS != $dow) {
            return false;
        }

        $mthS = date('m', $start);

        $yrS = date('Y', $start);

        $dayS = floor(date('d', $start));

        $dowS1 = (date('w', $start - ($ONE_DAY * ($dayS - 1))) + 35) % 7;

        $days_in_first_weekS = (7 - $dowS1) % 7;

        $whichWeekS = floor(($dayS - $days_in_first_weekS) / 7);

        if ($dowS >= $dowS1 && $days_in_first_weekS) {
            $whichWeekS++;
        }

        //echo "dayS=$dayS;dowS=$dowS;dowS1=$dowS1;wWS=$whichWeekS<br>";

        $mth = date('m', $date);

        $yr = date('Y', $date);

        $day = date('d', $date);

        $dow1 = (date('w', $date - ($ONE_DAY * ($day - 1))) + 35) % 7;

        $days_in_first_week = (7 - $dow1) % 7;

        $whichWeek = floor(($day - $days_in_first_week) / 7);

        if ($dow >= $dow1 && $days_in_first_week) {
            $whichWeek++;
        }

        //echo "day=$day;dow=$dow;dow1=$dow1;wW=$whichWeek<br>";

        if ((($yr - $yrS) * 12 + $mth - $mthS) % $freq) {
            return false;
        }

        return ($whichWeek == $whichWeekS);
    } elseif ('monthlyByDayR' == $event['event_rpt_type']) {
        $dowS = date('w', $start);

        $dow = date('w', $date);

        // do this comparison first in hopes of best performance

        if ($dowS != $dow) {
            return false;
        }

        $dayS = ceil(date('d', $start));

        $mthS = ceil(date('m', $start));

        $yrS = date('Y', $start);

        $daysthismonthS = 0 == $mthS % 4 ? $ldays_per_month[$mthS] : $days_per_month[$mthS];

        $whichWeekS = floor(($daysthismonthS - $dayS) / 7);

        $day = ceil(date('d', $date));

        $mth = ceil(date('m', $date));

        $yr = date('Y', $date);

        $daysthismonth = 0 == $mth % 4 ? $ldays_per_month[$mth] : $days_per_month[$mth];

        $whichWeek = floor(($daysthismonth - $day) / 7);

        if ((($yr - $yrS) * 12 + $mth - $mthS) % $freq) {
            return false;
        }

        return ($whichWeekS == $whichWeek);
    } elseif ('monthlyByDate' == $event['event_rpt_type']) {
        $mthS = date('m', $start);

        $yrS = date('Y', $start);

        $mth = date('m', $date);

        $yr = date('Y', $date);

        if ((($yr - $yrS) * 12 + $mth - $mthS) % $freq) {
            return false;
        }

        return (date('d', $date) == date('d', $start));
    } elseif ('yearly' == $event['event_rpt_type']) {
        $yrS = date('Y', $start);

        $yr = date('Y', $date);

        if (($yr - $yrS) % $freq) {
            return false;
        }

        return (date('dm', $date) == date('dm', $start));
    }

    // unknown repeat type

    return false;

    return false;
}

function date_to_epoch($d)
{
    return mktime(
        3,
        0,
        0,
        mb_substr($d, 4, 2),
        mb_substr($d, 6, 2),
        mb_substr($d, 0, 4)
    );
}

/* check if a date is an exception for an event
** $date - date in timestamp format
** $exdays - array of dates in YYYYMMDD format
*/
function is_exception($date, $ex_days)
{
    $size = count($ex_days);

    $count = 0;

    $date = date('Ymd', $date);

    //echo "Exception $date check.. count is $size <br>";

    while ($count < $size) {
        //echo "Exception date: $ex_days[$count] <br>";

        if ($date == $ex_days[$count++]) {
            return true;
        }
    }

    return false;
}

/* Get the Sunday of the week that the specified date is in.
** (If the date specified is a Sunday, then that date is returned.)
*/
function get_sunday_before($year, $month, $day)
{
    $weekday = date('w', mktime(3, 0, 0, $month, $day, $year));

    $newdate = mktime(3, 0, 0, $month, $day - $weekday, $year);

    return $newdate;
}

// Get the Monday of the week that the specified date is in.
// (If the date specified is a Monday, then that date is returned.)
function get_monday_before($year, $month, $day)
{
    $weekday = date('w', mktime(3, 0, 0, $month, $day, $year));

    if (0 == $weekday) {
        return mktime(3, 0, 0, $month, $day - 6, $year);
    }

    if (1 == $weekday) {
        return mktime(3, 0, 0, $month, $day, $year);
    }

    return mktime(3, 0, 0, $month, $day - ($weekday - 1), $year);
}

// Returns week number for specified date
// depending from week numbering settings.
// params:
//   $date - date in UNIX time format
function week_number($date)
{
    $ret = '';

    if ('1' == $GLOBALS['WEEK_START']) {
        $ret = strftime('%V', $date); // ISO Weeks -- which start on Mondays
        if ('' == $ret) { // %V not implemented on older versions of PHP :-(
            $ret = strftime('%W', $date);
        } // not 100%
    } else {
        $ret = strftime('%W', $date);
    }

    return $ret;
}

/*
** Fill in global var $agendax (array) all the calendar entries for the specified user for the
** specified date.  If we are displaying data from someone other than
** the logged in user, then check the access permission of the entry.
** params:
**   $date - date in YYYYMMDD format
**   $user - userid
*/
function fill_date_entries($date, $user, $show_mode)
{
    global $events, $agendax;

    global $agendax_url;

    $cnt = 0;

    $year = mb_substr($date, 0, 4);

    $month = mb_substr($date, 4, 2);

    $day = mb_substr($date, 6, 2);

    $dateu = mktime(3, 0, 0, $month, $day, $year);

    // get all the repeating events for this date and store in array $rep

    $rep = get_repeating_entries($user, $date);

    $cur_rep = 0;

    // get all the non-repeating events for this date and store in $ev

    $ev = get_entries($user, $date);

    for ($i = 0, $iMax = count($ev); $i < $iMax; $i++) {
        $is_repeating_event = ('R' == $ev[$i]['type']) ? true : false;

        // print out any repeating events that are before this one...

        while ($cur_rep < count($rep) && $rep[$cur_rep]['time'] < $ev[$i]['time']) {
            if (!empty($rep[$cur_rep]['ext_for_id'])) {
                $viewid = $rep[$cur_rep]['ext_for_id'];

                $viewname = $rep[$cur_rep]['title'] . ' (' . _('cont.') . ')';
            } else {
                $viewid = $rep[$cur_rep]['id'];

                $viewname = $rep[$cur_rep]['title'];
            }

            switch ($show_mode) {
                case AGENDAX_SHOW_BRIEF:
                {
                    store_entry_to_agendax_brief($viewid, $viewname, $date, $rep[$cur_rep]['cat'], $rep[$cur_rep]['cat_name'], $rep[$cur_rep]['description'], $is_repeating_event);
                    break;
                }
                case AGENDAX_SHOW_COMPLET:
                {
                    store_entry_to_agendax_complet(
                        $viewid,
                        $viewname,
                        $date,
                        $rep[$cur_rep]['description'],
                        $rep[$cur_rep]['contact'],
                        $rep[$cur_rep]['email'],
                        $rep[$cur_rep]['url'],
                        $rep[$cur_rep]['picture'],
                        $is_repeating_event
                    );
                    break;
                }
            }

            $cnt++;

            $cur_rep++;
        }

        if (!empty($ev[$i]['ext_for_id'])) {
            $viewid = $ev[$i]['ext_for_id'];

            $viewname = $ev[$i]['title'] . ' (' . _('cont.') . ')';
        } else {
            $viewid = $ev[$i]['id'];

            $viewname = $ev[$i]['title'];
        }

        switch ($show_mode) {
            case AGENDAX_SHOW_BRIEF:
            {
                store_entry_to_agendax_brief($viewid, $viewname, $date, $ev[$i]['cat'], $ev[$i]['cat_name'], $ev[$i]['description'], $is_repeating_event);
                break;
            }
            case AGENDAX_SHOW_COMPLET:
            {
                store_entry_to_agendax_complet(
                    $viewid,
                    $viewname,
                    $date,
                    $ev[$i]['description'],
                    $ev[$i]['contact'],
                    $ev[$i]['email'],
                    $ev[$i]['url'],
                    $ev[$i]['picture'],
                    $is_repeating_event
                );
                break;
            }
        }

        $cnt++;
    }

    // print out any remaining repeating events

    while ($cur_rep < count($rep)) {
        if (!empty($rep[$cur_rep]['ext_for_id'])) {
            $viewid = $rep[$cur_rep]['ext_for_id'];

            $viewname = $rep[$cur_rep]['title'] . ' (' . _('cont.') . ')';
        } else {
            $viewid = $rep[$cur_rep]['id'];

            $viewname = $rep[$cur_rep]['title'];
        }

        $is_repeating_event = true;

        switch ($show_mode) {
            case AGENDAX_SHOW_BRIEF:
            {
                store_entry_to_agendax_brief($viewid, $viewname, $date, $rep[$cur_rep]['cat'], $rep[$cur_rep]['cat_name'], $rep[$cur_rep]['description'], $is_repeating_event);
                break;
            }
            case AGENDAX_SHOW_COMPLET:
            {
                store_entry_to_agendax_complet(
                    $viewid,
                    $viewname,
                    $date,
                    $rep[$cur_rep]['description'],
                    $rep[$cur_rep]['contact'],
                    $rep[$cur_rep]['email'],
                    $rep[$cur_rep]['url'],
                    $rep[$cur_rep]['picture'],
                    $is_repeating_event
                );
                break;
            }
        }

        $cnt++;

        $cur_rep++;
    }
}

function store_entry_to_agendax_brief($eventid, $evtitle, $date, $catid, $catname, $desc, $is_repeating_event)
{
    global $agendax, $agendax_url;

    global $agendax_edit_permission_ok, $myts;

    $evtitle = htmlspecialchars($evtitle, ENT_QUOTES | ENT_HTML5);

    $desc = $myts->displayTarea($desc);

    if ($is_repeating_event) {
        $agendax['title'][] = '<a href="' . $agendax_url . '/?op=view&id=' . $eventid . '&on=' . $date . '">' . $evtitle . '</a>';
    } else {
        $agendax['title'][] = '<a href="' . $agendax_url . '/?op=view&id=' . $eventid . '">' . $evtitle . '</a>';
    }

    $agendax['edate'][] = agendax_showdate($date);

    $agendax['category'][] = _('Category');

    $agendax['categoryName'][] = "<a href=\"$agendax_url/index.php?op=cat&id=" . $catid . '">' . $catname . '</a>';

    // stript out all html tags

    $desc = preg_replace('/<.+?>/', '', $desc);

    //        $desc = str_replace("<br>", " ", $desc);

    $agendax['description'][] = mb_substr($desc, 0, 80) . ' ... ';

    if ($is_repeating_event) {
        $agendax['readmore'][] = '<a href="' . $agendax_url . '/?op=view&id=' . $eventid . '&on=' . $date . '">' . _('Read more') . '</a>';
    } else {
        $agendax['readmore'][] = '<a href="' . $agendax_url . '/?op=view&id=' . $eventid . '">' . _('Read more') . '</a>';
    }
}

function store_entry_to_agendax_complet($eventid, $evtitle, $date, $desc, $contact, $email, $url, $pic, $is_repeating_event)
{
    global $agendax, $agendax_url;

    global $agendax_edit_permission_ok, $myts;

    $evtitle = htmlspecialchars($evtitle, ENT_QUOTES | ENT_HTML5);

    $desc = $myts->displayTarea($desc);

    $contact = htmlspecialchars($contact, ENT_QUOTES | ENT_HTML5);

    $agendax['edate'][] = agendax_showdate($date);

    if ($agendax_edit_permission_ok) {
        $adminmenu = '<a href="' . $agendax_url . '/?op=edit&id=' . $eventid . '">' . _('Edit') . '</a>';

        if ($is_repeating_event) {
            $adminmenu = '<a href="' . $agendax_url . '/?op=edit&id=' . $eventid . '&on=' . $date . '&override=1">' . _('Edit') . '</a> | <a href="' . $agendax_url . '/?op=edit&id=' . $eventid . '">' . _('Repeating event edit') . '</a>';
        }

        $agendax['adminmenu'][] = $adminmenu;
    }

    if ($is_repeating_event) {
        $agendax['title'][] = '<a href="' . $agendax_url . '/?op=view&id=' . $eventid . '&on=' . $date . '">' . $evtitle . '</a>';
    } else {
        $agendax['title'][] = '<a href="' . $agendax_url . '/?op=view&id=' . $eventid . '">' . $evtitle . '</a>';
    }

    $agendax['description'][] = $desc;

    $agendax['contact'][] = '<a href="mailto:' . $email . '">' . $contact . '</a>';

    $agendax['url'][] = '<a href="http://' . $url . '">' . $url . '</a>';

    $agendax['picture'][] = '<img src="' . XOOPS_URL . '/uploads/' . $pic . '">';
}

// check to see if two events overlap
// time1 and time2 should be an integer like 235900
// duration1 and duration2 are integers in minutes
function times_overlap($time1, $duration1, $time2, $duration2)
{
    $hour1 = (int)($time1 / 10000);

    $min1 = ($time1 / 100) % 100;

    $hour2 = (int)($time2 / 10000);

    $min2 = ($time2 / 100) % 100;

    // convert to minutes since midnight

    // remove 1 minute from duration so 9AM-10AM will not conflict with 10AM-11AM

    if ($duration1 > 0) {
        $duration1 -= 1;
    }

    if ($duration2 > 0) {
        $duration2 -= 1;
    }

    $tmins1start = $hour1 * 60 + $min1;

    $tmins1end = $tmins1start + $duration1;

    $tmins2start = $hour2 * 60 + $min2;

    $tmins2end = $tmins2start + $duration2;

    //echo "tmins1start=$tmins1start, tmins1end=$tmins1end, tmins2start=$tmins2start, tmins2end=$tmins2end<br>";

    if (($tmins1start >= $tmins2end) || ($tmins2start >= $tmins1end)) {
        return false;
    }

    return true;
}

// Check for conflicts.
// Find overlaps between an array of dates and the other dates in the database.
// $date is an array of dates in Ymd format that is check for overlaps.
// the $duration, $hour, and $minute are integers that show the time of
// the event which is shared among the dates.
// $particpants are those whose calendars are to be checked.
// $login is the current user name.
// $id is the current calendar entry being checked if it has been stored before
// (this keeps overlaps from wrongly checking an event against itself.
// TODO: Update this to handle exceptions to repeating events
//
// Appt limits: if enabled we will store each event in an array using
// the key $user-$date, so for testuser on 12/31/95
// we would use $evtcnt["testuser-19951231"]
//
// Return empty string for no conflicts or return the HTML of the
// conflicts when one or more are found.
function check_for_conflicts($dates, $duration, $hour, $minute, $participants, $login, $id)
{
    //  global $single_user_login, $single_user;
    //  global $repeated_events, $limit_appts, $limit_appts_number;
    //  if (!count($dates)) return false;
    //
    //  $evtcnt = array ();
    //
    //  $sql = "SELECT distinct webcal_entry_user.cal_login, webcal_entry.cal_time," .
    //    "webcal_entry.cal_duration, webcal_entry.cal_name, " .
    //    "webcal_entry.cal_id, webcal_entry.cal_ext_for_id, " .
    //    "webcal_entry.cal_access, " .
    //    "webcal_entry_user.cal_status, webcal_entry.cal_date " .
    //    "FROM webcal_entry, webcal_entry_user " .
    //    "WHERE webcal_entry.cal_id = webcal_entry_user.cal_id " .
    //    "AND (";
    //  for ($x = 0; $x < count($dates); $x++) {
    //    if ($x != 0) $sql .= " OR ";
    //    $sql.="webcal_entry.cal_date = " . date ( "Ymd", $dates[$x] );
    //  }
    //  $sql .=  ") AND webcal_entry.cal_time >= 0 " .
    //    "AND webcal_entry_user.cal_status IN ('A','W') AND ( ";
    //  if ( $single_user == "Y" ) {
    //     $participants[0] = $single_user_login;
    //  } else if ( strlen ( $participants[0] ) == 0 ) {
    //     // likely called from a form with 1 user
    //     $participants[0] = $login;
    //  }
    //  for ( $i = 0; $i < count ( $participants ); $i++ ) {
    //    if ( $i > 0 )
    //      $sql .= " OR ";
    //    $sql .= " webcal_entry_user.cal_login = '" . $participants[$i] . "'";
    //  }
    //  $sql .= " )";
    //  // make sure we don't get something past the end date of the
    //  // event we are saving.
    //  //echo "SQL: $sql<P>";
    //  $conflicts = "";
    //  $res = dbi_query ( $sql );
    //  $found = array();
    //  $count = 0;
    //  if ( $res ) {
    //    $time1 = sprintf ( "%d%02d00", $hour, $minute );
    //    $duration1 = sprintf ( "%d", $duration );
    //    while ( $row = dbi_fetch_row ( $res ) ) {
    //      //Add to an array to see if it has been found already for the next part.
    //      $found[$count++] = $row[4];
    //      // see if either event overlaps one another
    //      if ( $row[4] != $id && ( empty ( $row[5] ) || $row[5] != $id ) ) {
    //        $time2 = $row[1];
    //        $duration2 = $row[2];
    //        $cntkey = $user . "-" . $row[8];
    //        $evtcnt[$cntkey]++;
    //        $over_limit = 0;
    //        if ( $limit_appts == "Y" && $limit_appts_number > 0
    //          && $evtcnt[$cntkey] >= $limit_appts_number ) {
    //          $over_limit = 1;
    //        }
    //        if ( $over_limit ||
    //          times_overlap ( $time1, $duration1, $time2, $duration2 ) ) {
    //          $conflicts .= "<LI>";
    //          if ( $single_user != "Y" )
    //            $conflicts .= "$row[0]: ";
    //          if ( $row[6] == 'R' && $row[0] != $login )
    //            $conflicts .=  "(" . translate("Private") . ")";
    //          else {
    //            $conflicts .=  "<A HREF=\"view_entry.php?id=$row[4]";
    //            if ( $user != $login )
    //              $conflicts .= "&user=$user";
    //            $conflicts .= "\">$row[3]</A>";
    //          }
    //          if ( $duration2 == ( 24 * 60 ) ) {
    //            $conflicts .= " (" . translate("All day event") . ")";
    //          } else {
    //            $conflicts .= " (" . display_time ( $time2 );
    //            if ( $duration2 > 0 )
    //              $conflicts .= "-" .
    //                display_time ( add_duration ( $time2, $duration2 ) );
    //            $conflicts .= ")";
    //          }
    //          $conflicts .= " on " . date_to_str( $row[8] );
    //          if ( $over_limit ) {
    //            $tmp = translate ( "exceeds limit of XXX events per day" );
    //            $tmp = str_replace ( "XXX", $limit_appts_number, $tmp );
    //            $conflicts .= " (" . $tmp . ")";
    //          }
    //        }
    //      }
    //    }
    //    dbi_free_result ( $res );
    //  } else {
    //    echo translate("Database error") . ": " . dbi_error (); exit;
    //  }
    //
    //
    //  //echo "<br>hello";
    //
    //  for ($q=0;$q<count($participants);$q++) {
    //    $time1 = sprintf ( "%d%02d00", $hour, $minute );
    //    $duration1 = sprintf ( "%d", $duration );
    //    //This date filter is not necessary for functional reasons, but it eliminates some of the
    //    //events that couldn't possibly match.  This could be made much more complex to put more
    //    //of the searching work onto the database server, or it could be dropped all together to put
    //    //the searching work onto the client.
    //    $date_filter  = "AND (webcal_entry.cal_date <= " . date("Ymd",$dates[count($dates)-1]);
    //    $date_filter .= " AND (webcal_entry_repeats.cal_end IS NULL OR webcal_entry_repeats.cal_end >= " . date("Ymd",$dates[0]) . "))";
    //    //Read repeated events for the participants only once for a participant for
    //    //for performance reasons.
    //    $repeated_events=query_events($participants[$q],true,$date_filter);
    //    //for ($dd=0; $dd<count($repeated_events); $dd++) {
    //    //  echo $repeated_events[$dd]['cal_id'] . "<br>";
    //    //}
    //    for ($i=0; $i < count($dates); $i++) {
    //      $dateYmd = date ( "Ymd", $dates[$i] );
    //      $list = get_repeating_entries($participants[$q],$dateYmd);
    //      $thisyear = substr($dateYmd, 0, 4);
    //      $thismonth = substr($dateYmd, 4, 2);
    //      for ($j=0; $j < count($list);$j++) {
    //        //okay we've narrowed it down to a day, now I just gotta check the time...
    //        //I hope this is right...
    //        $row = $list[$j];
    //        if ( $row['cal_id'] != $id && ( empty ( $row['cal_ext_for_id'] ) ||
    //          $row['cal_ext_for_id'] != $id ) ) {
    //          $time2 = $row['cal_time'];
    //          $duration2 = $row['cal_duration'];
    //          if ( times_overlap ( $time1, $duration1, $time2, $duration2 ) ) {
    //            $conflicts .= "<LI>";
    //            if ( $single_user != "Y" )
    //              $conflicts .= $row['cal_login'] . ": ";
    //            if ( $row['cal_access'] == 'R' && $row['cal_login'] != $login )
    //              $conflicts .=  "(" . translate("Private") . ")";
    //            else {
    //              $conflicts .=  "<A HREF=\"view_entry.php?id=" . $row['cal_id'];
    //              if ( $user != $login )
    //                $conflicts .= "&user=$user";
    //              $conflicts .= "\">" . $row['cal_name'] . "</A>";
    //            }
    //            $conflicts .= " (" . display_time ( $time2 );
    //            if ( $duration2 > 0 )
    //              $conflicts .= "-" .
    //                display_time ( add_duration ( $time2, $duration2 ) );
    //            $conflicts .= ")";
    //            $conflicts .= " on " . date("l, F j, Y", $dates[$i]);
    //          }
    //        }
    //      }
    //    }
    //  }
    //
    //  return $conflicts;
}

// Convert a time format HHMMSS (like 131000 for 1PM) into number of
// minutes past midnight.
function time_to_minutes($time)
{
    $h = (int)($time / 10000);

    $m = (int)($time / 100) % 100;

    $num = $h * 60 + $m;

    return $num;
}

// Calculate which row/slot this time represents.
// $time is input time in YYMMDD format
// $round_down indicates if we should change 1100 to 1059 so a
// 10AM-11AM appt just shows up in the 10AM slot, not the 11AM slot also.
function calc_time_slot($time, $round_down = false)
{
    global $TIME_SLOTS, $TZ_OFFSET;

    $interval = (24 * 60) / $TIME_SLOTS;

    $mins_since_midnight = time_to_minutes($time);

    $ret = (int)($mins_since_midnight / $interval);

    if ($round_down) {
        if ($ret * $interval == $mins_since_midnight) {
            $ret--;
        }
    }

    if ($ret > $TIME_SLOTS) {
        $ret = $TIME_SLOTS;
    }

    return $ret;
}

function display_time($time, $ignore_offset = 0)
{
    global $TZ_OFFSET;

    $hour = (int)($time / 10000);

    if (!$ignore_offset) {
        $hour += $TZ_OFFSET;
    }

    while ($hour < 0) {
        $hour += 24;
    }

    while ($hour > 23) {
        $hour -= 24;
    }

    $min = ($time / 100) % 100;

    if ('12' == $GLOBALS['TIME_FORMAT']) {
        $ampm = ($hour >= 12) ? _('pm') : _('am');

        $hour %= 12;

        if (0 == $hour) {
            $hour = 12;
        }

        $ret = sprintf('%d:%02d%s', $hour, $min, $ampm);
    } else {
        $ret = sprintf('%d:%02d', $hour, $min);
    }

    return $ret;
}

// Return the full month name
// params:
//   $m - month (0-11)
function month_name($m)
{
    switch ($m) {
        case 0:
            return _('January');
        case 1:
            return _('February');
        case 2:
            return _('March');
        case 3:
            return _('April');
        case 4:
            return _('May_'); // needs to be different than "May"
        case 5:
            return _('June');
        case 6:
            return _('July');
        case 7:
            return _('August');
        case 8:
            return _('September');
        case 9:
            return _('October');
        case 10:
            return _('November');
        case 11:
            return _('December');
    }

    return "unknown-month($m)";
}

// Return the abbreviated month name
// params:
//   $m - month (0-11)
function month_short_name($m)
{
    switch ($m) {
        case 0:
            return _('Jan');
        case 1:
            return _('Feb');
        case 2:
            return _('Mar');
        case 3:
            return _('Apr');
        case 4:
            return _('May');
        case 5:
            return _('Jun');
        case 6:
            return _('Jul');
        case 7:
            return _('Aug');
        case 8:
            return _('Sep');
        case 9:
            return _('Oct');
        case 10:
            return _('Nov');
        case 11:
            return _('Dec');
    }

    return "unknown-month($m)";
}

// Return the full weekday name
// params:
//   $w - weekday (0=Sunday,...,6=Saturday)
function weekday_name($w)
{
    switch ($w) {
        case 0:
            return _('Sunday');
        case 1:
            return _('Monday');
        case 2:
            return _('Tuesday');
        case 3:
            return _('Wednesday');
        case 4:
            return _('Thursday');
        case 5:
            return _('Friday');
        case 6:
            return _('Saturday');
    }

    return "unknown-weekday($w)";
}

// Return the abbreviated weekday name
// params:
//   $w - weekday (0=Sun,...,6=Sat)
function weekday_short_name($w)
{
    switch ($w) {
        case 0:
            return _('Sun');
        case 1:
            return _('Mon');
        case 2:
            return _('Tue');
        case 3:
            return _('Wed');
        case 4:
            return _('Thu');
        case 5:
            return _('Fri');
        case 6:
            return _('Sat');
    }

    return "unknown-weekday($w)";
}

// convert a date from an int format "19991231" into
// "Friday, December 31, 1999", "Friday, 12-31-1999" or whatever format
// the user prefers.
function date_to_str($indate, $format = '', $show_weekday = true, $short_months = false, $server_time = '')
{
    global $DATE_FORMAT, $TZ_OFFSET;

    if (0 == mb_strlen($indate)) {
        $indate = date('Ymd');
    }

    $newdate = $indate;

    if ('' != $server_time && $server_time >= 0) {
        $y = mb_substr($indate, 0, 4);

        $m = mb_substr($indate, 4, 2);

        $d = mb_substr($indate, 6, 2);

        if ($server_time + $TZ_OFFSET * 10000 > 240000) {
            $newdate = date('Ymd', mktime(3, 0, 0, $m, $d + 1, $y));
        } elseif ($server_time + $TZ_OFFSET * 10000 < 0) {
            $newdate = date('Ymd', mktime(3, 0, 0, $m, $d - 1, $y));
        }
    }

    // if they have not set a preference yet...

    if ('' == $DATE_FORMAT) {
        $DATE_FORMAT = '__month__ __dd__, __yyyy__';
    }

    if (empty($format)) {
        $format = $DATE_FORMAT;
    }

    $y = (int)($newdate / 10000);

    $m = (int)($newdate / 100) % 100;

    $d = $newdate % 100;

    $date = mktime(3, 0, 0, $m, $d, $y);

    $wday = strftime('%w', $date);

    if ($short_months) {
        $weekday = weekday_short_name($wday);

        $month = month_short_name($m - 1);
    } else {
        $weekday = weekday_name($wday);

        $month = month_name($m - 1);
    }

    $yyyy = $y;

    $yy = sprintf('%02d', $y %= 100);

    $ret = $format;

    $ret = str_replace('__yyyy__', $yyyy, $ret);

    $ret = str_replace('__yy__', $yy, $ret);

    $ret = str_replace('__month__', $month, $ret);

    $ret = str_replace('__mon__', $month, $ret);

    $ret = str_replace('__dd__', $d, $ret);

    $ret = str_replace('__mm__', $m, $ret);

    if ($show_weekday) {
        return "$weekday, $ret";
    }

    return $ret;
}

// Define an array to use to jumble up the key
$offsets = [31, 41, 59, 26, 54];

function hextoint($val)
{
    if (empty($val)) {
        return 0;
    }

    switch (mb_strtoupper($val)) {
        case '0':
            return 0;
        case '1':
            return 1;
        case '2':
            return 2;
        case '3':
            return 3;
        case '4':
            return 4;
        case '5':
            return 5;
        case '6':
            return 6;
        case '7':
            return 7;
        case '8':
            return 8;
        case '9':
            return 9;
        case 'A':
            return 10;
        case 'B':
            return 11;
        case 'C':
            return 12;
        case 'D':
            return 13;
        case 'E':
            return 14;
        case 'F':
            return 15;
    }

    return 0;
}

// an implementatin of array_splice() for PHP3
//   test cases:
//     insert an element
//       array_splice($array,$offset,0,array($item));
//     delete an element
//       array_splice($array,$offset,1);
function my_array_splice(&$input, $offset, $length, $replacement)
{
    if (floor(phpversion()) < 4) {
        // if offset is negative, then it starts at the end of array

        if ($offset < 0) {
            $offset = count($input) + $offset;
        }

        for ($i = 0; $i < $offset; $i++) {
            $new_array[] = $input[$i];
        }

        // if we have a replacement, insert it

        for ($i = 0, $iMax = count($replacement); $i < $iMax; $i++) {
            $new_array[] = $replacement[$i];
        }

        // now tack on the rest of the original array

        for ($i = $offset + $length, $iMax = count($input); $i < $iMax; $i++) {
            $new_array[] = $input[$i];
        }

        $input = $new_array;
    } else {
        array_splice($input, $offset, $length, $replacement);
    }
}

// Convert HTML entities in 8bit
// Only supported for PHP4 (not PHP3)
function html_to_8bits($html)
{
    if (floor(phpversion()) < 4) {
        return $html;
    }

    return strtr(
        $html,
        array_flip(
            get_html_translation_table(HTML_ENTITIES)
        )
    );
}

function agendax_read_events($date, $cat_id = '', $userid = 0)
{
    return read_events($date, $date, $cat_id, $userid);
}
