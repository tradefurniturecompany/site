<?php
namespace Hotlink\Brightpearl\Block\Adminhtml\System\Config\Form\Element;

class StaticElement extends \Magento\Backend\Block\AbstractBlock implements \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{

    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $template = <<<EOF
<tr class="system-fieldset-sub-foot" id="row_%s_static">
    <td class="label">
        <span data-config-scope="%s">%s</span>
    </td>
    <td class="value">
        <span class="static">%s</span>
    </td>
</tr>
EOF;
        $value = $this->getValue($element)
            ? $this->getValue($element)
            : '- no value -';

        $html = sprintf($template,
                        $element->getHtmlId(),
                        $element->getScopeLabel(),
                        $element->getLabel(),
                        $value );

        return $html;
    }

    public function getValue(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        return $this->escapeHtml( $element->getValue() );
    }
}
