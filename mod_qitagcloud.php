<?php
/**
* @version      1.0
* @package		mod_qitagcloud
* @author       www.huangqi.space
* @copyright	Copyright(C)2015 Qi Projects. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
*/

// no direct access

defined('_JEXEC') or die('Restricted access');

// Include the syndicate functions only once
require_once __DIR__ . '/helper.php';

$taghelper = new modTagcloudHelper;
$qitagcloud = $taghelper->getTags($params, $module->id);

require(JModuleHelper::getLayoutPath('mod_qitagcloud'));
?>