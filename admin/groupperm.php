<?php

require dirname(__DIR__, 3) . '/include/cp_header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';
$module_id = $xoopsModule->getVar('mid');

// language files
$language = $xoopsConfig['language'];
if (!file_exists(XOOPS_ROOT_PATH . "/modules/system/language/$language/admin/blocksadmin.php")) {
    $language = 'english';
}
require_once XOOPS_ROOT_PATH . "/modules/system/language/$language/admin.php";

if (!empty($_POST['submit'])) {
    redirect_header(XOOPS_URL . '/modules/agendax/admin/groupperm.php', 1, _AGX_DBUPDATED);

    exit;
}

$item_list = [
    '1' => _AGX_GPERM_SUBMIT,
'2' => _AGX_GPERM_EDITDELETE_EVENTS,
'4' => _AGX_GPERM_MANAGE_CATEGORIES,
'8' => _AGX_GPERM_APPROVE,
    // '16' => _AGX_GPERM_VIEW_OTHERS_EVENTS
];

$title_of_form = 'Agenda-X Permission Setting';
$perm_name = 'Global Permission';
$perm_desc = 'Select permissions that each group is allowed to do';

$form = new XoopsGroupPermForm($title_of_form, $module_id, $perm_name, $perm_desc);
foreach ($item_list as $item_id => $item_name) {
    $form->addItem($item_id, $item_name);
}

xoops_cp_header();
echo $form->render();
xoops_cp_footer();
