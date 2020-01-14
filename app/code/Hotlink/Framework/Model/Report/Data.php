<?php
namespace Hotlink\Framework\Model\Report;

class Data
{
    protected $_id;
    protected $_value;
    protected $_renderer;

    public function init( $id, $value, $renderer )
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

    public function getId()
    {
        return $this->_id;
    }

    public function getValue()
    {
        return $this->_value;
    }

    public function getRenderer()
    {
        return $this->_renderer;
    }

    public function setRenderer( $value )
    {
        $this->_renderer = $value;
        return $this;
    }
}
