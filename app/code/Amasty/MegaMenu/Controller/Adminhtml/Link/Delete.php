<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Controller\Adminhtml\Link;

use Magento\Backend\App\Action;

class Delete extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_MegaMenu::menu_links';

    /**
     * @var \Amasty\MegaMenu\Model\Repository\LinkRepository
     */
    private $linkRepository;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    public function __construct(
        Action\Context $context,
        \Amasty\MegaMenu\Model\Repository\LinkRepository $linkRepository,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->linkRepository = $linkRepository;
        $this->logger = $logger;
    }

    public function execute()
    {
        $packId = (int)$this->getRequest()->getParam('id');
        if ($packId) {
            try {
                $this->linkRepository->deleteById($packId);
                $this->messageManager->addSuccessMessage(__('The link have been deleted.'));
                $this->_redirect('ammegamenu/*/');

                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Can\'t delete item right now. Please review the log and try again.')
                );
                $this->logger->critical($e);
                $this->_redirect('ammegamenu/*/edit', ['id' => $packId]);

                return;
            }
        }
        $this->_redirect('ammegamenu/*/');
    }
}
