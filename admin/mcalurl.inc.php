<?php

require_once XOOPS_ROOT_PATH . '/class/xoopsmodule.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/formelement.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/form.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/formradio.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/formradioyn.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/formtext.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/formbutton.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/themeform.php';

if (empty($_POST['submit']) or isset($_GET['list'])) {
    # display form

    $mcalurl = getMcalUrl();

    if (isset($mcalurl['mc_url']) && '' != $mcalurl['mc_url']) {
        $mc_url = $mcalurl['mc_url'];

        $mc_isscript = $mcalurl['mc_isscript'];
    } else {
        $mc_url = '';

        $mc_isscript = 0;
    }

    $url_text = new XoopsFormText(_AGX_IMAGEURL_OR_SCRIPTPATH, 'mc_url', 70, 140, $mc_url);

    //    $url_isscript = new XoopsFormRadioYN(_AGX_ISPHPSCRIPT, "mc_isscript", $mc_isscript);

    $submit_button = new XoopsFormButton('', 'submit', 'submit', 'submit');

    $mcalurl_form = new XoopsThemeForm(_AGX_MINICALIMAGESETTING, 'mcalurlform', 'index.php?op=mi');

    $mcalurl_form->addElement($url_text);

    //    $mcalurl_form->addElement($url_isscript);

    $mcalurl_form->addElement($submit_button);

    $output_str .= $mcalurl_form->render();
} else {
    # update the mcalurl table

    extract($_POST);

    $myts = MyTextSanitizer::getInstance();

    $mc_url = $myts->stripSlashesGPC($mc_url);

    $query = 'UPDATE ' . $xoopsDB->prefix() . "_agendax_mcalurl SET mc_url='" . $mc_url . "', mc_isscript=9 WHERE mc_id=0";

    if ($xoopsDB->query($query)) {
        redirect_header("$agendax_url/index.php", 3, _AGX_UPDATE_IMG_SUCCESS);
    } else {
        redirect_header("$agendax_url/index.php?op=mi&list=1", 3, _AGX_DBERROR);
    }
}

function getMcalUrl($id = 0)
{
    global $xoopsDB;

    //query Database (returns an array)

    $result = $xoopsDB->queryF('SELECT mc_isscript, mc_url FROM ' . $xoopsDB->prefix('agendax_mcalurl') . " WHERE mc_id=$id");

    return $xoopsDB->fetchArray($result);
}
