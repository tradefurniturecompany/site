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

namespace Customweb\RealexCw\Model\Asset;

class TemplateResolver implements \Customweb_Asset_IResolver
{
	/**
	 * @var \Magento\Framework\View\FileSystem
	 */
	protected $_viewFileSystem;
	
	/**
	 * @var array
	 */
	private $fileExtensions = ['phtml'];
	
	/**
	 * @param \Magento\Framework\View\FileSystem $viewFileSystem
	 */
	public function __construct(
			\Magento\Framework\View\FileSystem $viewFileSystem
	) {
		$this->_viewFileSystem = $viewFileSystem;
	}
	
	public function resolveAssetStream($identifier)
	{
		$filePath = $this->_viewFileSystem->getTemplateFileName('Customweb_RealexCw::assets/' . $identifier);
		if (empty($filePath) || !$this->isValidFileExtension($filePath)) {
			throw new \Customweb_Asset_Exception_UnresolvableAssetException($identifier);
		}
		return new \Customweb_Core_Stream_Input_File($filePath);
	}
	
	public function resolveAssetUrl($identifier)
	{
		if (!$this->isValidFileExtension($identifier)) {
			throw new \Customweb_Asset_Exception_UnresolvableAssetException($identifier);
		}
		throw new \Customweb_Asset_Exception_NonPublicAssetException($identifier);
	}
	
	/**
	 * @param string $filePath
	 * @return boolean
	 */
	protected function isValidFileExtension($filePath)
	{
		$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
		if (in_array($fileExtension, $this->fileExtensions)) {
			return true;
		} else {
			return false;
		}
	}
}