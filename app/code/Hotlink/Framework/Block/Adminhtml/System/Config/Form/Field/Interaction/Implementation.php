<?php
namespace Hotlink\Framework\Block\Adminhtml\System\Config\Form\Field\Interaction; // Renderer

class Implementation extends \Magento\Config\Block\System\Config\Form\Field
{

    protected $factory;
    protected static $_interactions;

    function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Hotlink\Framework\Helper\Factory $factory,
        array $data = []
    )
    {
        $this->factory = $factory;
        parent::__construct(
            $context,
            $data
        );
    }

    function getFactory()
    {
        return $this->factory;
    }

    public static function addInteraction($key, $interaction)
    {
        if (!isset(self::$_interactions[$key])) {
            self::$_interactions[$key] = $interaction;
        }
    }

    public static function getInteraction($key)
    {
        if (isset(self::$_interactions[$key])) {
            return self::$_interactions[$key];
        }
    }

    protected function _getElementHtml( \Magento\Framework\Data\Form\Element\AbstractElement $element )
    {
        $config = $element->getFieldConfig();
        if ( isset( $config[ 'source_interaction' ] ) && isset( $config[ 'source_model' ] ) )
            {
                if ( $interaction = $this->getFactory()->singleton( $config[ 'source_interaction' ] ) )
                    {
                        $source = $this->getFactory()->singleton( $config[ 'source_model' ] );
                        $source->setInteraction( $interaction );
                        $options = $source->toOptionArray();
                        $element->setValues( $options );
                    }
                else
                    {
                        $this->error( "No interaction found for key $key" );
                    }
            }
        else
            {
                $this->error( 'No xml keys "interaction_key" and/or "source_model" defined for element '.$element->getId() );
            }
        return parent::_getElementHtml( $element );
    }

    protected function error( $message )
    {
        throw new \Hotlink\Framework\Model\Exception\Configuration( $message );
    }

}
