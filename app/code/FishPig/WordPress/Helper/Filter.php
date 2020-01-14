<?php
/*
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

namespace FishPig\WordPress\Helper;

use \Magento\Framework\App\Helper\Context;
use \FishPig\WordPress\Model\App;
use \FishPig\WordPress\Model\Config;
use \Magento\Cms\Model\Template\FilterProvider;
use \Magento\Framework\Module\Dir\Reader;

class Filter extends \Magento\Framework\App\Helper\AbstractHelper
{
	/*
	 * @var \FishPig\WordPress\Model\App
	 */
	protected $app = null;
	
	/*
	 * @var \FishPig\WordPress\Model\Config
	 */
	protected $config = null;

  /*
   * @var \Magento\Cms\Model\Template\FilterProvider
   */
  protected $_filterProvider;
    
  /*
	 * @var \Magento\Framework\Module\Dir\Reader
	 */
  protected $dirReader;
  
	/*
	 * @var array
	 */
	static protected $assetInjectionShortcodes = [];
	
	/*
	 *
	 *
	 * @return void
	 */
	public function __construct(Context $context, App $app, Config $config, FilterProvider $filterProvider, Reader $dirReader)
	{
		parent::__construct($context);

		$this->app = $app->init();
		$this->config = $config;
		$this->filterProvider = $filterProvider;
		$this->dirReader = $dirReader;
	}
	
	/*
	 * Call autop on the string
	 * Then go through each shortcode and try to apply
	 * Finally go through each shortcode again to check if another shortcode
	 * has handled it and if so add it to the assetInjectionShortcodes array
	 *
	 * @param $string
	 * @param $object = null
	 * @return string
	 */
	public function process($string, $object = null)
	{
		$string = trim($this->addParagraphTagsToString($string));

		return $this->doShortcode($string, $object);
	}

	/*
	 * Go through each shortcode and try to apply
	 * Finally go through each shortcode again to check if another shortcode
	 * has handled it and if so add it to the assetInjectionShortcodes array
	 *
	 * @param $string
	 * @param $object = null
	 * @return string
	 */
	public function doShortcode($content, $object = null)
	{
		if ($shortcodes = $this->config->getShortcodes()) {
			foreach($shortcodes as $alias => $shortcodeInstance) {
				// Parse $content and try to inject shortcode HTML
				$newContent = trim((string)$shortcodeInstance->setObject($object)->setValue($content)->process());
				
				// Content has changed so check for injection
				if ($content !== $newContent) {

					// Update $content with the updated content
					$content = $newContent;

					// Check if shortcode requires JS/CSS injection
					if ($shortcodeInstance->requiresAssetInjection()) {
						self::$assetInjectionShortcodes[get_class($shortcodeInstance)] = $shortcodeInstance;
					}
				}
			}

			// We might not need this
			// Keep it from running for now
			if (false) {
				// Now go through shortcodes and check for required assets against $content
				foreach($shortcodes as $alias => $shortcodeInstance) {
					if (!isset(self::$assetInjectionShortcodes[get_class($shortcodeInstance)])) {
						if ($shortcodeInstance->requiresAssetInjection($content)) {
							self::$assetInjectionShortcodes[get_class($shortcodeInstance)] = $shortcodeInstance;
						}
					}
				}
			}
		}
		
		// Filter the content for {{block and {{widget
		if (strpos($content, '{{') !== false) {
			if (preg_match_all('/\{\{([^\n]{1,})\}\}/Us', $content, $matches)) {
				foreach($matches[0] as $key => $value) {
					$content = str_replace($value, html_entity_decode($value), $content);
				}
			}

			$content = $this->filterProvider->getPageFilter()->filter($content);
		}
		
		return $content;
	}
	
	/*
	 *
	 *
	 * @return 
	 */
	public function getAssetInjectionShortcodes()
	{
		$shortcodes = $this->config->getShortcodes();
		
		if (isset($shortcodes['pluginshortcodewidget'])) {
			return ['pluginshortcodewidget' => $shortcodes['pluginshortcodewidget']];
		}
		
		return self::$assetInjectionShortcodes;
	}
	
	/*
	 *
	 *
	 * @return 
	 */
	public function addParagraphTagsToString($string)
	{
		if ($this->_getFunctionFromWordPress('wpautop', 'wp-includes' . DIRECTORY_SEPARATOR . 'formatting.php', array(
			'wpautop',
			'wp_replace_in_html_tags',
			'_autop_newline_preservation_helper',
			'wp_html_split',
			'get_html_split_regex',
		))) {
			$string = fp_wpautop($string);
			
			// Fix shortcodes that get P'd off!
			$string = preg_replace('/<p>\[/', '[', $string);
			$string = preg_replace('/\]<\/p>/', ']', $string);
		}

		return $string;
	}
	
	/*
	 *
	 *
	 * @return 
	 */
	protected function _getFunctionFromWordPress($function, $file, $depends = array())
	{
		$newFunction = 'fp_' . $function;
		
		if (function_exists($newFunction)) {
			return true;
		}

		// Get file from WordPress
//	$targetFile = $this->app->getPath() . DIRECTORY_SEPARATOR . $file;

		// Get file from Magento
		$targetFile = $this->dirReader->getModuleDir('', 'FishPig_WordPress') . DIRECTORY_SEPARATOR . 'WordPress' . DIRECTORY_SEPARATOR . $file;
		
		if (!is_file($targetFile)) {
			return false;
		}

		$code = preg_replace('/\/\*\*.*\*\//Us', '', file_get_contents($targetFile));

		$depends = array_flip($depends);
		foreach($depends as $key => $value) {
			$depends[$key] = '';
		}

		foreach($depends as $function => $ignore) {
			if (preg_match('/(function ' . $function . '\(.*)function/sU', $code, $matches)) {
				$depends[$function] = $matches[1];
			}
			else {
				return false;
			}
		}
		
		$code = preg_replace('/(' . implode('|', array_keys($depends)) . ')/', 'fp_$1', implode("\n\n", $depends));
		
		@eval($code);

		return function_exists($newFunction);
	}
}
