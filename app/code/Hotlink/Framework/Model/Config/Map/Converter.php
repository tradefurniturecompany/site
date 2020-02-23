<?php
namespace Hotlink\Framework\Model\Config\Map;

class Converter implements \Magento\Framework\Config\ConverterInterface
{

    function convert( $source )
    {
        if ( $source === null )
            {
                return [];
            }
        return $this->_toArray( $source );
    }

    protected function _toArray( \DOMNode $document )
    {
        $result = [];
        if ( $document->hasChildNodes() )
            {
                $hotlink = $document->firstChild;
                foreach ( $hotlink->childNodes as $platform )
                    {
                        if ( 'platform' == $platform->localName )
                            {
                                $platformClass = $this->_classAttribute( $platform );
                                $interactions = [];
                                foreach ( $platform->childNodes as $interaction )
                                    {
                                        if ( 'interaction' == $interaction->localName )
                                            {
                                                $interactionClass = $this->_classAttribute( $interaction );
                                                $triggers = [];
                                                $monitors = [];
                                                $actions = [];
                                                $implementations = [];
                                                foreach ( $interaction->childNodes as $item )
                                                    {
                                                        switch ( $item->localName )
                                                            {
                                                                case 'implementation':
                                                                    $implementations[] = $this->_classAttribute( $item );
                                                                    break;
                                                                case 'trigger':
                                                                    $triggers[] = $this->_classAttribute( $item );
                                                                    break;
                                                                case 'monitor':
                                                                    $monitors[] = $this->_classAttribute( $item );
                                                                    break;
                                                                case 'action':
                                                                    $actions[] = $this->_classAttribute( $item );
                                                                    break;
                                                                default:
                                                                    break;
                                                            }
                                                    }
                                                $interactions[ $interactionClass ] = [ 'implementations' => $implementations,
                                                                                       'triggers'        => $triggers,
                                                                                       'monitors'        => $monitors,
                                                                                       'actions'         => $actions ];
                                            }
                                    }
                                $result[ $platformClass ] = $interactions;
                            }
                    }
            }
        return $result;
    }

    protected function _classAttribute( \DOMNode $node )
    {
        return $node->attributes->getNamedItem( 'class' )->value;
    }

}
