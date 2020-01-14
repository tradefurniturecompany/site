<?php
/**
 * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_RealexCw
 * 
 */

namespace Customweb\RealexCw\Model\Adapter;

class LayoutRenderer extends \Customweb_Mvc_Layout_Renderer
{
	/**
	 * @var \Magento\Framework\App\View
	 */
	private $_view;

	/**
	 * @var \Magento\Framework\App\RequestInterface
	 */
	protected $_request;

	/**
	 * @var \Magento\Framework\App\ResponseInterface
	 */
	private $_response;

	/**
	 * @param \Magento\Framework\App\View $view
	 * @param \Magento\Framework\App\RequestInterface $request
	 * @param \Magento\Framework\App\ResponseInterface $response
	 */
	public function __construct(
		\Magento\Framework\App\View $view,
		\Magento\Framework\App\RequestInterface $request,
		\Magento\Framework\App\ResponseInterface $response
	) {
		$this->_view = $view;
		$this->_request = $request;
		$this->_response = $response;
	}

	public function render(\Customweb_Mvc_Layout_IRenderContext $context)
	{
		$this->initView();
		$this->setTitle($context->getTitle());
		$this->addAssets($context->getJavaScriptFiles(), 'js');
		$this->addAssets($context->getCssFiles(), 'css');
		$this->setContent($context->getMainContent());
		return $this->getOutput();
	}

	private function initView()
	{
		$this->_view->loadLayout(['default', 'realexcw_layout_renderer'], true, true, false);
		$this->_view->getPage()->getConfig()->setPageLayout('1column');
		$this->_view->getLayout()->unsetElement('sidebar.additional');
		$this->_view->getLayout()->unsetElement('page.main.title');
		$this->_view->getPage()->getConfig()->addPageAsset('Customweb_RealexCw::css/realexcw.css');
	}

	/**
	 * @param string $title
	 */
	private function setTitle($title)
	{
		$this->_view->getPage()->getConfig()->getTitle()->set($title);
	}

	/**
	 * @param string[] $assets
	 * @param string $contentType
	 */
	private function addAssets($assets, $contentType)
	{
		foreach ($assets as $asset) {
			$this->_view->getPage()->getConfig()->addRemotePageAsset($asset, $contentType);
		}
	}

	/**
	 * @param string $content
	 */
	private function setContent($content)
	{
		$this->_view->getLayout()->addBlock('\Magento\Framework\View\Element\Text', 'realexcw.main.content', 'content')->setText($content);
	}

	/**
	 * @return string
	 */
	private function getOutput()
	{
		$this->_view->renderLayout();
		return $this->_response->getContent();
	}
}