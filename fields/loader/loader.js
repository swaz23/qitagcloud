/*
 * @package		EasyTagCloud
 * @version		2.6
 * @author		Qi Huang
 * @copyright	Copyright (c) 2011 - 2014 www.joomlatonight.com. All rights reserved
 * @license		GNU/GPL (http://www.gnu.org/copyleft/gpl.html)
 */

function autoHide(plugin) {
	 var tagsorder = document.getElementById("jform_params_tags_order");
	 var maxfont = document.getElementById("jform_params_maxfontsize");
	 var minfont = document.getElementById("jform_params_minfontsize");	 
	 var boldtags = document.getElementById("jform_params_bold");	 	 
	 var tagscolor = document.getElementById("jform_params_tagscolor-lbl");
	 var tagshovercolor = document.getElementById("jform_params_tagshovercolor-lbl");
	 var tagsbgcolor = document.getElementById("jform_params_tagsbgcolor-lbl");
	 var tagshoverbgcolor = document.getElementById("jform_params_tagshoverbgcolor-lbl");
	 var underline = document.getElementById("jform_params_show_underline");
     var hoverunderline = document.getElementById("jform_params_hover_show_underline");
     var tagsalign = document.getElementById("jform_params_tags_align");	 
	 var lineheight = document.getElementById("jform_params_line_height");
	 var horizontal = document.getElementById("jform_params_horizontal_space");
	 var padding = document.getElementById("jform_params_tagspadding");
	 var borderradius = document.getElementById("jform_params_borderradius");
	 var colorful = document.getElementById("jform_params_colorful_tags");	 
	 var params = new Array(tagsorder,maxfont,minfont,boldtags,tagscolor,tagshovercolor,tagsbgcolor,tagshoverbgcolor,underline,hoverunderline,tagsalign,lineheight,horizontal,padding, borderradius,colorful);
	 for (var i = 0; i < params.length; i++) {
		  params[i].parentNode.parentNode.style.display = '';		 
	 }
     if (plugin == 'tags3d') {
	          var params_hide = new Array(tagsorder,boldtags,tagshovercolor,tagsbgcolor,tagshoverbgcolor,underline,hoverunderline,tagsalign,lineheight,horizontal,padding, borderradius);
	 } else if (plugin == 'awesomecloud') {
	          var params_hide = new Array(tagsorder,boldtags,tagscolor,tagshovercolor,tagsbgcolor,tagshoverbgcolor,underline,hoverunderline,tagsalign,lineheight,horizontal,padding, borderradius);	 
	 } else if (plugin == 'none') {
	          var params_hide = new Array();
	 }
	
	
	 for (var i = 0; i < params_hide.length; i++) {
		  params_hide[i].parentNode.parentNode.style.display = 'none';
		  //params[i].disabled = true;
	 }
	 
	 
}


function convertNow(path) {
 	var xmlhttp;
    document.getElementById("convertnow").innerHTML="Converting...";	
	document.getElementById("convertnow").disabled="disabled";	
    if (window.XMLHttpRequest) {// code for IE7+, Firefox, Chrome, Opera, Safari
        xmlhttp=new XMLHttpRequest();
    } else {// code for IE6, IE5
        xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
	xmlhttp.onreadystatechange=function() {
          if (xmlhttp.readyState==4 && xmlhttp.status==200) {
              document.getElementById("convertnow").style.display='none';
			  document.getElementById("convertinfo").style.display="";	
			  document.getElementById("convertinfo").innerHTML=xmlhttp.responseText;			  
          }
    }

    xmlhttp.open("GET",path,true);
    xmlhttp.send();   
}

function sourceSwitcher(s) {
   var categories = document.getElementById("jform_params_categories");
   var k2categories = document.getElementById("jform_params_k2categories");   
   if (s == "joomla") {
       categories.parentNode.parentNode.style.display = '';
       k2categories.parentNode.parentNode.style.display = 'none';	   
   } else if (s == "k2") {
       categories.parentNode.parentNode.style.display = 'none';
       k2categories.parentNode.parentNode.style.display = '';  
   }
}	