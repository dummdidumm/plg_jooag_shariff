<?php
/**
 * @package    JooAg_Shariff
 *
 * @author     Joomla Agentur <info@joomla-agentur.de>
 * @copyright  Copyright (c) 2009 - 2015 Joomla-Agentur All rights reserved.
 * @license    GNU General Public License version 2 or later;
 * @description A small Plugin to share Social Links!
 */
defined('_JEXEC') or die;

/**
 * Class PlgContentJooag_Shariff
 *
 * @since  1.0.0
 */
class PlgContentJooag_Shariff extends JPlugin
{
	/**
	 * renders the buttons before the article
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   mixed    &$article  An object with a "text" property
	 * @param   mixed    &$params   Additional parameters. See {@see PlgContentContent()}.
	 * @param   integer  $page      Optional page number. Unused. Defaults to zero.
	 *
	 * @return  string
	 */	
	public function onContentBeforeDisplay($context, &$article, &$params, $page = 0)
	{
		if($context == 'com_content.article' and $this->params->get('position') == '0')
		{
			$output = $this->getOutputPosition('0');
			
			return $output;
		}
	}

	/**
	 * renders the buttons after the article
	 *
	 * @param   string   $context   The context of the content being passed to the plugin.
	 * @param   mixed    &$article  An object with a "text" property
	 * @param   mixed    &$params   Additional parameters. See {@see PlgContentContent()}.
	 * @param   integer  $page      Optional page number. Unused. Defaults to zero.
	 *
	 * @return  string
	 */
	public function onContentAfterDisplay($context, &$article, &$params, $page = 0)
	{		
		if($context == 'com_content.article' and $this->params->get('position') == '1')
		{
			$output = $this->getOutputPosition('1');
			
			return $output;
		}
	}
	
	/**
	 * place shariff in your aticles and modules via {shariff} experimental
	*/
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{	
		if($context == 'mod_custom.content' and JString::strpos( $article->text, '{shariff}' )  !== false and $this->params->get('position') == '2')
		{
			$article->text = preg_replace( "#{shariff}#s", $this->getOutputPosition('2'), $article->text );	
		}
	} 
	
	/**
	 * appends the required scripts to the documents and returns the markup
	 *
	 * @param   integer  $position  current position
	 *
	 * @return string
	 */
	public function getOutputPosition($position)
	{
		$setCatId = $this->params->get('showbycategory');
		$currentCatId = JFactory::getApplication()->input->getInt('catid');

		if (($this->params->get('position') == $position) AND ((is_array($setCatId) && in_array($currentCatId, $setCatId)) OR empty($setCatId)))
		{
			$output = $this->getOutput();
			
			return $output;
		}
		
	}

	/**
	* Shariff output generation
	**/
	public function getOutput()
	{
		$output = '';
		
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root() . 'plugins/content/jooag_shariff/assets/shariff.min.css');
		
		$lang = explode("-", JFactory::getLanguage()->getTag());
		JHtml::_('jquery.framework');
		
		//$this->generateJSON();
		
		$services = $this->params->get('services');
		if($this->params->get('info'))
		{
			array_push($services, "info" );
		}
				
		$services = implode("&quot;,&quot;", $services );
		$services = '&quot;' . $services . '&quot;';

		$output = '<div class="shariff" data-theme="' . $this->params->get('theme')
			. '" data-lang="' . $lang[0]
			. '" data-orientation="' . $this->params->get('orientation')
			. '" data-url="' . JURI::getInstance()->toString()
			. '" data-info-url="/index.php?option=com_content&view=article&id='.$this->params->get('info')
			. '" data-services="[' . $services . ']" data-backend-url="/plugins/content/jooag_shariff/backend/" class="shariff"></div>'
			. '<script src="plugins/content/jooag_shariff/assets/shariff.min.js"></script>';
			
		return $output;
	}
	
	/**
	 * writes a file for shariff backend
	 *
	 * @return void
	 */
	public function onExtensionAfterUpdate()
	{
		$jsonString = file_get_contents(JPATH_ROOT . '/plugins/content/jooag_shariff/backend/shariff.json');
		$data = json_decode($jsonString);
		$data->domain = JURI::getInstance()->getHost();
		$data = json_encode($data);
		JFile::write(JPATH_ROOT . '/plugins/content/jooag_shariff/backend/shariff.json', $data);
	}
}
