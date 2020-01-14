<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingTableRates
 */


namespace Amasty\ShippingTableRates\Block\Adminhtml\System\Config\Form;

use Amasty\Base\Observer\GenerateInformationTab;

/**
 * Module Information Container
 */
class Information extends \Magento\Config\Block\System\Config\Form\Fieldset
{
    /**
     * @var string
     */
    private $userGuideLink = 'https://amasty.com/docs/doku.php?'
    . 'id=magento_2%3Ashipping_table_rates&utm_source=extension&utm_medium=link&utm_campaign=str-m2-guide';

    /**
     * @var \Amasty\Base\Helper\Module
     */
    private $moduleHelper;

    const MODULE_CODE = 'Amasty_ShippingTableRates';

    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\View\Helper\Js $jsHelper,
        \Amasty\Base\Helper\Module $moduleHelper,
        array $data = []
    ) {
        $this->moduleHelper = $moduleHelper;
        parent::__construct($context, $authSession, $jsHelper, $data);
    }

    /**
     * @return string
     */
    public function getUserGuideLink()
    {
        return $this->userGuideLink;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    protected function _getHeaderHtml($element)
    {
        if ($element->getIsNested()) {
            $html = '<tr class="nested"><td colspan="4"><div class="' . $this->_getFrontendClass($element) . '">';
        } else {
            $html = '<div class="' . $this->_getFrontendClass($element) . '">';
        }

        $html .= '<div class="entry-edit-head admin__collapsible-block">' .
            '<span id="' .
            $element->getHtmlId() .
            '-link" class="entry-edit-head-link"></span>';

        $html .= $this->_getHeaderTitleHtml($element);

        $html .= '</div>';
        $html .= '<input id="' .
            $element->getHtmlId() .
            '-state" name="config_state[' .
            $element->getId() .
            ']" type="hidden" value="' .
            (int)$this->_isCollapseState(
                $element
            ) . '" />';
        $html .= '<fieldset class="' . $this->_getFieldsetCss() . '" id="' . $element->getHtmlId() . '">';
        $html .= '<legend>' . $element->getLegend() . '</legend>';

        $html .= $this->_getHeaderCommentHtml($element);

        // field label column
        $html .= '<table cellspacing="0" class="form-list"><colgroup class="label" /><colgroup class="value" />';
        if ($this->getRequest()->getParam('website') || $this->getRequest()->getParam('store')) {
            $html .= '<colgroup class="use-default" />';
        }
        $html .= '<colgroup class="scope-label" /><colgroup class="" /><tbody>';
        return $html;
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    protected function _getHeaderTitleHtml($element)
    {
        $moduleInfo = $this->moduleHelper->getModuleInfo(static::MODULE_CODE);
        $modelTitle = $moduleInfo['description'];
        $moduleVersion = $moduleInfo['version'];

        $isVersionLast = $this->isLastVersion($moduleVersion);
        $className = $isVersionLast ? 'last-version' : '';

        $html = '<a id="' .
            $element->getHtmlId() .
            '-head" href="#' .
            $element->getHtmlId() .
            '-link" onclick="Fieldset.toggleCollapse(\'' .
            $element->getHtmlId() .
            '\', \'' .
            $this->getUrl(
                '*/*/state'
            ) . '\'); return false;">' . $modelTitle . ' ';

        $html .= '<span class="amasty-info-block">';
        $html .= '<span class="module-version ' . $className . '" >' . $moduleVersion . '</span>';
        $html .= '</span>';
        $html .= ' ' . __('by');
        $html .= ' ' . $this->getLogoHtml();
        $html .= '</a>';

        $html .= '<div class="amasty-user-guide amasty-info-block"><span class="message success">'
            . __(
                'Confused with configuration?'
                . ' No worries, please consult the <a target="_blank" href="%1">user guide</a>'
                .' to properly configure the extension.',
                $this->getUserGuideLink()
            )
            . '</span></div><br/>';

        return $html;
    }

    /**
     * @param        $currentVer
     *
     * @return bool
     */
    private function isLastVersion($currentVer)
    {
        $result = true;
        $allExtensions = $this->moduleHelper->getAllExtensions();
        if ($allExtensions && isset($allExtensions[static::MODULE_CODE])) {
            $module = $allExtensions[static::MODULE_CODE];
            if ($module && is_array($module)) {
                $module = array_shift($module);
            }

            if (isset($module['version']) && $module['version'] > (string)$currentVer) {
                $result = false;
            }
        }

        return $result;
    }

    /**
     * @return string
     */
    private function getLogoHtml()
    {
        $src = $this->_assetRepo->getUrl("Amasty_Base::images/amasty_logo.svg");
        $href = 'https://amasty.com' . $this->getSeoparams() . 'amasty_logo_' . static::MODULE_CODE;
        $html = '<a target="_blank" href="' . $href . '"><img class="amasty-logo" src="' . $src . '"/></a>';
        $html = '<object>' . $html . '</object>';
        return $html;
    }

    /**
     * @return string
     */
    private function getSeoparams()
    {
        return GenerateInformationTab::SEO_PARAMS;
    }
}
