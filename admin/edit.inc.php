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

if (!preg_match('/index.php/', $HTTP_SERVER_VARS['PHP_SELF'])) {
    exit('Access Denied');
}

require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

# ----------------------------------------------------------------------
# edit event form
# ----------------------------------------------------------------------
$query = 'SELECT id, title, description, contact, url, email, picture, cat, date, time, duration, priority, type, access, submit_by,
                     cat_name,
                     event_rpt_type, event_end, frequency, event_repeaton_days                
              FROM ' . XOOPS_DB_PREFIX . '_agendax_events 
              LEFT JOIN ' . XOOPS_DB_PREFIX . '_agendax_cat 
              ON cat = cat_id 
              LEFT JOIN ' . XOOPS_DB_PREFIX . "_agendax_event_repeats 
              ON id = event_id
              WHERE id=$id";
$result = $GLOBALS['xoopsDB']->queryF($query);
$row = $GLOBALS['xoopsDB']->fetchObject($result);

require_once XOOPS_ROOT_PATH . '/class/xoopsform/formelement.php';
require_once XOOPS_ROOT_PATH . '/modules/agendax/include/agendaxformdate.php';

global $myts;  //text sanitisor;

$agendax['name'] = 'editform';
$agendax['action'] = "$agendax_url/index.php?op=upevent&id=$row->id";
$agendax['method'] = 'post';
$agendax['extra'] = 'enctype=multipart/form-data';

$agendax['title'] = _('Edit event');

$agendax['elements'][0]['caption'] = '';
$agendax['elements'][0]['body'] = '<input type="submit" value="' . _('Save event') . '">';

$agendax['elements'][1]['caption'] = _('Event title') . '*';
$agendax['elements'][1]['body'] = '<input class="text" type="text" name="title" value="' . htmlspecialchars($row->title, ENT_QUOTES | ENT_HTML5) . '"> ' . '(' . _('* denotes required') . ')';
$agendax['elements'][2]['caption'] = _('Description') . '*';
$agendax['elements'][2]['body'] = '<textarea class="comment" id="description" name="description" cols="40" rows="10">' . htmlspecialchars($row->description, ENT_QUOTES | ENT_HTML5) . '</textarea>' . "<script type=\"text/javascript\">
               var config = new HTMLArea.Config(); // create a new configuration object
                                                   // having all the default values
               config.width = '500px';
               config.height = '200px';

               HTMLArea.replace('description', config);
              </script>";

$agendax['elements'][3]['caption'] = _('Contact') . '*';
$agendax['elements'][3]['body'] = '<input class="text" type="text" name="contact" value="' . htmlspecialchars($row->contact, ENT_QUOTES | ENT_HTML5) . '"></input>';
$agendax['elements'][4]['caption'] = _('Email') . '*';
$agendax['elements'][4]['body'] = '<input class="text" type="text" name="email" value="' . $row->email . '">';

$agendax['elements'][5]['caption'] = _('URL');
$agendax['elements'][5]['body'] = '<input class="text" type="text" name="url" value="' . $row->url . '">';

$agendax['elements'][6]['caption'] = _('Picture');

$temp = '<input class="text" type="text" name="foto" value="' . $row->picture . '">';
$agendax['elements'][6]['body'] = $temp;

$agendax['elements'][7]['caption'] = _('Category');

$temp = '<select class="textBox2" name="cat">';

# get the categories
$query = 'select cat_id,cat_name from ' . XOOPS_DB_PREFIX . '_agendax_cat';
$result = $GLOBALS['xoopsDB']->queryF($query);
while (false !== ($rowc = $GLOBALS['xoopsDB']->fetchObject($result))) {
    $temp .= '<option value="' . $rowc->cat_id . '"';

    $temp .= ($rowc->cat_id == $row->cat) ? ' selected' : '';

    $temp .= '>' . $rowc->cat_name . '</option>';
}
$temp .= '</select>';
$agendax['elements'][7]['body'] = $temp;

$year = mb_substr($row->date, 0, 4);
$month = mb_substr($row->date, 4, 2);
$day = mb_substr($row->date, 6, 2);
$eventdate = $year . '-' . $month . '-' . $day;

$agendax['elements'][8]['caption'] = _('Date') . '*';
$agendax['elements'][8]['body'] =

    '<input type="text" name="startDate" size="12" id="f_date_b" value="' . $eventdate . '"><button type="reset" id="f_trigger_b">...</button>

<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_b",      // id of the input field
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        showsTime      :    true,            // will display a time selector
        button         :    "f_trigger_b",   // trigger for the calendar (button ID)
        singleClick    :    false            // double-click mode
    });
</script>' . ' (' . _('Please Use Exclusively The Date Selector') . ')';

$agendax['elements'][9]['caption'] = _('Recurrence');
$agendax['elements'][9]['body'] = '';

$rpt_type = empty($row->event_rpt_type) ? 'none' : $row->event_rpt_type;
$agendax['elements'][10]['caption'] = _('Pattern');
$agendax['elements'][10]['body'] = '<input type="radio" name="rpt_type" value="none" ' . (0 == strcmp($rpt_type, 'none') ? 'checked' : '') . '> ' . _('None');

if ((null === $row->frequency) || (0 == $row->frequency)) {
    $row->frequency = 1;
}
$agendax['elements'][11]['caption'] = '';
$rpt_freq_daily = 0 == strcmp($rpt_type, 'daily') ? $row->frequency : '';
$agendax['elements'][11]['body'] = '<input type="radio" name="rpt_type" value="daily" '
                                      . (0 == strcmp($rpt_type, 'daily') ? 'checked' : '')
                                      . '> '
                                      . _('Daily')
                                      . ' : '
                                      . _('Recurs every')
                                      . ' '
                                      . '<INPUT NAME="rpt_freq_daily" SIZE="4" MAXLENGTH="4" VALUE="'
                                      . $rpt_freq_daily
                                      . '"> '
                                      . _('day(s)');

$agendax['elements'][12]['caption'] = '';
$rpt_freq_weekly = 0 == strcmp($rpt_type, 'weekly') ? $row->frequency : '';
$rpt_sun = mb_substr($row->event_repeaton_days, 0, 1);
$rpt_mon = mb_substr($row->event_repeaton_days, 1, 1);
$rpt_tue = mb_substr($row->event_repeaton_days, 2, 1);
$rpt_wed = mb_substr($row->event_repeaton_days, 3, 1);
$rpt_thu = mb_substr($row->event_repeaton_days, 4, 1);
$rpt_fri = mb_substr($row->event_repeaton_days, 5, 1);
$rpt_sat = mb_substr($row->event_repeaton_days, 6, 1);
$agendax['elements'][12]['body'] = '<input type="radio" name="rpt_type" value="weekly" '
                                      . (0 == strcmp($rpt_type, 'weekly') ? 'checked' : '')
                                      . '> '
                                      . _('Weekly')
                                      . ' : '
                                      . _('Recurs every')
                                      . ' '
                                      . '<INPUT NAME="rpt_freq_weekly" SIZE="2" MAXLENGTH="2" VALUE="'
                                      . $rpt_freq_weekly
                                      . '"> '
                                      . _('week(s)')
                                      . ' '
                                      . _('on')
                                      . '<br>'
                                      . '<INPUT TYPE="checkbox" NAME="rpt_sun" VALUE="y" '
                                      . (('y' == $rpt_sun) ? 'CHECKED' : '')
                                      . '> '
                                      . _('Sunday')
                                      . '<INPUT TYPE="checkbox" NAME="rpt_mon" VALUE="y" '
                                      . (('y' == $rpt_mon) ? 'CHECKED' : '')
                                      . '> '
                                      . _('Monday')
                                      . '<INPUT TYPE="checkbox" NAME="rpt_tue" VALUE="y" '
                                      . (('y' == $rpt_tue) ? 'CHECKED' : '')
                                      . '> '
                                      . _('Tuesday')
                                      . '<INPUT TYPE="checkbox" NAME="rpt_wed" VALUE="y" '
                                      . (('y' == $rpt_wed) ? 'CHECKED' : '')
                                      . '> '
                                      . _('Wednesday')
                                      . '<INPUT TYPE="checkbox" NAME="rpt_thu" VALUE="y" '
                                      . (('y' == $rpt_thu) ? 'CHECKED' : '')
                                      . '> '
                                      . _('Thursday')
                                      . '<INPUT TYPE="checkbox" NAME="rpt_fri" VALUE="y" '
                                      . (('y' == $rpt_fri) ? 'CHECKED' : '')
                                      . '> '
                                      . _('Friday')
                                      . '<INPUT TYPE="checkbox" NAME="rpt_sat" VALUE="y" '
                                      . (('y' == $rpt_sat) ? 'CHECKED' : '')
                                      . '> '
                                      . _('Saturday');

$agendax['elements'][13]['caption'] = '';
$rpt_freq_monthlyByDay = 0 == strcmp($rpt_type, 'monthlyByDay') ? $row->frequency : '';
$agendax['elements'][13]['body'] = '<input type="radio" name="rpt_type" value="monthlyByDay" '
                                      . (0 == strcmp($rpt_type, 'monthlyByDay') ? 'checked' : '')
                                      . '> '
                                      . _('Monthly : Recurs every')
                                      . ' '
                                      . '<INPUT NAME="rpt_freq_monthlyByDay" SIZE="2" MAXLENGTH="2" VALUE="'
                                      . $rpt_freq_monthlyByDay
                                      . '"> '
                                      . _('month(s)')
                                      . ' '
                                      . _('on the same weekday');

$agendax['elements'][14]['caption'] = '';
$rpt_freq_monthlyByDayR = 0 == strcmp($rpt_type, 'monthlyByDayR') ? $row->frequency : '';
$agendax['elements'][14]['body'] = '<input type="radio" name="rpt_type" value="monthlyByDayR" '
                                      . (0 == strcmp($rpt_type, 'monthlyByDayR') ? 'checked' : '')
                                      . '> '
                                      . _('Monthly : Recurs every')
                                      . ' '
                                      . '<INPUT NAME="rpt_freq_monthlyByDayR" SIZE="2" MAXLENGTH="2" VALUE="'
                                      . $rpt_freq_monthlyByDayR
                                      . '"> '
                                      . _('month(s)')
                                      . ' '
                                      . _('on the same weekday')
                                      . ' '
                                      . _('from the end');

$agendax['elements'][15]['caption'] = '';
$rpt_freq_monthlyByDate = 0 == strcmp($rpt_type, 'monthlyByDate') ? $row->frequency : '';
$agendax['elements'][15]['body'] = '<input type="radio" name="rpt_type" value="monthlyByDate" '
                                      . (0 == strcmp($rpt_type, 'monthlyByDate') ? 'checked' : '')
                                      . '> '
                                      . _('Monthly : Recurs every')
                                      . ' '
                                      . '<INPUT NAME="rpt_freq_monthlyByDate" SIZE="2" MAXLENGTH="2" VALUE="'
                                      . $rpt_freq_monthlyByDate
                                      . '"> '
                                      . _('month(s)')
                                      . ' '
                                      . _('on the same date');

$agendax['elements'][16]['caption'] = '';
$rpt_freq_yearly = 0 == strcmp($rpt_type, 'yearly') ? $row->frequency : '';
$agendax['elements'][16]['body'] = '<input type="radio" name="rpt_type" value="yearly" '
                                      . (0 == strcmp($rpt_type, 'yearly') ? 'checked' : '')
                                      . '> '
                                      . _('Yearly')
                                      . ' : '
                                      . _('Recurs every')
                                      . ' '
                                      . '<INPUT NAME="rpt_freq_yearly" SIZE="2" MAXLENGTH="2" VALUE="'
                                      . $rpt_freq_yearly
                                      . '"> '
                                      . _('year(s)');

$agendax['elements'][17]['caption'] = _('Recur Until');
if ((int)$row->event_end > 0) {
    $year = mb_substr($row->event_end, 0, 4);

    $month = mb_substr($row->event_end, 4, 2);

    $day = mb_substr($row->event_end, 6, 2);

    $eventEndDate = $year . '-' . $month . '-' . $day;
} else {
    $eventEndDate = '';
}
$agendax['elements'][17]['body'] = '<input type="radio" name="rpt_end_use" value="n" '
                                   . (('' == $eventEndDate) ? 'checked' : '')
                                   . '> '
                                   . _('No End Date')
                                   . '<br>'
                                   . '<input type="radio" name="rpt_end_use" value="y"'
                                   . (('' != $eventEndDate) ? 'checked' : '')
                                   . '> '
                                   . '<input type="text" name="endDate" size="12" id="f_date_c" value="'
                                   . (('' == $eventEndDate) ? $eventdate : $eventEndDate)
                                   . '"><button type="reset" id="f_trigger_c">...</button>

<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_c",      // id of the input field
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        showsTime      :    true,            // will display a time selector
        button         :    "f_trigger_c",   // trigger for the calendar (button ID)
        singleClick    :    false            // double-click mode
    });
</script>'
                                   . ' ('
                                   . _('Use The Date Selector')
                                   . ')';

$agendax['elements'][18]['caption'] = '';
$agendax['elements'][18]['body'] = '<input type=submit value="' . _('Save event') . "\">\n";

$GLOBALS['xoopsOption']['template_main'] = 'agendax_eventform.html';

global $xoops_module_header;

$js_path = XOOPS_URL . '/modules/agendax/include';
$xoops_module_header .= "
        <link rel=\"stylesheet\" type=\"text/css\" media=\"all\" href=\"$js_path/jscalendar/calendar-blue.css\">
        <script type=\"text/javascript\" src=\"$js_path/jscalendar/calendar.js\"></script>
        <script type=\"text/javascript\" src=\"$js_path/jscalendar/lang/calendar-en.js\"></script>
        <script type=\"text/javascript\" src=\"$js_path/jscalendar/calendar-setup.js\"></script>
      ";
$xoops_module_header .= " 
      <script type=\"text/javascript\">
        _editor_url = \"$js_path/htmlarea/\";
        _editor_lang = \"en\";
      </script>
      <script type=\"text/javascript\" src=\"$js_path/htmlarea/htmlarea.js\"></script>
      ";
$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
