<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Block\Adminhtml\System\Config\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

class ColorTemplate extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var \Amasty\MegaMenu\Model\OptionSource\ColorTemplate
     */
    private $colorTemplateModel;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    private $jsonEncoder;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Amasty\MegaMenu\Model\OptionSource\ColorTemplate $colorTemplateModel,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->colorTemplateModel = $colorTemplateModel;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $html = $element->getElementHtml();
        $html .= '<script type="text/javascript">
            require(["jquery"], function ($) {
                $(document).ready(function () {
                    var input = $("#' . $element->getHtmlId() . '"),
                        AmColorTemplateConfig = '. $this->getJsonConfig() .';
                        
                    input.change(function() {
                        var value = $(this).val();
                        if (AmColorTemplateConfig[value]) {
                            $.each(AmColorTemplateConfig[value], function(key, $value) {
                                var input = $("#ammegamenu_color_" + key);
                                input.val($value);
                                input.css({"backgroundColor" : $value});
                            });
                        }
                    });
                });
            });
            </script>';

        return $html;
    }

    private function getJsonConfig()
    {
        $result = [];
        foreach ($this->colorTemplateModel->getData() as $key => $config) {
            unset($config['title']);
            $result[$key] = $config;
        }

        return $this->jsonEncoder->encode($result);
    }
}
