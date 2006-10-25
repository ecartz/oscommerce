<?php
/*
  $Id: $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2006 osCommerce

  Released under the GNU General Public License
*/

  $Qgroups = $osC_Database->query('select distinct content_group from :table_languages_definitions where languages_id = :languages_id order by content_group');
  $Qgroups->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
  $Qgroups->bindInt(':languages_id', $_GET[$osC_Template->getModule()]);
  $Qgroups->execute();

  $groups_array = array();
  while ($Qgroups->next()) {
    $groups_array[] = array('id' => $Qgroups->value('content_group'), 'text' => $Qgroups->value('content_group'));
  }
?>

<div>
  <div style="float: right; margin-top: 10px;"><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '&lID=' . $_GET[$osC_Template->getModule()]), osc_icon('back.png', IMAGE_BACK) . ' ' . TEXT_BACK_TO_LANGUAGES); ?></div>

  <h1><?php echo osc_link_object(osc_href_link(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()]), $osC_Template->getPageTitle()); ?></h1>
</div>

<?php
  if ($osC_MessageStack->size($osC_Template->getModule()) > 0) {
    echo $osC_MessageStack->output($osC_Template->getModule());
  }
?>

<div id="infoBox_lDefault" <?php if (!empty($_GET['action'])) { echo 'style="display: none;"'; } ?>>
  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="dataTable">
    <thead>
      <tr>
        <th><?php echo TABLE_HEADING_DEFINITION_GROUPS; ?></th>
        <th><?php echo TABLE_HEADING_TOTAL_DEFINITIONS; ?></th>
        <th><?php echo TABLE_HEADING_ACTION; ?></th>
      </tr>
    </thead>
    <tbody>

<?php
  $Qgroups = $osC_Database->query('select distinct content_group, count(*) as total_entries from :table_languages_definitions where languages_id = :languages_id group by content_group order by content_group');
  $Qgroups->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
  $Qgroups->bindInt(':languages_id', $_GET[$osC_Template->getModule()]);
  $Qgroups->execute();

  while ($Qgroups->next()) {
    if (!isset($dInfo) && (!isset($_GET['group']) || (isset($_GET['group']) && ($_GET['group'] == $Qgroups->value('content_group'))))) {
      $dInfo = new objectInfo($Qgroups->toArray());
    }
?>

      <tr onmouseover="rowOverEffect(this);" onmouseout="rowOutEffect(this);">
        <td><?php echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&group=' . $Qgroups->value('content_group') . '&action=lDefine'), osc_image('images/icons/folder.gif', ICON_FOLDER) . '&nbsp;' . $Qgroups->value('content_group')); ?></td>
        <td align="right"><?php echo $Qgroups->value('total_entries'); ?></td>
        <td align="right">

<?php
    if (isset($dInfo) && ($Qgroups->value('content_group') == $dInfo->content_group)) {
      echo osc_link_object('#', osc_icon('trash.png', IMAGE_DELETE), 'onclick="toggleInfoBox(\'lDelete\');"');
    } else {
      echo osc_link_object(osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&group=' . $Qgroups->value('content_group') . '&action=lDelete'), osc_icon('trash.png', IMAGE_DELETE));
    }
?>

        </td>
      </tr>

<?php
  }
?>

    </tbody>
  </table>

  <p align="right"><?php echo '<input type="button" value="' . IMAGE_INSERT . '" onclick="toggleInfoBox(\'lNew\');" class="infoBoxButton">'; ?></p>
</div>

<div id="infoBox_lNew" <?php if ($_GET['action'] != 'lNew') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('new.png', IMAGE_INSERT) . ' ' . TEXT_INFO_HEADING_NEW_LANGUAGE_DEFINITION; ?></div>
  <div class="infoBoxContent">
    <form name="lNew" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&action=insert_definition'); ?>" method="post">

    <p><?php echo TEXT_INFO_INSERT_DEFINITION_INTRO; ?></p>

    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DEFINITION_KEY . '</b>'; ?></td>
        <td class="smallText" width="60%"><?php echo osc_draw_input_field('key', null, 'style="width: 100%"'); ?></td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DEFINITION_VALUE . '</b>'; ?></td>
        <td class="smallText" width="60%">

<?php
  foreach ($osC_Language->getAll() as $l) {
    echo osc_image('../includes/languages/' . $l['code'] . '/images/' . $l['image'], $l['name'], null, null, 'style="vertical-align: top; padding-top: 5px; margin-left: -20px;"') . '&nbsp;' . osc_draw_textarea_field('value[' . $l['id'] . ']', null, 60, 4, 'style="width: 99%;"') . '<br />';
  }
?>

        </td>
      </tr>
      <tr>
        <td class="smallText" width="40%"><?php echo '<b>' . TEXT_INFO_LANGUAGE_DEFINITION_GROUP . '</b>'; ?></td>
        <td class="smallText" width="60%">

<?php
  if (!empty($groups_array)) {
    echo osc_draw_pull_down_menu('group', $groups_array, null, 'style="width: 30%;"') . '&nbsp;&nbsp;<b>' . TEXT_INFO_LANGUAGE_DEFINITION_GROUP_NEW . '</b>&nbsp;';
  }

  echo osc_draw_input_field('group_new', null, 'style="width: ' . (empty($groups_array) ? '100%' : '40%') . ';"');
?>

        </td>
      </tr>
    </table>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_SAVE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="toggleInfoBox(\'lDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  if (isset($dInfo)) {
?>

<div id="infoBox_lDelete" <?php if ($_GET['action'] != 'lDelete') { echo 'style="display: none;"'; } ?>>
  <div class="infoBoxHeading"><?php echo osc_icon('trash.png', IMAGE_DELETE) . ' ' . $dInfo->content_group; ?></div>
  <div class="infoBoxContent">
    <form name="lDelete" action="<?php echo osc_href_link_admin(FILENAME_DEFAULT, $osC_Template->getModule() . '=' . $_GET[$osC_Template->getModule()] . '&group=' . $dInfo->content_group . '&action=deleteconfirm_definition'); ?>" method="post">

    <p><?php echo TEXT_INFO_DELETE_DEFINITION_INTRO; ?></p>
    <p><?php echo '<b>' . $dInfo->content_group . '</b>'; ?></p>

<?php
    $Qdefs = $osC_Database->query('select id, definition_key from :table_languages_definitions where languages_id = :languages_id and content_group = :content_group order by definition_key');
    $Qdefs->bindTable(':table_languages_definitions', TABLE_LANGUAGES_DEFINITIONS);
    $Qdefs->bindInt(':languages_id', $_GET[$osC_Template->getModule()]);
    $Qdefs->bindValue(':content_group', $dInfo->content_group);
    $Qdefs->execute();

    $defs_array = array();

    while ($Qdefs->next()) {
      $defs_array[] = array('id' => $Qdefs->valueInt('id'), 'text' => $Qdefs->value('definition_key'));
    }
?>

    <p>(<a href="javascript:selectAllFromPullDownMenu('defs');"><u>select all</u></a> | <a href="javascript:resetPullDownMenuSelection('defs');"><u>select none</u></a>)<br /><?php echo osc_draw_pull_down_menu('defs[]', $defs_array, null, 'id="defs" size="10" multiple="multiple" style="width: 100%;"'); ?></p>

    <p align="center"><?php echo '<input type="submit" value="' . IMAGE_DELETE . '" class="operationButton"> <input type="button" value="' . IMAGE_CANCEL . '" onclick="resetPullDownMenuSelection(\'defs\'); toggleInfoBox(\'lDefault\');" class="operationButton">'; ?></p>

    </form>
  </div>
</div>

<?php
  }
?>
