<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Osc\Block;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Block\Block;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Mageplaza\Osc\Helper\Data as OscHelper;
use Mageplaza\Osc\Model\System\Config\Source\StaticBlockPosition;
use Zend_Serializer_Exception;

/**
 * Class StaticBlock
 * @package Mageplaza\Osc\Block
 */
class StaticBlock extends Template
{
    /**
     * @var OscHelper
     */
    protected $_oscHelper;

    /**
     * @type CheckoutSession
     */
    private $checkoutSession;

    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;

    /**
     * StaticBlock constructor.
     *
     * @param Context $context
     * @param OscHelper $oscHelper
     * @param CheckoutSession $checkoutSession
     * @param BlockRepositoryInterface $blockRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        OscHelper $oscHelper,
        CheckoutSession $checkoutSession,
        BlockRepositoryInterface $blockRepository,
        array $data = []
    ) {
        $this->_oscHelper = $oscHelper;
        $this->checkoutSession = $checkoutSession;
        $this->blockRepository = $blockRepository;

        parent::__construct($context, $data);
    }

    /**
     * @return array
     * @throws LocalizedException
     * @throws Zend_Serializer_Exception
     */
    public function getStaticBlock()
    {
        $result = [];

        $config = $this->_oscHelper->isEnableStaticBlock() ? $this->_oscHelper->getStaticBlockList() : [];
        foreach ($config as $key => $row) {
            /** @var Block $block */
            $block = $this->getLayout()->createBlock(Block::class)->setBlockId($row['block'])->toHtml();
            if (($row['position'] == StaticBlockPosition::SHOW_IN_SUCCESS_PAGE && $this->getNameInLayout() == 'osc.static-block.success')
                || ($row['position'] == StaticBlockPosition::SHOW_AT_TOP_CHECKOUT_PAGE && $this->getNameInLayout() == 'osc.static-block.top')
                || ($row['position'] == StaticBlockPosition::SHOW_AT_BOTTOM_CHECKOUT_PAGE && $this->getNameInLayout() == 'osc.static-block.bottom')) {
                $result[] = [
                    'content'   => $block,
                    'sortOrder' => $row['sort_order']
                ];
            }
        }

        usort($result, function ($a, $b) {
            return ($a['sortOrder'] <= $b['sortOrder']) ? -1 : 1;
        });

        return $result;
    }
}
