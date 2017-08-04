<?php
 /*
 * @package		QiTagCloud
 * @version		1.0
 * @author		Qi Huang
 * @copyright	Copyright (c) 2017 www.huangqi.space. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 */

defined('_JEXEC') or die ('Restricted access');

jimport('joomla.form.formfield');


class JFormFieldLoader extends JFormField
{
	protected $type = 'Loader';
	
	function getInput()
	{   
		$document = JFactory::getDocument();		
        $uri = JURI::root(true).'/modules/mod_qitagcloud/fields/loader/';
		$document->addScript($uri . 'loader.js');	
		$document->addScript($uri . 'jquery.multiple.select.js');	
        $document->addStyleSheet($uri . 'multiple-select.css', 'text/css', null, array()); 		
        $script = "jQuery(document).ready(function($) {
                     $('#jform_params_categoriesfilter').multipleSelect({ filter: true});
					 $('#jform_params_categoriesfilter_chzn').hide();
                  });";	
		$document->addScriptDeclaration($script);			
	}
}
?>