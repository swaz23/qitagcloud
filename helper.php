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

class modTagcloudhelper 
{
	public function getTags($params, $moduleid) 
	{
		global $mainframe;
		$db = JFactory :: getDBO();
		$user = JFactory::getUser();
		$groups = implode(',', $user->getAuthorisedViewLevels());
		$document = JFactory::getDocument();		 		    
		$max_tags = $params->get('max_tags');	
		$selected_tags = $params->get('selected_tags');		  
		$tags = new stdClass();
		$count = array();	
		$tags->size = array();
		$tags->color = array();
		$tags->id = array();
		$tags->alias = array();
		$tags->moduleid = $moduleid;	  
		$query = $db->getQuery(true)
			->select(
				array(
					$db->quoteName('m.tag_id') . ' AS tag_id',
					$db->quoteName('t.access') . ' AS access',
					$db->quoteName('t.alias') . ' AS alias',
					$db->quoteName('t.path') . ' AS path',					
					$db->quoteName('t.title') . ' AS title',	
					$db->quoteName('t.parent_id') . ' AS parent_id',					
					$db->quoteName('m.type_alias') . ' AS type_alias',
					$db->quoteName('c.core_catid') . ' AS catid'
				)
			)
			->from($db->quoteName('#__contentitem_tag_map') . ' AS m')
			->where($db->quoteName('t.access') . ' IN (' . $groups . ')');
		// only return accessible content
		$query->where($db->quoteName('c.core_access') . ' IN (' . $groups . ')');			
		// only return published tags
		$query->where($db->quoteName('t.published') . ' = 1 ');
		// only return published content
		$query->where($db->quoteName('c.core_state') . ' = 1 ');
		// Optionally filter on language
		$language = JComponentHelper::getParams('com_tags')->get('tag_list_language_filter', 'all');
		if ($language != 'all')
		{
			if ($language == 'current_language')
			{
				$language = JHelperContent::getCurrentLanguage();
			}
			$query->where($db->quoteName('t.language') . ' IN (' . $db->quote($language) . ', ' . $db->quote('*') . ')');
		}		
		$query->join('INNER', $db->quoteName('#__tags', 't') . ' ON m.tag_id = t.id')
			->join('INNER', $db->quoteName('#__ucm_content', 'c') . ' ON m.type_alias = c.core_type_alias AND m.core_content_id = c.core_content_id');

		$db->setQuery($query);			  	 
		//echo  $query->__toString(); 
		//exit; 
		try
		{
			$results = $db->loadObjectList();
		}
		catch (RuntimeException $e)
		{
			$results = array();
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
		}
		 $filtered_array = Array();		  
		 $tmp_array = Array();
		 $categories_filter = $params->get('categories_filter');	
		 // filtered by selected tags and categories
		 foreach ($results as $result) 
		 {
            if (empty($selected_tags) || in_array($result->tag_id, $selected_tags)) 
			{
			    $type_alias = explode("." , $result->type_alias);
			    $cat_alias = $type_alias[0].'.'.$result->catid;
			    if (empty($categories_filter) || in_array($cat_alias, $categories_filter))
				{ 
                    if (array_key_exists($result->title, $tmp_array)) 
					{
				        $tmp_array[$result->title]->count ++;			     
				    } 
					else 
					{
						$tmp_array[$result->title] = new stdClass();
						$tmp_array[$result->title]->count = 1;
						$tmp_array[$result->title]->id = $result->tag_id;
						$tmp_array[$result->title]->alias = $result->alias;						  
				    }			 
			    }
		    }		 
		 }

		$i = 0;  
		$params->get('random_tags') ? self::shuffle_assoc($tmp_array) : arsort($tmp_array);			  
		foreach ($tmp_array as $k => $v) 
		{		 
			$filtered_array[$i] = new stdClass();
			$filtered_array[$i]->tag = $k;
			$filtered_array[$i]->count = $v->count;
			$filtered_array[$i]->id = $v->id;
			$filtered_array[$i]->alias = $v->alias;						
			$i++;
			if ($i == $max_tags) 
			{
			    break;
			}
		}
        unset($tmp_array);
		 
		$sorted_results = self::tag_sort($filtered_array, $params->get('order')); 		 
        foreach ($sorted_results as $a) 
		{
			$tags->count[$a->tag] = $a->count;	
			$tags->id[$a->tag] = $a->id;
			$tags->alias[$a->tag] = $a->alias;				        
        }
        $tags->no_tag_found = empty($tags->count) ? true : false;
        $min_count = $tags->no_tag_found ? 0 : min($tags->count);
		$max_count = $tags->no_tag_found ? 0 : max($tags->count);		 
	    $spread = $max_count - $min_count;	 
	    if ($spread <= 0)
			$spread = 1;
		$font_spread = $params->get('max_font_size') - $params->get('min_font_size');
	    if ($font_spread < 0)
			$font_spread = 1;
	    $font_step = $font_spread / $spread;		 
		foreach($sorted_results as $result)
		{  
	        $tags->size[$result->tag] = (int)($params->get('min_font_size') + (($result->count - $min_count) * $font_step)); 
			$tags->color[$result->tag] = $params->get('colorful') ? "color:#".dechex(rand(0,16777215)) : "";
        }	    														 
		$tags->align = $params->get('align').';';
	    $tags->target = $params->get('target') ? '_blank' : '_self';	
	    $tags->colorful = $params->get('colorful');	
        $tags->display_count = $params->get('display_count');	
		
		// enable google fonts
        if ($params->get('google_font') != '') 
		{
		    $family = JString::str_ireplace(' ', '+', JString::trim($params->get('google_font')));
		    $subset = $params->get('script_subset') == '' ? '' : '&subset='.$params->get('script_subset');
			$effect = $params->get('google_font_effect') == '' ? '' : '&effect='.JString::substr($params->get('google_font_effect'), 12);
		    $document->addStyleSheet('http://fonts.googleapis.com/css?family='.$family.$subset.$effect);
			$google_font = $params->get('google_font');
			$tags->google_font_effect = $params->get('google_font_effect') == '' ? '' : 'class="'.$params->get('google_font_effect').'"';
	    }
		else
		{
			$google_font = $tags->google_font_effect = "";		
		}

		// build css codes in head						    
		$css['']['width'] = $params->get('width');
		$css['']['height'] = $params->get('height'); 
		$css['']['text-align'] = $params->get('align');		
		$css['']['line-height'] = $params->get('line_height'); 		
		$css['a']['margin-left'] = $params->get('horizontal_space'); 
		$css['a']['margin-right'] = $params->get('horizontal_space'); 
		$css['a']['padding'] = $params->get('padding');
		$css['a']['-webkit-border-radius'] = $params->get('border_radius'); 
		$css['a']['-moz-border-radius'] = $params->get('border_radius'); 
		$css['a']['border-radius'] = $params->get('border_radius'); 		
		$css['a']['font-family'] = $google_font; 
	    $css['a:link']['text-decoration'] = $params->get('underline') ? 'underline' : 'none';
	    $css['a:link']['color'] = $params->get('color');	
	    $css['a:link']['background-color'] = $params->get('bg_color');
	    $css['a:link']['font-weight'] = $params->get('bold') ? 'bold' : 'normal';
	    $css['a:visited']['text-decoration'] = $params->get('underline');
	    $css['a:visited']['color'] = $params->get('color');	
	    $css['a:visited']['background-color'] = $params->get('bg_color');
	    $css['a:visited']['font-weight'] = $params->get('bold');		
	    $css['a:hover']['text-decoration'] = $params->get('hover_underline') ? 'underline' : 'none';
	    $css['a:hover']['color'] = $params->get('hover_color');	
	    $css['a:hover']['background-color'] = $params->get('hover_bg_color');
        self::css_builder($css, $moduleid);
        return $tags;
 }

	public function tag_sort($result, $type) 
	{
		$sort_array = Array();
	    $sort_array2 = Array();
	    $sort_array3 = Array();	
	    $merge_array = Array();
	    if($type == 1) 
		{
			return $result; //no need to sort
	    }
		else
		{
			foreach($result as $r) 
			{
				$sort_array[] = $r->tag;
				$sort_array2[] = $r->tag;		
				$sort_array3[] = $r->count;	
			}			      
	    }	      	
	    if($type == 0) 
		{  
			//sort by tag name
			natcasesort($sort_array); 
			foreach($sort_array as $i => $a)
			{    
				$merge_array[] = $result[$i];
			}			   
	    }
	    elseif($type == 2) 
		{ 
			//sort by most tagged first
		    natcasesort($sort_array3);
	        foreach($sort_array3 as $i => $a)
			{
				$merge_array[] = $result[$i];
	        }		
	    }
	    elseif($type == 3) 
		{ 
			//sort randomly
			shuffle($sort_array2);
	        foreach($sort_array2 as $a) 
			{
				$key = array_keys($sort_array, $a);
				$merge_array[] = $result[$key[0]];
	        }		  
	    }
	    return $merge_array;
    }


	public function css_builder($blocks, $moduleid) 
	{
		$document = JFactory::getDocument();	
	    $css = '';
        foreach ($blocks as $block_k => $block_v) 
		{ 
	        $css .= '#qitagcloud_'.$moduleid.' '.$block_k.'{';
			foreach ($block_v as $attr_k => $attr_v) 
			{
				if ($attr_v == '') continue;
			    $css .= $attr_k.':'.$attr_v.';';
			}
			$css .= '}';			       	
	    }
	    $document->addStyleDeclaration($css);	
  }
 
    public function shuffle_assoc(&$array) 
	{
        $keys = array_keys($array);
        shuffle($keys);
		$randomized_array = Array();
        foreach ($keys as $key) 
		{
			$randomized_array[$key] = $array[$key];
		}
		$array = Array();
		foreach ($randomized_array as $k => $v) 
		{
            $array[$k] = $v;
		}		   
    }
}
?>
