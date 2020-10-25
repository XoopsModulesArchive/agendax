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
// eventform.inc.php file

/*
** The unique id of an item to check permissions for.
*/
$perm_itemid = AGENDAX_PERM_SUBMIT;

/*
** check permission
*/
if (!$gpermHandler->checkRight($perm_name, $perm_itemid, $groups, $module_id)) {
    // not allowed

    redirect_header(XOOPS_URL . '/modules/agendax/index.php', 1, _AGX_NO_PERMISSION_TO_SUBMIT);

    exit;
}
    /*
    ** allowed to submit
    */

    $js_path = XOOPS_URL . '/modules/agendax/include';
    require XOOPS_ROOT_PATH . '/modules/agendax/include/jscalendar/calendar.php';
    $datepicker = new DHTML_Calendar($js_path . '/jscalendar/', 'en', 'calendar-win2k-2', false);

    $agendax['name'] = 'eventform';
    $agendax['action'] = "$agendax_url/index.php?op=addevent";
    $agendax['method'] = 'post';
    $agendax['extra'] = 'enctype=multipart/form-data';

    $agendax['title'] = _('Add event');

    $agendax['elements'][0]['caption'] = '';
    $agendax['elements'][0]['body'] = '<input type="submit" value="' . _('Add event') . '">';

    $agendax['elements'][1]['caption'] = _('Event title') . '*';
    $agendax['elements'][1]['body'] = '<input class="text" type="text" id="title" name="title"> (' . _('* denotes required') . ')';

    $agendax['elements'][2]['caption'] = _('Description') . '*';
    $agendax['elements'][2]['body'] = '<textarea class="comment" id="description" name="description" cols="40" rows="20"></textarea>' . "<script type=\"text/javascript\">
               var config = new HTMLArea.Config(); // create a new configuration object
                                                   // having all the default values
               config.width = '500px';
               config.height = '200px';

               HTMLArea.replace('description', config);
              </script>";

    if (is_object($xoopsUser)) {
        $name = $xoopsUser->getVar('name', 'E');
    } else {
        $name = '';
    }
    $agendax['elements'][3]['caption'] = _('Contact') . '*';
    $agendax['elements'][3]['body'] = '<input class="text" type="text" id="contact" value="' . $name . '" name="contact"></input>';
    if (is_object($xoopsUser)) {
        $email = $xoopsUser->getVar('email', 'E');
    } else {
        $email = '';
    }
    $agendax['elements'][4]['caption'] = _('Email') . '*';
    $agendax['elements'][4]['body'] = '<input class="text" type="text" id="email" value="' . $email . '" name="email">';

    $agendax['elements'][5]['caption'] = _('URL');
    $agendax['elements'][5]['body'] = '<input class="text" type="text" id="url" name="url">';

    $agendax['elements'][6]['caption'] = _('Picture');

    $size = $filesize / 1000;
    $temp = '<input class="text" type="file" name="foto">';

    $temp .= '<br><br>( ' . _('max') . ' $size kb - ' . _('valid extensions') . ' : ';

    for ($i = 0, $iMax = count($extensions); $i < $iMax; $i++) {
        $temp .= $extensions[$i] . ' ';
    }

    $temp .= ')';
    $agendax['elements'][6]['body'] = $temp;

    $agendax['elements'][7]['caption'] = _('Category');

    $temp = '<select class="textBox2" name="cat">';

    # get the categories
    $query = 'SELECT cat_id,cat_name FROM ' . XOOPS_DB_PREFIX . '_agendax_cat';
    $result = $GLOBALS['xoopsDB']->queryF($query);
    while (false !== ($row = $GLOBALS['xoopsDB']->fetchObject($result))) {
        $temp .= "\t<option value=" . $row->cat_id;

        $temp .= 1 == $row->cat_id ? ' selected' : '';

        $temp .= '>' . $row->cat_name . "</option>\n";
    }
    $temp .= "</select>\n";
    $agendax['elements'][7]['body'] = $temp;

    /*
    **    get date
    */
    global $userTimestamp;

    # get days
    $day = date('d', $userTimestamp);
    $month = date('m', $userTimestamp);
    $year = date('Y', $userTimestamp);

    /*
    **    To do different date format
    */

    $todayvalue = $year . '-' . $month . '-' . $day;
    $agendax['elements'][8]['caption'] = _('Date') . '*';
    //      $agendax['elements'][8]['body'] =
    //"<input type=\"text\" name=\"startDate\" size=\"12\" id=\"f_date_b\" value=\"".$todayvalue."\"><button type=\"reset\" id=\"f_trigger_b\">...</button>
    //
    //<script type=\"text/javascript\">
    //    Calendar.setup({
    //        inputField     :    \"f_date_b\",      // id of the input field
    //        ifFormat       :    \"%Y-%m-%d\",       // format of the input field
    //        showsTime      :    true,            // will display a time selector
    //        button         :    \"f_trigger_b\",   // trigger for the calendar (button ID)
    //        singleClick    :    false            // double-click mode
    //    });
    //</script>"
    //
    $agendax['elements'][8]['body'] = $datepicker->return_input_field(
        // calendar options go here; see the documentation and/or calendar-setup.js
            [
                'firstDay' => 0, // show Sunday first

'showsTime' => false,
'showOthers' => false,
'ifFormat' => '%Y-%m-%d',
'timeFormat' => '12',
            ],
            // field attributes go here
            [
                //           'style'       => 'width: 15em; color: #840; background-color: #ff8; border: 1px solid #000; text-align: center',

'name' => 'startDate',
'size' => 12,
'value' => $todayvalue,
            ]
    ) . ' (' . _('Please Use Exclusively The Date Selector') . ')';

    $agendax['elements'][9]['caption'] = _('Recurrence');
    $agendax['elements'][9]['body'] = '';

    $rpt_type = empty($rpt_type) ? 'none' : $rpt_type;
    $agendax['elements'][10]['caption'] = _('Pattern');
    $agendax['elements'][10]['body'] = '<input type="radio" name="rpt_type" value="none" ' . (0 == strcmp($rpt_type, 'none') ? 'checked' : '') . '> ' . _('None');

    $agendax['elements'][11]['caption'] = '';
    $agendax['elements'][11]['body'] = '<input type="radio" name="rpt_type" value="daily" '
                                          . (0 == strcmp($rpt_type, 'daily') ? 'checked' : '')
                                          . '> '
                                          . _('Daily')
                                          . ' : '
                                          . _('Recurs every')
                                          . ' '
                                          . '<INPUT NAME="rpt_freq_daily" SIZE="4" MAXLENGTH="4" VALUE="'
                                          . (empty($rpt_freq) ? '' : $rpt_freq)
                                          . '"> '
                                          . _('day(s)');

    $agendax['elements'][12]['caption'] = '';
    $agendax['elements'][12]['body'] = '<input type="radio" name="rpt_type" value="weekly" '
                                          . (0 == strcmp($rpt_type, 'weekly') ? 'checked' : '')
                                          . '> '
                                          . _('Weekly')
                                          . ' : '
                                          . _('Recurs every')
                                          . ' '
                                          . '<INPUT NAME="rpt_freq_weekly" SIZE="2" MAXLENGTH="2" VALUE="'
                                          . (empty($rpt_freq) ? '' : $rpt_freq)
                                          . '"> '
                                          . _('week(s)')
                                          . ' '
                                          . _('on')
                                          . '<br>'
                                          . '<INPUT TYPE="checkbox" NAME="rpt_sun" VALUE="y" '
                                          . (!empty($rpt_sun) ? 'CHECKED' : '')
                                          . '> '
                                          . _('Sunday')
                                          . '<INPUT TYPE="checkbox" NAME="rpt_mon" VALUE="y" '
                                          . (!empty($rpt_mon) ? 'CHECKED' : '')
                                          . '> '
                                          . _('Monday')
                                          . '<INPUT TYPE="checkbox" NAME="rpt_tue" VALUE="y" '
                                          . (!empty($rpt_tue) ? 'CHECKED' : '')
                                          . '> '
                                          . _('Tuesday')
                                          . '<INPUT TYPE="checkbox" NAME="rpt_wed" VALUE="y" '
                                          . (!empty($rpt_wed) ? 'CHECKED' : '')
                                          . '> '
                                          . _('Wednesday')
                                          . '<INPUT TYPE="checkbox" NAME="rpt_thu" VALUE="y" '
                                          . (!empty($rpt_thu) ? 'CHECKED' : '')
                                          . '> '
                                          . _('Thursday')
                                          . '<INPUT TYPE="checkbox" NAME="rpt_fri" VALUE="y" '
                                          . (!empty($rpt_fri) ? 'CHECKED' : '')
                                          . '> '
                                          . _('Friday')
                                          . '<INPUT TYPE="checkbox" NAME="rpt_sat" VALUE="y" '
                                          . (!empty($rpt_sat) ? 'CHECKED' : '')
                                          . '> '
                                          . _('Saturday');

    $agendax['elements'][13]['caption'] = '';
    $agendax['elements'][13]['body'] = '<input type="radio" name="rpt_type" value="monthlyByDay" '
                                          . (0 == strcmp($rpt_type, 'monthlyByDay') ? 'checked' : '')
                                          . '> '
                                          . _('Monthly : Recurs every')
                                          . ' '
                                          . '<INPUT NAME="rpt_freq_monthlyByDay" SIZE="2" MAXLENGTH="2" VALUE="'
                                          . (empty($rpt_freq) ? '' : $rpt_freq)
                                          . '"> '
                                          . _('month(s)')
                                          . ' '
                                          . _('on the same weekday');

    $agendax['elements'][14]['caption'] = '';
    $agendax['elements'][14]['body'] = '<input type="radio" name="rpt_type" value="monthlyByDayR" '
                                          . (0 == strcmp($rpt_type, 'monthlyByDayR') ? 'checked' : '')
                                          . '> '
                                          . _('Monthly : Recurs every')
                                          . ' '
                                          . '<INPUT NAME="rpt_freq_monthlyByDayR" SIZE="2" MAXLENGTH="2" VALUE="'
                                          . (empty($rpt_freq) ? '' : $rpt_freq)
                                          . '"> '
                                          . _('month(s)')
                                          . ' '
                                          . _('on the same weekday from the end');

    $agendax['elements'][15]['caption'] = '';
    $agendax['elements'][15]['body'] = '<input type="radio" name="rpt_type" value="monthlyByDate" '
                                          . (0 == strcmp($rpt_type, 'monthlyByDate') ? 'checked' : '')
                                          . '> '
                                          . _('Monthly : Recurs every')
                                          . ' '
                                          . '<INPUT NAME="rpt_freq_monthlyByDate" SIZE="2" MAXLENGTH="2" VALUE="'
                                          . (empty($rpt_freq) ? '' : $rpt_freq)
                                          . '"> '
                                          . _('month(s)')
                                          . ' '
                                          . _('on the same date');

    $agendax['elements'][16]['caption'] = '';
    $agendax['elements'][16]['body'] = '<input type="radio" name="rpt_type" value="yearly" '
                                          . (0 == strcmp($rpt_type, 'yearly') ? 'checked' : '')
                                          . '> '
                                          . _('Yearly')
                                          . ' : '
                                          . _('Recurs every')
                                          . ' '
                                          . '<INPUT NAME="rpt_freq_yearly" SIZE="2" MAXLENGTH="2" VALUE="'
                                          . (empty($rpt_freq) ? '' : $rpt_freq)
                                          . '"> '
                                          . _('year(s)');

    $caption = _('Recur Until');
    $rpt_endDate = 'endDate';

    $agendax['elements'][17]['caption'] = $caption;

    $agendax['elements'][17]['body'] = '<input type="radio" name="rpt_end_use" value="n"' . (empty($rpt_end_use) ? 'checked' : '') . '> ' . _('No End Date') . '<br>' . '<input type="radio" name="rpt_end_use" value="y" > ' . $datepicker->return_input_field(
        // calendar options go here; see the documentation and/or calendar-setup.js
            [
                'firstDay' => 0, // show Sunday first

'showsTime' => false,
'showOthers' => false,
'ifFormat' => '%Y-%m-%d',
'timeFormat' => '12',
            ],
            // field attributes go here
            [
                'name' => 'endDate',
'size' => 12,
'value' => $todayvalue,
            ]
    ) . ' (' . _('Use The Date Selector') . ')';

    $agendax['elements'][18]['caption'] = '';
    $agendax['elements'][18]['body'] = '<input type=submit value="' . _('Add event') . "\">\n";

    $GLOBALS['xoopsOption']['template_main'] = 'agendax_eventform.html';

    global $xoops_module_header;

    $xoops_module_header .= $datepicker->get_load_files_code();

    $xoops_module_header .= " 
      <script type=\"text/javascript\">
        _editor_url = \"$js_path/htmlarea/\";
        _editor_lang = \"en\";
      </script>
      <script type=\"text/javascript\" src=\"$js_path/htmlarea/htmlarea.js\"></script>
      ";

    $xoopsTpl->assign('xoops_module_header', $xoops_module_header);
