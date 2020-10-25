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

//if (!$agendax_isadmin) redirect_header("index.php", 2, _("Sorry, you are not admin of Agenda-X"));

require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';

if (!isset($_GET['on'])) {
    exit('value on is not set');
}
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

$agendax['name'] = 'editform4exception';
$agendax['action'] = "$agendax_url/index.php?op=exception&id=$row->id";
$agendax['method'] = 'post';
$agendax['extra'] = 'enctype=multipart/form-data';

$agendax['title'] = _('Repeating event exception');

$submit_button = new XoopsFormButton('', 'exception', _('Save exception'), 'submit');
$reset_button = new XoopsFormButton('', 'reset', _('Reset'), 'reset');

$agendax['elements'][0]['caption'] = '';
$agendax['elements'][0]['body'] = $submit_button->render() . '&nbsp;&nbsp;' . $reset_button->render();
$agendax['elements'][0]['body'] .= '<input type="hidden" name="event_id" value="' . $row->id . '">';
$agendax['elements'][0]['body'] .= '<input type="hidden" name="submit_by" value="' . $row->submit_by . '">';
$agendax['elements'][0]['body'] .= '<input type="hidden" name="original_date" value="' . $on . '">';

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

$year = mb_substr($on, 0, 4);
$month = mb_substr($on, 4, 2);
$day = mb_substr($on, 6, 2);
$eventdate = $year . '-' . $month . '-' . $day;

$agendax['elements'][8]['caption'] = _('Date') . '*';

$agendax['elements'][8]['body'] =

    '<input type="text" name="event_date" size="12" id="f_date_b" value="' . $eventdate . '"><button type="reset" id="f_trigger_b">...</button>

<script type="text/javascript">
    Calendar.setup({
        inputField     :    "f_date_b",      // id of the input field
        ifFormat       :    "%Y-%m-%d",       // format of the input field
        showsTime      :    true,            // will display a time selector
        button         :    "f_trigger_b",   // trigger for the calendar (button ID)
        singleClick    :    false            // double-click mode
    });
</script>' . ' (' . _('Please Use Exclusively The Date Selector') . ')';

//      $timetmp = mktime(3,0,0,intval($month), intval($day), intval($year));
//      $date_select = new XoopsFormTextDateSelect("Date", "event_date", 12, $timetmp);
//      $agendax['elements'][8]['body'] = $date_select->render();

$agendax['elements'][9]['caption'] = '';
$agendax['elements'][9]['body'] = $submit_button->render() . '&nbsp;&nbsp;' . $reset_button->render();

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
