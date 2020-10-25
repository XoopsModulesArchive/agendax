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

include '../../../mainfile.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsmodule.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/formelement.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/form.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/formradio.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/formradioyn.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/formtext.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/formbutton.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/themeform.php';
require XOOPS_ROOT_PATH . '/include/cp_functions.php';
if ($xoopsUser) {
    $xoopsModule = XoopsModule::getByDirname('agendax');

    if (!$xoopsUser->isAdmin($xoopsModule->mid())) {
        redirect_header(XOOPS_URL . '/', 3, _NOPERM);

        exit();
    }
} else {
    redirect_header(XOOPS_URL . '/', 3, _NOPERM);

    exit();
}
if (file_exists('../language/' . $xoopsConfig['language'] . '/admin.php')) {
    @include '../language/' . $xoopsConfig['language'] . '/admin.php';
} else {
    @include '../language/english/admin.php';
}
// transitional function, should be removed soon
//function translate ($text) {

//  return _( trans_temp($text) );
//  return trans_temp($text);
//}
//if (!isset($agendax_path)) $agendax_path = XOOPS_ROOT_PATH.'/modules/agendax';
//if (!isset($agendax_path)) $agendax_url = XOOPS_URL.'/modules/agendax';
