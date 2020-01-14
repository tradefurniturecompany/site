<?php
/**
 * Venustheme
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Venustheme.com license that is
 * available through the world-wide-web at this URL:
 * http://www.venustheme.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Venustheme
 * @package    Ves_Megamenu
 * @copyright  Copyright (c) 2014 Venustheme (http://www.venustheme.com/)
 * @license    http://www.venustheme.com/LICENSE-1.0.html
 */
namespace Ves\Megamenu\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	static $arr = array();
	static $categories = array();
	/**
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;

	/**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
	protected $_filterProvider;

	/**
     * @var \Magento\Cms\Model\Template\Filter
     */
	protected $_filter;

	/**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
	protected $_categoryFactory;


	protected $menu;

	/**
     * @var \Magento\Framework\Escaper
     */
	protected $_escaper;

	protected $_cats;

	/**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
	protected $_storeManager;

	protected $_url;

	protected $_catsCollection;

	protected $menuCategories;

	static $_hidden_menu_content_1 = "hidden-xs hidden-sm";
	static $_hidden_menu_content_2 = "hidden-lg hidden-md";
	

	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		\Magento\Cms\Model\Template\Filter $filter,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Escaper $escaper,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Customer\Model\Group $groupManager,
		\Ves\Megamenu\Model\Config\Source\StoreCategories $storeCategories,
		\Magento\Framework\Url $url
		) {
		parent::__construct($context);
		$this->_filterProvider = $filterProvider;
		$this->_filter = $filter;
		$this->_coreRegistry = $registry;
		$this->_categoryFactory = $categoryFactory;
		$this->_escaper = $escaper;
		$this->_storeManager = $storeManager;
		$this->_url = $url->getCurrentUrl();
		$this->mediaUrl         = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$this->_groupCollection = $groupManager;
		$this->storeCategories  = $storeCategories;
	}

	public function filter($str)
	{
		$html = $this->_filterProvider->getPageFilter()->filter($str);
		return $html;
	}

	public function decodeWidgets($str){
		$result = '';
		$imgs = [];
		$firstPosition = 0;
		$i = 0;
		$count = substr_count($str, 'title="{{widget');
		for ($i=0; $i < $count; $i++) {
			if($firstPosition==0) $tmp = $firstPosition;
			$firstPosition = strpos($str, "<img", $tmp);
			$nextPosition = strpos($str, ">", $firstPosition);
			$tmp = $firstPosition;
			$length = $nextPosition - $firstPosition;
			$img = substr($str, $firstPosition, $length+2);
			if( strpos($img, '{{widget')){
				$f = strpos($img, "{{widget", 0);
				$n = strpos($img, '"', $f);
				$widgetCode = substr($img, $f, ($n-$f));
				$widgetHtml = $this->filter(html_entity_decode($widgetCode));
				if($i==0) $result = $str;
				$result = str_replace($img, $widgetHtml, $result);
				$str = str_replace($img, '', $str);
			}
		}

		$count = substr_count($str, 'title="{widget');
		for ($i=0; $i < $count; $i++) {
			if($firstPosition==0) $tmp = $firstPosition;
			$firstPosition = strpos($str, "<img", $tmp);
			$nextPosition = strpos($str, ">", $firstPosition);
			$tmp = $firstPosition;
			$length = $nextPosition - $firstPosition;
			$img = substr($str, $firstPosition, $length+2);
			if( strpos($img, '{widget')){
				$f = strpos($img, "{widget", 0);
				$n = strpos($img, '"', $f);
				$widgetCode = '{' . substr($img, $f, ($n-$f)) . '}';
				$widgetHtml = $this->filter(html_entity_decode($widgetCode));
				if($i==0) $result = $str;
				$result = str_replace($img, $widgetHtml, $result);
				$str = str_replace($img, '', $str);
			}
		}

		$widgets = ['Magento_Widget/placeholder.gif', 'Magento_Catalog/images/product_widget_new.png', 'Magento_CatalogWidget/images/products_list.png'];
		for ($z=0; $z < count($widgets); $z++) {
			$count = substr_count($str, $widgets[$z]);
			for ($i=0; $i < $count; $i++) {
				if($firstPosition==0) $tmp = $firstPosition;
				$firstPosition = strpos($str, "<img", $tmp);
				$nextPosition = strpos($str, ">", $firstPosition);
				$tmp = $firstPosition;
				$length = $nextPosition - $firstPosition;
				$img = substr($str, $firstPosition, $length+2);
				if( strpos($img, 'id="')){
					$f = strpos($img, 'id="', 0);
					$n = strpos($img, '"', $f+4);
					$widgetCode = substr($img, $f+4, ($n-$f-4));
					$widgetCode = str_replace("--", "", $widgetCode);
					$widgetCode = base64_decode($widgetCode);
					$widgetHtml = $widgetCode;
					if($i==0) $result = $str;
					$result = str_replace($img, $widgetHtml, $result);
					$str .= str_replace($img, '', $str);
				}
			}	
		}

		if($result!=''){
			return $result;
		}
		return $str;
	}

	public function getCoreRegistry(){
		return $this->_coreRegistry;
	}

	public function renderMenuItemData($data = [] , $itemBuild = [], $menuItems){
		$data_id = isset($data['id'])?$data['id']:0;
		$itemBuild = isset($menuItems[$data_id])?$menuItems[$data_id]:[];
		$children = [];
		if(isset($data['children']) && (count($data['children'])>0)){
			foreach ($data['children'] as $k => $v) {
				$children[] = $this->renderMenuItemData($v, $itemBuild, $menuItems);
			}
		}
		$itemBuild['children'] = $children;
		return $itemBuild;
	}

	public function getMenu(){
		return $this->menu;
	}

	public function drawAnchor($item){
		$hasChildren = false;
		$tChildren = false;
		if($item['content_type'] == 'parentcat'){
			$catChildren = $this->getTreeCategories($item['parentcat']);
			if($catChildren) $tChildren = true;
		}
		if(($item['show_footer'] && $item['footer_html']!='') || ($item['show_header'] && $item['header_html']!='') ||  ($item['show_left_sidebar'] && $item['left_sidebar_html']!='') || ($item['show_right_sidebar'] && $item['right_sidebar_html']!='') || ($item['show_content'] && ((($item['content_type'] == 'childmenu' || $item['content_type'] == 'dynamic') && (isset($item['children']) && count($item['children'])>0)) || ($item['content_type'] == 'content' && $item['content_html']!=''))) || ($item['content_type'] == 'parentcat' && $tChildren) ) $hasChildren = true;

		$html = $class = $style = $attr = '';

		// Design
		if(isset($item['color']) && $item['color']!=''){
			$style .= 'color: '.$item['color'].';';
		}
		if(isset($item['bg_color']) && $item['bg_color']!=''){
			$style .= 'background-color: '.$item['bg_color'].';';
		}
		if(isset($item['inline_css']) && $item['inline_css']!=''){
			$style .= $item['inline_css'];
		}
		if($style!='') $style = 'style="' . $style . '"';

		$class .= ' nav-anchor';

		if($item['content_type'] == 'dynamic') $class .= ' subdynamic';
		if($item['is_group']) $class .= ' subitems-group';

		// Custom Link, Category Link
		$href = '';
		$onclick = '';
		if($item['link_type'] == 'custom_link'){
			$link = $item['link'];

			if((strpos($link, ":javascript:") !== false) || (strpos($link, ":JAVASCRIPT:") !== false)){
				$href = '#';
				$onclick = str_replace(array(":javascript:", ":JAVASCRIPT:"), "", $link);
				$onclick = str_replace('"', "'", $onclick);
				$onclick = trim($onclick);
			} else {
				$href = $this->filter($item['link']);
				if($this->endsWith($href, '/')){
					$href = substr_replace($href, "", -1);
				}
			}
			
		}elseif($item['link_type'] == 'category_link'){
			if ($category = $this->getCategory($item['category'])) {
				$href = $category['url'];
				if($urls = parse_url($href)){
					$url_host = isset($urls['host'])?$urls['host']:"";
					$base_url = $this->_storeManager->getStore()->getBaseUrl();
					if($url_host && ($base_urls = parse_url($base_url))) {
						$base_urls['host'] = isset($base_urls['host'])?$base_urls['host']:"";
						if($url_host != $base_urls['host']){
							$href = str_replace($url_host, $base_urls['host'], $href);
						}
					}
				}
			}
		}

		if($class!='') $class = 'class="' . $class . '"';

		// Attributes
		if(isset($item['hover_color']) && $item['hover_color']){
			$attr .= ' data-hover-color="'.$item['hover_color'].'"';
		}
		if(isset($item['bg_hover_color']) && $item['bg_hover_color']){
			$attr .= ' data-hover-bgcolor="'.$item['bg_hover_color'].'"';
		}

		if(isset($item['color']) && $item['color']){
			$attr .= ' data-color="'.$item['color'].'"';
		}

		if(isset($item['bg_color']) && $item['bg_color']){
			$attr .= ' data-bgcolor="'.$item['bg_color'].'"';
		}

		$onclick_attr = '';
        if($onclick) {
            $onclick_attr = ' onclick="'.$onclick.'"';
        }

		$target = $item['target']?'target="' . $item['target'] . '"':'';
		if($href=='') $href = '#';
		if($href == '#') $target = '';
		$html .= '<a href="' . $href . '" ' . $target . ' ' . $attr . ' ' . $style . ' ' . $class .$onclick_attr. '>';

		if($item['show_icon']){
			$html .= '<i class="' .$item['icon_classes'] . '"></i>';
		}

		// Icon Left
		if($item['show_icon'] && $item['icon_position']=='left' && $item['icon']!=''){
			$html .= '<img class="menu-icon icon-left" src="'.$item['icon'].'" alt="'.$item['name'].'"/>';
			if(isset($item['hover_icon']) && $item['hover_icon']) {
				$html .= '<img class="menu-icon icon-left icon-hover" src="'.$item['hover_icon'].'" style="display:none" alt="'.$item['name'].'"/>';
			}
		}

		if($item['name']!=''){
			$html .= '<span>' . $item['name'] . '</span>';
		}

		// Icon Right
		if($item['show_icon'] && $item['icon_position']=='right' && $item['icon']!=''){
			$html .= '<img class="menu-icon icon-right" src="'.$item['icon'].'" alt="'.$item['name'].'"/>';
			if(isset($item['hover_icon']) && $item['hover_icon']) {
				$html .= '<img class="menu-icon icon-right icon-hover" src="'.$item['hover_icon'].'" style="display:none" alt="'.$item['name'].'"/>';
			}
		}

		if($hasChildren) $html .= '<span class="caret"></span><span class="opener"></span>';

		$html .= '</a>';
		return $html;
	}

	public function drawItem($item, $level = 0, $x = 0, $listTag = true){
		$hasChildren = false;
		$class = $style = $attr = '';
		if(isset($item['class'])) $class = $item['class'];
		if(!isset($item['status']) || (isset($item['status']) && !$item['status'])) return;
		if(isset($item['children']) && count($item['children'])>0) $hasChildren = true;

		$class .= ' nav-item level' . $level . ' nav-' . $x;
		// Item Align Type
		if($item['align'] == '1'){
			$class .= ' submenu-left';
		}elseif($item['align'] == '2'){
			$class .= ' submenu-right';
		}elseif($item['align'] == '3'){
			$class .= ' submenu-alignleft';
		}elseif($item['align'] == '4'){
			$class .= ' submenu-alignright';
		}

		// Group Childs Item
		if($item['is_group']){
			$class .= ' subgroup ';
		}else{
			$class .= ' subhover ';
		}

		if($item['content_type'] == 'dynamic') $class .= ' subdynamic';

		// Disable Dimesion
		if(((int)$item['disable_bellow'])>0)
			$attr .= 'data-disable-bellow="'.$item['disable_bellow'].'"';
		
		if($level==0){
			$class .=' dropdown level-top';
		}else{
			$class .=' dropdown-submenu';
		}
		$class .= ' '.$item['classes'];

		// Custom Link, Category Link
		$href = '';
		$onclick = '';
		if($item['link_type'] == 'custom_link'){
			$href = $item['link'];

			if((strpos($href, ":javascript:") !== false) || (strpos($href, ":JAVASCRIPT:") !== false)){
				$href = '#';
				$onclick = str_replace(array(":javascript:", ":JAVASCRIPT:"), "", $href);
				$onclick = str_replace('"', "'", $onclick);
				$onclick = trim($onclick);
			}
		}elseif($item['link_type'] == 'category_link'){
			if ($category = $this->getCategory($item['category'])) {
				$href = $category['url'];
				if($urls = parse_url($href)){
					$url_host = isset($urls['host'])?$urls['host']:"";
					$base_url = $this->_storeManager->getStore()->getBaseUrl();
					if($url_host && ($base_urls = parse_url($base_url))) {
						$base_urls['host'] = isset($base_urls['host'])?$base_urls['host']:"";
						if($url_host != $base_urls['host']){
							$href = str_replace($url_host, $base_urls['host'], $href);
						}
					}
				}
			}
		}

		$link = $this->filter($href);
		$link = trim($link);
		if($this->endsWith($link, '/')){
			$link = substr_replace($link, "", -1);
		}
		$currentUrl = trim($this->_url);
		$currentUrl = $this->filter($currentUrl);
		if($this->endsWith($currentUrl, '/')){
			$currentUrl = substr_replace($currentUrl, "", -1);
		}
		if($link == $currentUrl && ($href != '' && $href!='#')){
			$class .= ' active';
		}

		if($listTag){
			if($class!='') $class = 'class="' . $class . '"';
			$html = '<li id=' . $item['htmlId'] . ' ' . $class . ' ' . $style . ' ' . $attr . '>';
		}else{
			if(isset($item['dynamic'])){
				$class .= ' dynamic-item '.$item['htmlId'];
			}
			if($class!='') $class = 'class="' . $class . '"';
			$html = '<div ' . $class . ' ' . $style . ' ' . $attr . '>';
		}

		if(!isset($item['dynamic'])) $html .= $this->drawAnchor($item);
		$tChildren = false;
		$catChildren = [];
		if($item['content_type'] == 'parentcat'){
			$catChildren = $this->getTreeCategories($item['parentcat']);
			if($catChildren) $tChildren = true;
		}
		if(($item['show_footer'] && $item['footer_html']!='') || ($item['show_header'] && $item['header_html']!='') ||  ($item['show_left_sidebar'] && $item['left_sidebar_html']!='') || ($item['show_right_sidebar'] && $item['right_sidebar_html']!='') || ($item['show_content'] && ((($item['content_type'] == 'childmenu' || $item['content_type'] == 'dynamic') && (isset($item['children']) && count($item['children'])>0)) || ($item['content_type'] == 'content' && $item['content_html']!=''))) || ($item['content_type'] == 'parentcat' && $tChildren) ){
			$level++;
			$subClass = $subStyle = $subAttr = '';

			if($item['sub_width']!='') $subStyle .= 'width:'.$item['sub_width'].';';

			if(isset($item['dropdown_bgcolor']) && $item['dropdown_bgcolor']) $subStyle .= 'background-color:'.$item['dropdown_bgcolor'].';';
			if(isset($item['dropdown_bgimage']) && $item['dropdown_bgimage']){
				if(!$item['dropdown_bgpositionx']) $item['dropdown_bgpositionx'] = 'center';
				if(!$item['dropdown_bgpositiony']) $item['dropdown_bgpositiony'] = 'center';
				$subStyle .= 'background: url(\''.$item['dropdown_bgimage'].'\') ' . $item['dropdown_bgimagerepeat'] . ' ' . $item['dropdown_bgpositionx'] . ' ' . $item['dropdown_bgpositiony'] . ' ' . $item['dropdown_bgcolor'] . ';' ;
			}
			if(isset($item['dropdown_inlinecss']) && $item['dropdown_inlinecss']) $subStyle .= $item['dropdown_inlinecss'];
			
			$subClass .= 'submenu';
			
			if(isset($item['animation_in'])){
				$subClass .= ' animated ';
				$subClass .= $item['animation_in'];
				if($item['animation_in']){
					$subAttr .= ' data-animation-in="' . $item['animation_in'] . '"';
				}
				if($item['animation_time']){
					$subStyle .= 'animation-duration: ' . $item['animation_time'] . 's;-webkit-animation-duration: ' . $item['animation_time'] . 's;';
				}
			}

			if($item['is_group']){
				$subClass .= ' dropdown-mega';
			}else{
				$subClass .= ' dropdown-menu';
			}
			if($subClass!='') $subClass = 'class="' . $subClass . '"';
			if($subStyle!='') $subStyle = 'style="' . $subStyle . '"';

			if(!isset($item['dynamic']))
				$html .= '<div ' . $subClass . ' ' . $subStyle . '>';
		
			
			// TOP BLOCK
			if($item['show_header'] && $item['header_html']!=''){
				$html .= '<div class="megamenu-header">' . $this->decodeWidgets($item['header_html']) . '</div>';
			}

			if($item['show_left_sidebar'] || $item['show_content'] || $item['show_right_sidebar']){
				if(isset($item['dynamic']) && $item['dynamic']) {
					$html .= '<div class="content-wrap"'.' ' . $subStyle .'>';
				} else {
					$html .= '<div class="content-wrap">';
				}
				

				$left_sidebar_width = isset($item['left_sidebar_width'])?$item['left_sidebar_width']:0;
				$content_width = $item['content_width'];
				$right_sidebar_width = isset($item['right_sidebar_width'])?$item['right_sidebar_width']:0;

				// LEFT SIDEBAR BLOCK
				if($item['show_left_sidebar'] && $item['left_sidebar_html']!=''){
					if($left_sidebar_width) $left_sidebar_width = 'style="width:'.$left_sidebar_width.'"';
					
					$html .= '<div class="megamenu-sidebar left-sidebar" '.$left_sidebar_width.'>'.$this->decodeWidgets($item['left_sidebar_html']).'</div>';
				}
				// MAIN CONTENT BLOCK
				if($item['show_content'] && ((($item['content_type'] == 'childmenu' || $item['content_type'] == 'dynamic') && $hasChildren) || $item['content_type'] == 'parentcat' || ($item['content_type'] == 'content' && $item['content_html']!=''))){
					$html .= '<div class="megamenu-content" '.($content_width==''?'':'style="width:'.$content_width.'"').'>';

					// Content HTML
					if($item['content_type'] == 'content' && $item['content_html']!=''){
						$html .= '<div class="nav-dropdown">' . $this->decodeWidgets($item['content_html']) . '</div>';
					}

					// Dynamic Tab
					if($item['content_type'] == 'dynamic' && $hasChildren){
						$html .= '<div class="level' . $level . ' nav-dropdown">';
						$children = $item['children'];
						$i = 0;
						$total = count($children);
						$column = (int)$item['child_col'];
						$z = 0;
						$html .= '<div class="dorgin-items row hidden-sm hidden-xs">';
						$html .= '<div class="dynamic-items col-xs-3 hidden-xs hidden-sm">';
						$html .= '<ul>';
						foreach ($children as $it) {
							$iClass = '';
							if($z==0){
								$iClass = 'class="dynamic-active"';
							}
							$html .= '<li ' . $iClass . ' data-dynamic-id="' . $it['htmlId'] . '">';
							$html .= $this->drawAnchor($it, $level);
							$html .= '</li>';
							$i++;
							$z++;
						}
						$html .= '</ul>';
						$html .= '</div>';
						$html .= '<div class="dynamic-content col-xs-9 hidden-xs hidden-sm">';
						$z = 0;
						foreach ($children as $it) {
							if($z==0){ $it['class'] = 'dynamic-active'; }
							$it['dynamic'] = true;
							$html .= $this->filter($this->drawItem($it, $level, $i, false));
							$i++;
							$z++;
						}
						$html .= '</div>';
						$html .= '</div>';

						$html .= '<div class="orgin-items hidden-lg hidden-md">';
						$i = 0;
						$column = 1;
						foreach ($children as $it) {
							if( $column == 1 || $i%$column == 0){
								$html .= '<div class="row">';
							}
							$html .= '<div class="mega-col col-sm-' . (12/$column) . ' mega-col-' . $i . ' mega-col-level-' . $level . '">';
							$html .= $this->filter($this->drawItem($it, $level, $i, false));
							$html .= '</div>';
							if( $column == 1 || ($i+1)%$column == 0 || $i == ($total-1) ) {
								$html .= '</div>';
							}
							$i++;
						}
						$html .= '</div>';


						$html .= '</div>';
					}

					// Child item
					if ($item['content_type'] == 'childmenu' && $hasChildren) {
						$column = (int)$item['child_col'];
						$html .= '<div class="level' . $level . ' nav-dropdown ves-column' . $column . '">';
						$children = $item['children'];
						$i = 0;
						$total = count($children);
						
						$resultTmp = [];
						$x1 = 0;
						$levelTmp =1;
						foreach ($children as  $z => $it) {
							$resultTmp[$x1][$levelTmp] = $this->drawItem($it, $level, $i, false);
							if ($x1==$column-1 || $i == (count($children)-1)) {
								$levelTmp++;
								$x1=0;
							} else {
								$x1++;
							}
							$i++;
						}
						$html .= '<div class="item-content1 '.self::$_hidden_menu_content_1.'">';
						foreach ($resultTmp as $k1 => $v1) {
							$html .= '<div class="mega-col mega-col-' . $i . ' mega-col-level-' . $level . '">';
							foreach ($v1 as $k2 => $v2) {
								$html .= $v2;
							}
							$html .= '</div>';
						}
						$html .= '</div>';
						$html .= '<div class="item-content2 '.self::$_hidden_menu_content_2.'">';
						foreach ($children as  $z => $it) {
							$html .= $this->filter($this->drawItem($it, $level, $i, false));
						}
						$html .= '</div>';
						$html .= '</div>';
					}
					
					// Child item
					if($item['content_type'] == 'parentcat'){
						$html .= '<div class="level' . $level . ' nav-dropdown">';
						$catChildren = $this->getTreeCategories($item['parentcat']);

						$i = 0;
						$total = count($catChildren);
						$column = (int)$item['child_col'];
						foreach ($catChildren as $it) {
							if( $column == 1 || $i%$column == 0){
								$html .= '<div class="row">';
							}
							$html .= '<div class="mega-col col-sm-' . (12/$column) . ' mega-col-' . $i . ' mega-col-level-' . $level . ' col-xs-12">';
							$html .= $this->drawItem($it, $level, $i, false);
							$html .= '</div>';
							if( $column == 1 || ($i+1)%$column == 0 || $i == ($total-1) ) {
								$html .= '</div>';
							}
							$i++;
						}
						$html .= '</div>';

					}
					$html .= '</div>';
				}

				// RIGHT SIDEBAR BLOCK
				if($item['show_right_sidebar'] && $item['right_sidebar_html']!=''){
					if($right_sidebar_width) $right_sidebar_width = 'style="width:' . $right_sidebar_width . '"';
					$html .= '<div class="megamenu-sidebar right-sidebar" '.$right_sidebar_width.'>'.$this->decodeWidgets($item['right_sidebar_html']).'</div>';
				}

				$html .= '</div>';
			}

			// BOOTM BLOCK
			if( $item['show_footer'] && $item['footer_html']!=''){
				$html .= '<div class="megamenu-footer">'.$this->decodeWidgets($item['footer_html']).'</div>';
			}

			if(!isset($item['dynamic']))
				$html .= '</div>';
		}
		if($listTag){
			$html .= '</li>';
		}else{
			$html .= '</div>';	
		}
		$html= $this->decodeImg($html);
		return $html;
	}

	public function decodeImg($str){
		$count = substr_count($str, "<img");
		$mediaUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		$firstPosition = 0;
		for ($i=0; $i < $count; $i++) {
			if($firstPosition==0) $tmp = $firstPosition;
			$firstPosition = strpos($str, "<img", $tmp);
			$nextPosition = strpos($str, ">", $firstPosition);
			$tmp = $nextPosition;
			if(!strpos($str, "<img", $tmp)) continue;
			$length = $nextPosition - $firstPosition;
			$img = substr($str, $firstPosition, $length+2);
			$newImg = $this->filter($img);
			$post_length = strlen($newImg);
			$f = strpos($newImg, 'src="', 0)+5;
			if($post_length < ($f+5)) {
				$n = strpos($newImg, '"', $post_length-1);
			} else {
				$n = strpos($newImg, '"', $f+5);
			}
			
			$src = substr($newImg, $f, ($n-$f));
			if( !strpos($img, 'placeholder.gif')){
				$src1 = '';
				if( strpos($newImg, '___directive')){
					$e = strpos($newImg, '___directive', 0) + 13;
					$e1 = strpos($newImg, '/key', 0);
					$src1 = substr($newImg, $e, ($e1-$e));
					$src1 = base64_decode($src1);
				}elseif(!substr_count($src, $mediaUrl)){
					$mediaP = strpos($src, "wysiwyg", 0);
					$src1 = substr($src, $mediaP);
					$src1 = '{{media url="'.$src1.'"}}';
				}
				if($src1){
					try{
						$newImg = str_replace($src, $src1, $newImg);
						$str = str_replace($img, $newImg, $str);
					}catch(\Exception $e){

					}
				}
			}
		}
		return $str;
	}

	public function decodeAnchor($str) {
		$tmpStr        = $str;
		$startString   =  'href="';
		$endString     = '"';
		$count         = substr_count($str, $startString);
		$firstPosition = 0;
		for ($i=0; $i < $count; $i++) {
			if($firstPosition==0) $tmp = $firstPosition;
			$firstPosition = strpos($str, $startString, $tmp);
			$nextPosition  = strpos($str, $endString, $firstPosition + strlen($startString));
			$tmp           = $nextPosition;

			if (!strpos($str, $startString, $firstPosition)) {
				continue;
			}
			$length = $nextPosition - $firstPosition - strlen($startString);
			$img    = substr($str, $firstPosition + strlen($startString), $length);
			if(strpos($img, "cms/wysiwyg/directive/___directive/")) {
				$imgTmp = explode("cms/wysiwyg/directive/___directive/", $img);
				if (count($imgTmp) == 2) {
					if(strpos($imgTmp[1], "/key")) {
						$imgTmp = explode("/key", $imgTmp[1]);
						if (count($imgTmp) == 2) {
							$tmpStr = str_replace($img, base64_decode($imgTmp[0]), $tmpStr);
						}
					} else {
						$tmpStr = str_replace($img, base64_decode($imgTmp[1]), $tmpStr);
					}
				}
			}
		}
		return $tmpStr;
	}

	public function getAllCategory(){
		if(!$this->_cats){
			$this->_cats = $this->_categoryFactory->create()->getCollection()
			->addAttributeToSelect('*')
			->addAttributeToFilter('is_active','1')
			->addAttributeToFilter('include_in_menu',1)
			->addAttributeToSort('position', 'asc');
			if(($store = $this->_storeManager->getStore()) && $this->_cats instanceof \Magento\Catalog\Model\ResourceModel\Category\Collection) {
				$this->_cats->setStore($store);
			}
		}
		return $this->_cats;
	}

	public function getTreeCategories($parentId, $level = 0, $list = [], $max_level = 100){
		$cats = $this->getAllCategory();
		foreach($cats as $category)
		{
			if($category->getParentId() == $parentId){
				$tmp = [];
				$category->setStoreId($this->_storeManager->getStore()->getId());
				$tmp["name"] = $category->getName();
				$tmp['link_type'] = 'custom_link';
				$tmp['image']	= $category->getImageUrl();
				$tmp['link'] = $category->getUrl();
				$tmp['show_footer'] = $tmp['show_header'] = $tmp['show_left_sidebar'] = $tmp['show_right_sidebar'] = 0;
				$tmp['show_content'] = 1;
				$tmp['content_width'] = $tmp['sub_width'] = '100%';
				$tmp['color'] = '';
				$tmp['show_icon'] = $tmp['is_group'] = false;
				$tmp['content_type'] = 'childmenu';
				$tmp['target'] = '_self';
				$tmp['align'] = 3;
				$tmp['child_col'] = 1;
				$tmp['status'] = 1;
				$tmp['disable_bellow'] = 0;
				$tmp['classes'] = '';
				$tmp['id'] = $category->getId();
				if($urls = parse_url($tmp['link'])){
					$url_host = isset($urls['host'])?$urls['host']:"";
					$base_url = $this->_storeManager->getStore()->getBaseUrl();
					//echo $base_url;die();
					if($url_host && ($base_urls = parse_url($base_url))) {
						$base_urls['host'] = isset($base_urls['host'])?$base_urls['host']:"";
						if($url_host != $base_urls['host']){
							$tmp['link'] = str_replace($url_host, $base_urls['host'], $tmp['link']);
						}
					}
				}

				$subcats = $category->getChildren();
				if($subcats){
					$next_level = (int)$level + 1;
					if($next_level < $max_level){
						$tmp['children'] = $this->getTreeCategories($category->getId(),(int)$level + 1, [], $max_level);
					}
				}
				$list[] = $tmp;
			}
		}
		return $list;
	}

	function endsWith($haystack, $needle) {
    // search forward starting from end minus needle length characters
		return $needle === "" || (($temp = strlen($haystack) - strlen($needle)) >= 0 && strpos($haystack, $needle, $temp) !== false);
	}

	/**
     * Return brand config value by key and store
     *
     * @param string $key
     * @param \Magento\Store\Model\Store|int|string $store
     * @return string|null
     */
    public function getConfig($key, $store = null)
    {
        $store = $this->_storeManager->getStore($store);
        $websiteId = $store->getWebsiteId();

        $result = $this->scopeConfig->getValue(
            'vesmegamenu/'.$key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store);
        return $result;
    }

	public function getCustomerGroups()
	{
		$data_array = array();

		$customer_groups = $this->_groupCollection->getCollection();

		foreach ($customer_groups as $item_group) {
			$data_array[] =  array('value' => $item_group->getId(), 'label' => $item_group->getCode());
		}

		return $data_array;

	}

	public function getCategory($catId, $cat = '') {
		$catId = (int) $catId;
		if ($cat == '') {
			$cat = $this->getRootCategory();
		}
		$category = '';
		if ($cat) {
			if ((int) $cat['value'] == (int) $catId) {
				return $cat['category'];
			} else if (isset($cat['children']) && is_array($cat['children'])) {
				foreach ($cat['children'] as $catChild) {
					$category = $this->getCategory($catId, $catChild);
					if ($category) {
						break;
					}
				}
			}
		}
		return $category;
	}

	public function getRootCategory() {
		if (!$this->_catsCollection) {
			$cats   = null;
			if($menuCategories = $this->getMenuCategories()) {
				$cats   = $this->storeCategories->getCategoriesCollection($menuCategories, null, $this->_storeManager->getStore()->getId());
			}
			$rootId = $this->_storeManager->getStore()->getRootCategoryId();

			if ($cats) {
				foreach ($cats as $cat) {
					if ($cat['value'] == $rootId) {
						$this->_catsCollection = $cat;
						break;
					}
				}
			} else {
				$this->_catsCollection = [];
			}
		}
		return $this->_catsCollection;
	}

    public function getMenuCategories() {
    	return $this->menuCategories;
    }

    public function setMenuCategories($categories) {
    	$this->menuCategories = $categories;
    	return $this;
    }
    // HTML Minifier
    public function minify_html($input) {
        if(trim($input) === "") return $input;
        // Remove extra white-space(s) between HTML attribute(s)
        $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function($matches) {
            return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
        }, str_replace("\r", "", $input));
        // Minify inline CSS declaration(s)
        if(strpos($input, ' style=') !== false) {
            $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function($matches) {
                return '<' . $matches[1] . ' style=' . $matches[2] . $this->minify_css($matches[3]) . $matches[2];
            }, $input);
        }
        return preg_replace(
            array(
                // t = text
                // o = tag open
                // c = tag close
                // Keep important white-space(s) after self-closing HTML tag(s)
                '#<(img|input)(>| .*?>)#s',
                // Remove a line break and two or more white-space(s) between tag(s)
                '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
                '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
                '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
                '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
                '#<(img|input)(>| .*?>)<\/\1>#s', // reset previous fix
                '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
                '#(?<=\>)(&nbsp;)(?=\<)#', // --ibid
                // Remove HTML comment(s) except IE comment(s)
                '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
            ),
            array(
                '<$1$2</$1>',
                '$1$2$3',
                '$1$2$3',
                '$1$2$3$4$5',
                '$1$2$3$4$5$6$7',
                '$1$2$3',
                '<$1$2',
                '$1 ',
                '$1',
                ""
            ),
        $input);
    }
    // CSS Minifier => http://ideone.com/Q5USEF + improvement(s)
    public function minify_css($input) {
        if(trim($input) === "") return $input;
        return preg_replace(
            array(
                // Remove comment(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
                // Remove unused white-space(s)
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
                // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
                '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
                // Replace `:0 0 0 0` with `:0`
                '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
                // Replace `background-position:0` with `background-position:0 0`
                '#(background-position):0(?=[;\}])#si',
                // Replace `0.6` with `.6`, but only when preceded by `:`, `,`, `-` or a white-space
                '#(?<=[\s:,\-])0+\.(\d+)#s',
                // Minify string value
                '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][a-z0-9\-_]*?)\2(?=[\s\{\}\];,])#si',
                '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
                // Minify HEX color code
                '#(?<=[\s:,\-]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
                // Replace `(border|outline):none` with `(border|outline):0`
                '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
                // Remove empty selector(s)
                '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s'
            ),
            array(
                '$1',
                '$1$2$3$4$5$6$7',
                '$1',
                ':0',
                '$1:0 0',
                '.$1',
                '$1$3',
                '$1$2$4$5',
                '$1$2$3',
                '$1:0',
                '$1$2'
            ),
        $input);
    }

    // JavaScript Minifier
    public function minify_js($input) {
        if(trim($input) === "") return $input;
        return preg_replace(
            array(
                // Remove comment(s)
                '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
                // Remove white-space(s) outside the string and regex
                '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
                // Remove the last semicolon
                '#;+\}#',
                // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
                '#([\{,])([\'])(\d+|[a-z_][a-z0-9_]*)\2(?=\:)#i',
                // --ibid. From `foo['bar']` to `foo.bar`
                '#([a-z0-9_\)\]])\[([\'"])([a-z_][a-z0-9_]*)\2\]#i'
            ),
            array(
                '$1',
                '$1$2',
                '}',
                '$1$3',
                '$1.$3'
            ),
        $input);
    }
}
