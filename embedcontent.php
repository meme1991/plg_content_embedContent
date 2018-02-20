<?php
# @Author: SPEDI srl
# @Date:   30-01-2018
# @Email:  sviluppo@spedi.it
# @Last modified by:   SPEDI srl
# @Last modified time: 31-01-2018
# @License: GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
# @Copyright: Copyright (c) SPEDI srl

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');
if (version_compare(JVERSION, '1.6.0', 'ge')){
	jimport('joomla.html.parameter');
}

class plgContentEmbedContent extends JPlugin {

	var $plg_name = "embedcontent";
	var $plg_tag  = "embedContent";

	function plgContentArticleGallery( &$subject, $params ){
		parent::__construct( $subject, $params );

		// Define the DS constant under Joomla! 3.0+
		if (!defined('DS')){
			define('DS', DIRECTORY_SEPARATOR);
		}
	}

	// Joomla! 2.5+
	function onContentPrepare($context, &$row, &$params, $page = 0){
		$this->renderPhGallery($row, $params, $page = 0);
	}

	// The main function
	function renderPhGallery(&$row, &$params, $page = 0){

		// API
		jimport('joomla.filesystem.file');
		$mainframe    = JFactory::getApplication();
		$document     = JFactory::getDocument();
		$db           = JFactory::getDbo();
		$tmpl	      = $mainframe->getTemplate();
		//$menu           = $mainframe->getMenu();
		//$active         = $mainframe->getMenu()->getActive();

		//var_dump($active);

		// Check se il plugin è attivato
		if (JPluginHelper::isEnabled('content', $this->plg_name) == false) return;

		// Salvare se il formato della pagina non è quello che vogliamo
		$allowedFormats = array('', 'html', 'feed', 'json');
		if (!in_array(JRequest::getCmd('format'), $allowedFormats)) return;

		// Controllo semplice delle prestazioni per determinare se il plugin dovrebbe elaborare ulteriormente
		if (JString::strpos($row->text, $this->plg_tag) === false) return;

		// Start Plugin
		$regex_one		= '/({embedContent\s*)(.*?)(})/si';
		$regex_all		= '/{embedContent\s*.*?}/si';
		// $regex_one		= '/({'.$plg_tag.'\s*)(.*?)(})/si';
		// $regex_all		= '/{'.$plg_tag.'\s*.*?}/si';
		//$matches 		= array();
		$count_matches	= preg_match_all($regex_all,$row->text,$matches,PREG_OFFSET_CAPTURE | PREG_PATTERN_ORDER);

		//var_dump($matches, $count_matches);

		for($i=0;$i<$count_matches;$i++){
			$tag	= $matches[0][$i][0];
			preg_match($regex_one,$tag,$phocagallery_parts);
			$parts = explode("|", $phocagallery_parts[2]);
			//var_dump($parts);
			foreach($parts as $value){
				$values = explode("=", $value, 2);
				//var_dump($values);
				//es. $values[0] -> catid
				//es. $values[1] -> 1
				/*************************
									URL
				*************************/
				if($values[0] == 'url') {
					$param[$i]['url'] = $values[1];
				}
				/*************************
									OVERLAY
				*************************/
				if($values[0] == 'overlay') {
					$param[$i]['overlay'] = $values[1];
				}
				/*************************
									LABEL
				*************************/
				if($values[0] == 'label') {
					$param[$i]['label'] = $values[1];
				}
				/*************************
									WIDTH
				*************************/
				if($values[0] == 'width') {
					$param[$i]['width'] = $values[1];
				}
				/*************************
									HEIGHT
				*************************/
				if($values[0] == 'height') {
					$param[$i]['height'] = $values[1];
				}
				/*************************
									ALIGN
				*************************/
				if($values[0] == 'align') {
					$param[$i]['align'] = $values[1];
				}
				/*************************
									 TAG
				*************************/
				$param[$i]['tag'] = $matches[0][$i][0];
			}

			if($param[$i]['url'] == '')
				return;

			/*************************
						QUAERY RESULT
			*************************/
			//$param[$i]['result'] = $results;

			//var_dump($param[$i]);

			/*************************
			TEST DEI PARAMETRI OPZIONALI
			*************************/
			if(!isset($param[$i]['tmpl']))   $param[$i]['tmpl']   = 'default';
			if(!isset($param[$i]['width']))  $param[$i]['width']  = '100%';
			if(!isset($param[$i]['height'])) $param[$i]['height'] = '450px';
			if(!isset($param[$i]['align']))  $param[$i]['align']  = 0;
		}

		// ----------------------------------- Prepare the output -----------------------------------

		for($k=0;$k<$count_matches;$k++){
			// Fetch the template
			$item        = $param[$k];
			$PlgTmplName = $item['tmpl'];
			$id 			   = substr(md5($k.$item['tag']), 1, 5);

			// recupero del template
			ob_start();
			$templatePath = __DIR__.DS.'tmpl'.DS.$PlgTmplName.'/default.php';
			include ($templatePath);
			$getTemplate = ob_get_contents();
			ob_end_clean();

			// Output
			$plg_html = $getTemplate;
			// Do the replace
			$row->text = str_replace($item['tag'], $plg_html, $row->text);
		}

	} // END FUNCTION

} // END CLASS
