<?php
/*
 * @package		QiTagCloud
 * @version		1.0
 * @author		Qi Huang
 * @copyright	Copyright (c) 2017 www.huangqi.space. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 */

defined('JPATH_BASE') or die;

JFormHelper::loadFieldClass('groupedlist');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package     Joomla.Libraries
 * @subpackage  Form
 * @since       3.1
 */
class JFormFieldCategoriesFilter extends JFormFieldGroupedList
{
	/**
	 * A flexible tag list that respects access controls
	 *
	 * @var    string
	 * @since  3.1
	 */
	public $type = 'CategoriesFilter';

	/**
	 * Method to get a list of categories
	 *
	 * @return  array  The field option objects.
	 *
	 * @since   3.1
	 */
	protected function getGroups()
	{
		$groups = array();	
		$db		= JFactory::getDbo();
		$query	= $db->getQuery(true)
			->select('a.type_alias AS value')
			->from('#__content_types AS a')
			->order('a.type_alias ASC');

		// Get the contenttypes.
		$db->setQuery($query);

		try
		{
			$contenttypes = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			return false;
		}
        
		$extensions = Array();

		foreach ($contenttypes as $contenttype)
		{
		    $ctarray = explode('.', $contenttype->value);
			$extension = $ctarray[0];
			if (in_array($extension, $extensions) || $extension == 'com_tags') {
			   continue;
			} else {
			   $extensions[] = $extension;
			   $groups[$extension] = JHtml::_('category.options', $extension);
		            for ($i = 0; $i < count($groups[$extension]); $i++) {
		                 $groups[$extension][$i]->value = $extension.'.'.$groups[$extension][$i]->value;     
		            }
			}
		}
		// Merge any additional groups in the XML definition.
		$groups = array_merge(parent::getGroups(), $groups);

		return $groups;
	}
}