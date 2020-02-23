<?php
namespace Hotlink\Framework\Model\Report;

class Data
{
    protected $_id;
    protected $_value;
    protected $_renderer;

    function init( $id, $value, $renderer )
    {
        $this->_id = $id;
        $this->_value = $value;
        if ( ! $renderer )
            {
                $renderer = ( $value instanceof \Hotlink\Framework\Model\Report\IReportData )
                    ? $value->getReportDataRenderer()
                    : '\Hotlink\Framework\Block\Adminhtml\Report\Item\Data\DefaultData';
            }
        $this->_renderer = $renderer;
        return $this;
    }

    function getId()
    {
        return $this->_id;
    }

    function getValue()
    {
        return $this->_value;
    }

    function getRenderer()
    {
        return $this->_renderer;
    }

    function setRenderer( $value )
    {
        $this->_renderer = $value;
        return $this;
    }
}
