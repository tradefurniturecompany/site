<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_CommonRules
 */


namespace Amasty\CommonRules\Block\Adminhtml\Rule\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

abstract class AbstractTab extends Generic implements TabInterface
{
    /**
     * @var \Amasty\CommonRules\Model\OptionProvider\Pool
     */
    protected $poolOptionProvider;

    /**
     * @var string
     */
    protected $registryKey = '';

    /**
     * AbstractTab constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Amasty\CommonRules\Model\OptionProvider\Pool $poolOptionProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Amasty\CommonRules\Model\OptionProvider\Pool $poolOptionProvider,
        array $data = []
    ) {
        $this->poolOptionProvider = $poolOptionProvider;
        parent::__construct($context, $registry, $formFactory, $data);
    }


    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return $this->getLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return $this->getLabel();
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @return string
     */
    public function getRegistryKey()
    {
        return $this->registryKey;
    }

    /**
     * @param string $registryKey
     * @return $this
     */
    public function setRegistryKey($registryKey)
    {
        $this->registryKey = $registryKey;

        return $this;
    }

    /**
     * @return mixed
     */
    protected function getModel()
    {
        return $this->_coreRegistry->registry($this->getRegistryKey());
    }

    /**
     * @return \Magento\Framework\Phrase|string Tab label and title
     */
    protected abstract function getLabel();

    /**
     * Doing for possibility extend and additional new fields in tab form
     * @param \Magento\Rule\Model\AbstractModel $model
     * @return \Magento\Framework\Data\Form $form
     */
    abstract protected function formInit($model);
}
