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

require_once XOOPS_ROOT_PATH . '/modules/agendax/minical.inc.php';

function minical_show($options)
{
    //
    // Show - a mini-calendar, days with events are highlighted
    //      - an image, could be dynamic by supplying an php script
    //      - a random event for the current day if any
    //      - if not a random event from next day to up to 2 month
    //
    //    global $xoopsDB, $xoopsConfig;
    require XOOPS_ROOT_PATH . '/modules/agendax/config.inc.php';
    $block = [];

    // Minical Image showing
    $mc_showrandomevent = ($options[0] == 1) ? true : false;
    $mc_showimage       = ($options[1] == 1) ? true : false;
    $photo_sponsor_auto = ($options[2] == 1) ? true : false;
    $mc_sponsor         = $options[3];
    $mcalurl            = $options[4];

    if ($mc_showimage) {
        // if matches ^http:// then full image src url
        if (preg_match('|^http://|i', $mcalurl) > 0) {
            $mi_type = 'full';
            // if matches .php$ then php script
        } elseif (preg_match('|\.php$|i', $mcalurl) > 0) {
            $mi_type = 'php';
            // otherwise see if it is a local directory for random image show
        } elseif ($dir = @opendir(XOOPS_ROOT_PATH . '/' . $mcalurl)) {
            while ($item = readdir($dir)) {
                if ($item == '.' or $item == '..') {
                    continue;
                }
                $ext = strrchr($item, '.');
                //It will return the extension with the . on it (ex: ".jpg")
                if (in_array(substr($ext, 1), $extensions)) {
                    $images[] = $item;
                }
            }
            closedir($dir);
            if (@count($images) >= 1) {
                $mi_type = 'random';
            } else {
                $mi_type = 'default';
            }
            // default show Agenda-X Logo
        } else {
            $mi_type = 'default';
        }

        switch ($mi_type) {
            case 'php' :
            {
                ob_start();
                require XOOPS_ROOT_PATH . '/modules/agendax/script/' . $mcalurl;
                $mc_url = ob_get_contents();
                ob_end_clean();
                break;
            }

            case 'full' :
            {
                $mc_url = $mcalurl;
                break;
            }

            case 'random' :
            {
                $mc_url = XOOPS_URL . '/' . $mcalurl . '/' . $images[rand(0, count($images) - 1)];
                break;
            }

            default :
            {
                $mc_url = XOOPS_URL . '/modules/agendax/agendax_slogo.png';
                break;
            }
        }

        // based on $mc_url we will determine the "photo sponsor" and link to it
        if ($photo_sponsor_auto == 'true') {
            $parsed                 = parse_url($mc_url);
            $photo_sponsor          = @$parsed['host'] ?: 'www.China-Offshore.com';
            $block['photo_sponsor'] = '<small>' . _AGX_PHOTO_BY . ': ' . '</small><a href="http://' . $photo_sponsor . '"><small>' . preg_replace('/^www\./', '', $photo_sponsor) . '</small></a><br>';
        } else {
            $block['photo_sponsor'] = '';
        }
        $block['image'] = '<a href="' . $mc_sponsor . '"><img src="' . $mc_url . '"></a><br>';
        // show a furture day event, randomly
    }

    if ($mc_showrandomevent) {
        srand((float)microtime() * 12345678);
        $eventsArray = get_FurtureDays_events();
        if ($eventsArray) {
            $rand_keys = array_rand($eventsArray, 1);
            $eventdate = $eventsArray[$rand_keys]['date'];

            $d = (int)substr($eventdate, 6, 2);
            $m = (int)substr($eventdate, 4, 2);
            $y = (int)substr($eventdate, 0, 4);

            $id    = $eventsArray[$rand_keys]['id'];
            $title = $eventsArray[$rand_keys]['title'];

            if (_SHORTDATESTRING == 'n/j/Y') {
                $date = $m . '.' . $d . '.' . $y;
            } elseif (_SHORTDATESTRING == 'j/n/Y') {
                $date = $da . '.' . $m . '.' . $y;
            } else {          //default to "Y/n/j"

                $date = $y . '.' . $m . '.' . $d;
            }

            if ($eventsArray[$rand_keys]['repeat'] == 'no') {
                $block['randEvent'] = '<a href="' . XOOPS_URL . "/modules/agendax/index.php?op=view&id=$id" . '"><small>' . $title . " ($date)" . '</small></a>';
            } else {
                $block['randEvent'] = '<a href="' . XOOPS_URL . "/modules/agendax/index.php?op=view&id=$id" . '"><small>' . $title . ' (' . _AGX_RECURRING . ')' . '</small></a>';
            }
        }
    }
    $block['postNewEvent'] = '<a href="' . XOOPS_URL . '/modules/agendax/index.php?op=eventform"><small>' . _AGX_POSTANEWEVENT . '</small></a>';

    // minicalendar
    ob_start();
    display_minical($block['image'], &$block['photo_sponsor'], &$block['randEvent']);
    $block['contents'] = ob_get_contents();
    ob_end_clean();

    return $block;
}

function thisWeek_show()
{
    $block       = [];
    $eventsArray = get_thisWeek_events();
    if ($eventsArray) {
        foreach ($eventsArray as $event) {
            $eventdate = $event['date'];
            $d         = (int)substr($eventdate, 6, 2);
            $m         = (int)substr($eventdate, 4, 2);
            $y         = (int)substr($eventdate, 0, 4);
            $id        = $event['id'];
            $title     = $event['title'];
            if (_SHORTDATESTRING == 'n/j/Y') {
                $date = $m . '.' . $d . '.' . $y;
            } elseif (_SHORTDATESTRING == 'j/n/Y') {
                $date = $da . '.' . $m . '.' . $y;
            } else {          //default to "Y/n/j"

                $date = $y . '.' . $m . '.' . $d;
            }
            if ($event['repeat'] == 'no') {
                $block['contents'][] = '<a href="' . XOOPS_URL . "/modules/agendax/index.php?op=view&id=$id" . '">' . $title . " ($date)" . '</a>';
            } else {
                $block['contents'][] = '<a href="' . XOOPS_URL . "/modules/agendax/index.php?op=view&id=$id" . '">' . $title . ' (' . _AGX_RECURRING . ')' . '</a>';
            }
        }
    }
    return $block;
}

function thisMonth_show()
{
    $block       = [];
    $eventsArray = get_thisMonth_events();
    if ($eventsArray) {
        foreach ($eventsArray as $event) {
            $eventdate = $event['date'];
            $d         = (int)substr($eventdate, 6, 2);
            $m         = (int)substr($eventdate, 4, 2);
            $y         = (int)substr($eventdate, 0, 4);
            $id        = $event['id'];
            $title     = $event['title'];
            if (_SHORTDATESTRING == 'n/j/Y') {
                $date = $m . '.' . $d . '.' . $y;
            } elseif (_SHORTDATESTRING == 'j/n/Y') {
                $date = $da . '.' . $m . '.' . $y;
            } else {          //default to "Y/n/j"

                $date = $y . '.' . $m . '.' . $d;
            }
            if ($event['repeat'] == 'no') {
                $block['contents'][] = '<a href="' . XOOPS_URL . "/modules/agendax/index.php?op=view&id=$id" . '">' . $title . " ($date)" . '</a>';
            } else {
                $block['contents'][] = '<a href="' . XOOPS_URL . "/modules/agendax/index.php?op=view&id=$id" . '">' . $title . ' (' . _AGX_RECURRING . ')' . '</a>';
            }
        }
    }
    return $block;
}

/*
function agendax_getMcalUrl($id=0){
    global $xoopsDB;
    //query Database (returns an array)
    $result = $xoopsDB->queryF("SELECT mc_isscript, mc_url FROM ".$xoopsDB->prefix("agendax_mcalurl")." WHERE mc_id=$id");
    return $xoopsDB->fetchArray($result);
}
*/

function get_FurtureDays_events()
{
    // events of future days,  up to 60 days
    global $xoopsDB;
    $ONE_DAY = 86400;

    $events = [];

    $userTimestamp = xoops_getUserTimestamp(time());

    $date        = date('Ymd', $userTimestamp);
    $date2months = date('Ymd', $userTimestamp + 60 * $ONE_DAY);

    // if there's events Today then return them;
    $query  = 'SELECT id, title, date FROM ' . $xoopsDB->prefix('agendax_events') . " WHERE approved=1 AND date=$date";
    $result = $xoopsDB->queryF($query);
    if ($xoopsDB->getRowsNum($result) > 0) {
        while (false !== ($row = $xoopsDB->fetchArray($result))) {
            $row['repeat'] = 'no';
            $events[]      = $row;
        }
        return $events;
    } else {
        // otherwise return all events from tomorrow to up to 2 month
        $query   = 'SELECT id, title, date FROM ' . $xoopsDB->prefix('agendax_events') . ' WHERE approved=1 AND ';
        $query   .= "type = 'E' AND ( date>$date AND date <=$date2months )";
        $result  = $xoopsDB->queryF($query);
        $numrows = $xoopsDB->getRowsNum($result);
        if ($numrows > 0) {
            while (false !== ($row = $xoopsDB->fetchArray($result))) {
                $row['repeat'] = 'no';
                $events[]      = $row;
            }
        }
        $query   = 'SELECT id, title, date FROM ' . $xoopsDB->prefix('agendax_events') . ' LEFT JOIN ' . $xoopsDB->prefix('agendax_event_repeats') . ' ON id=event_id' . " WHERE approved=1 AND type = 'R' AND (event_end=0 OR event_end is NULL OR event_end <=$date2months)";
        $result  = $xoopsDB->queryF($query);
        $numrows = $xoopsDB->getRowsNum($result);
        while (false !== ($row = $xoopsDB->fetchArray($result))) {
            $row['repeat'] = 'yes';
            $events[]      = $row;
        }
    }
    return $events;
}

function get_thisWeek_events()
{
    global $xoopsDB;
    $events  = [];
    $ONE_DAY = 86400;

    $userTimestamp = xoops_getUserTimestamp(time());

    $date      = date('Ymd', $userTimestamp);
    $date7days = date('Ymd', $userTimestamp + 7 * $ONE_DAY);

    $query   = 'SELECT id, title, date  FROM ' . $xoopsDB->prefix('agendax_events') . ' WHERE approved=1 AND ';
    $query   .= "type = 'E' AND ( date >= $date AND date < $date7days ";
    $query   .= ') ORDER BY date ASC';
    $result  = $xoopsDB->queryF($query);
    $numrows = $xoopsDB->getRowsNum($result);
    if ($numrows > 0) {
        while (false !== ($row = $xoopsDB->fetchArray($result))) {
            $row['repeat'] = 'no';
            $events[]      = $row;
        }
    }
    $query   = 'SELECT id, title, date FROM ' . $xoopsDB->prefix('agendax_events') . ' LEFT JOIN ' . $xoopsDB->prefix('agendax_event_repeats') . ' ON id=event_id' . " WHERE approved=1 AND type = 'R' AND (event_end=0 OR event_end is NULL OR event_end <=$date7days)";
    $result  = $xoopsDB->queryF($query);
    $numrows = $xoopsDB->getRowsNum($result);
    while (false !== ($row = $xoopsDB->fetchArray($result))) {
        $row['repeat'] = 'yes';
        $events[]      = $row;
    }

    return $events;
}

function get_thisMonth_events()
{
    global $xoopsDB;
    $events  = [];
    $ONE_DAY = 86400;

    $userTimestamp = xoops_getUserTimestamp(time());
    $date          = date('Ymd', $userTimestamp);
    $date30days    = date('Ymd', $userTimestamp + 30 * $ONE_DAY);

    $query   = 'SELECT id, title, date  FROM ' . $xoopsDB->prefix('agendax_events') . ' WHERE approved=1 AND ';
    $query   .= "type = 'E' AND ( date >= $date AND date < $date30days ) ORDER BY date ASC";
    $result  = $xoopsDB->queryF($query);
    $numrows = $xoopsDB->getRowsNum($result);
    if ($numrows > 0) {
        while (false !== ($row = $xoopsDB->fetchArray($result))) {
            $row['repeat'] = 'no';
            $events[]      = $row;
        }
    }
    $query = 'SELECT id, title, date FROM ' . $xoopsDB->prefix('agendax_events') . ' LEFT JOIN ' . $xoopsDB->prefix('agendax_event_repeats') . ' ON id=event_id' . " WHERE approved=1 AND type='R' AND (event_end=0 OR event_end is NULL OR event_end <=$date30days)";

    $result  = $xoopsDB->queryF($query);
    $numrows = $xoopsDB->getRowsNum($result);
    while (false !== ($row = $xoopsDB->fetchArray($result))) {
        $row['repeat'] = 'yes';
        $events[]      = $row;
    }

    return $events;
}

//function pending_approval_show() {
//    global $xoopsDB;
//
//    $query = 'SELECT COUNT (*) FROM '.$xoopsDB->prefix("agendax_events").' WHERE approved = 0';
//    $result = $xoopsDB->queryF($query);
//    $block['contents']['adminlink'] = XOOPS_URL."/modules/agendax/admin/index.php";
//	list($block['contents']['events_nb']) = $xoopsDB->fetchRow($result);
//	$block['contents']['lang_link'] = 'Agendax Events';
//
//	return $block;   
//}
function b_agx_mcal_opt_edit($options)
{
    $form = "\n<TABLE>";

    $form .= "\n<TR><TD>" . _MB_AGX_SHOW_TODAY_EVENT_IN_MINICAL . "</TD><TD>\n<input type='radio' name='options[0]' value='1'";
    if ($options[0] == 1) {
        $form .= ' checked';
    }
    $form .= '>&nbsp;' . _YES . "<input type='radio' name='options[0]' value='0'";
    if ($options[0] == 0) {
        $form .= ' checked';
    }
    $form .= '>&nbsp;' . _NO . "</TD></TR>\n";

    $form .= '<TR><TD>' . _MB_AGX_SHIMG . "</TD><TD>\n<input type='radio' name='options[1]' value='1'";
    if ($options[1] == 1) {
        $form .= ' checked';
    }
    $form .= '>&nbsp;' . _YES . "<input type='radio' name='options[1]' value='0'";
    if ($options[1] == 0) {
        $form .= ' checked';
    }
    $form .= '>&nbsp;' . _NO . "</TD></TR>\n";

    $form .= '<TR><TD>' . _MB_AGX_PHOTO_SPONSOR_AUTO . "</TD><TD>\n<input type='radio' name='options[2]' value='1'";
    if ($options[2] == 1) {
        $form .= ' checked';
    }
    $form .= '>&nbsp;' . _YES . "<input type='radio' name='options[2]' value='0'";
    if ($options[2] == 0) {
        $form .= ' checked';
    }
    $form .= '>&nbsp;' . _NO . "</TD></TR>\n";

    $form .= '<TR><TD>' . _MB_AGX_IMGLNK . "</TD><TD><input type='text' name='options[3]' value='" . $options[3] . "'></TD></TR>";
    $form .= '<TR><TD>' . _MB_AGX_IMAGEURL_OR_SCRIPTPATH . "</TD><TD><input type='text' name='options[4]' value='" . $options[4] . "'></TD></TR>";

    $form .= '</TABLE>';

    return $form;
}


