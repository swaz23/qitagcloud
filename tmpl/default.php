<?php 
// no direct access
defined('_JEXEC') or die('Restricted access');
JLoader::register('TagsHelperRoute', JPATH_BASE . '/components/com_tags/helpers/route.php');
?>
<div id="qitagcloud<?php echo '_'.$module->id;?>" <?php echo $qitagcloud->google_font_effect; ?>>
<?php
if ($qitagcloud->no_tag_found) 
{
	echo JText::_('MOD_QITAGCLOUD_NO_TAG_FOUND');
} 
else 
{
	foreach($qitagcloud->count as $key => $value) 
	{    
        $tag_url = JRoute::_(TagsHelperRoute::getTagRoute($qitagcloud->id[$key] . ':' . $qitagcloud->alias[$key]));
	    $title = $value == 1 ? JText::_('MOD_QITAGCLOUD_TAGGED_ITEM') : JText::_('MOD_QITAGCLOUD_TAGGED_ITEMS');
	    $title = $value.' '.$title;
	    $display_count = $qitagcloud->display_count ? '<span class="tag-count badge badge-info"> '. $value . '</span>' : '';  
	    $tag = '<a href="' . $tag_url . '" style="font-size:' . $qitagcloud->size[$key] . 'px;' . $qitagcloud->color[$key]. '" title="' . $title . '" target="' . $qitagcloud->target . '">' . $key . '</a>' . $display_count; 	
        echo $tag;
        echo " ";
	} 
}
?>
</div>