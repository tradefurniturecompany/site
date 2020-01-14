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

abstract class StaticResolver implements \Customweb_Asset_IResolver
{
	/**
	 * @var \Magento\Framework\Filesystem
	 */
	protected $_filesystem;

	/**
	 * @var \Magento\Framework\View\Asset\Repository
	 */
	protected $_assetRepository;

	/**
	 * @var \Magento\Framework\App\View\Asset\Publisher
	 */
	protected $_assetPublisher;

	/**
	 * @var string
	 */
	private $directory;

	/**
	 * @var array
	 */
	private $fileExtensions;

	/**
	 * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
	 * @param \Magento\Framework\View\Asset\Repository $assetRepository
	 * @param string $directory
	 * @param array $fileExtensions
	 */
	public function __construct(
			\Magento\Framework\Filesystem $filesystem,
			\Magento\Framework\View\Asset\Repository $assetRepository,
			\Magento\Framework\App\View\Asset\Publisher $assetPublisher,
			$directory,
			$fileExtensions = null
	) {
		$this->_filesystem = $filesystem;
		$this->_assetRepository = $assetRepository;
		$this->_assetPublisher = $assetPublisher;
		$this->directory = $directory;
		$this->fileExtensions = $fileExtensions;
	}

	public function resolveAssetStream($identifier)
	{
		return new \Customweb_Core_Stream_Input_File($this->getFilePath($this->getFile($identifier)));
	}

	public function resolveAssetUrl($identifier)
	{
		return new \Customweb_Core_Url($this->getFile($identifier)->getUrl());
	}

	/**
	 * @param string $identifier
	 * @return \Magento\Framework\View\Asset\File
	 * @throws \Customweb_Asset_Exception_UnresolvableAssetException
	 */
	protected function getFile($identifier)
	{
		$file = $this->_assetRepository->createAsset('Customweb_RealexCw::' . $this->directory . '/' . $identifier);
		try {
			$this->_assetPublisher->publish($file);
		} catch (\Magento\Framework\Exception\FileSystemException $e) {
			throw new \Customweb_Asset_Exception_UnresolvableAssetException($identifier);
		} catch (\Magento\Framework\View\Asset\File\NotFoundException $e) {
			throw new \Customweb_Asset_Exception_UnresolvableAssetException($identifier);
		} catch (\Magento\Framework\View\Asset\ContentProcessorException $e) {
			throw new \Customweb_Asset_Exception_UnresolvableAssetException($identifier);
		}
		$staticViewDirectory = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::STATIC_VIEW);
		if (!$staticViewDirectory->isExist($file->getPath()) || !$this->isValidFileExtension($identifier)) {
			throw new \Customweb_Asset_Exception_UnresolvableAssetException($identifier);
		}
		return $file;
	}

	/**
	 * @param \Magento\Framework\View\Asset\File $file
	 * @return string
	 */
	protected function getFilePath(\Magento\Framework\View\Asset\File $file)
	{
		return $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::STATIC_VIEW)->getAbsolutePath($file->getPath());
	}

	/**
	 * @param string $filePath
	 * @return boolean
	 */
	protected function isValidFileExtension($filePath)
	{
		$fileExtension = pathinfo($filePath, PATHINFO_EXTENSION);
		if ($this->fileExtensions == null || in_array($fileExtension, $this->fileExtensions)) {
			return true;
		} else {
			return false;
		}
	}
}