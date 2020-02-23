<?php
namespace Hotlink\Framework\Block\Adminhtml\Report\Data;

class Item extends \Magento\Framework\View\Element\Template
{

    protected static $levelLookup = array(
        \Hotlink\Framework\Model\Report\Item::LEVEL_FATAL  =>  'FTL',
        \Hotlink\Framework\Model\Report\Item::LEVEL_ERROR  =>  'ERR',
        \Hotlink\Framework\Model\Report\Item::LEVEL_WARN   =>  'WRN',
        \Hotlink\Framework\Model\Report\Item::LEVEL_INFO   =>  'INF',
        \Hotlink\Framework\Model\Report\Item::LEVEL_DEBUG  =>  'DBG',
        \Hotlink\Framework\Model\Report\Item::LEVEL_TRACE  =>  'TRC'
        );

    protected $_template = 'Hotlink_Framework::report/interaction/report/item.phtml';
    protected $_initialTimestamp = null;

    protected function _beforeToHtml()
    {
        if ( is_null( $this->_initialTimestamp ) )
            {
                if ( $item = $this->getItem() )
                    {
                        $this->_initialTimestamp = $item->getTimestamp();
                    }
            }
    }

    protected function _afterToHtml($html)
    {
        $this->setPrevious( $this->getItem() );
        return parent::_afterToHtml( $html );
    }

    function getItemDuration()
    {
        $duration = 0;
        if ( $item = $this->getItem() )
            {
                if (  $previous = $this->getPrevious() )
                    {
                        $duration = round( $item->getTimestamp() - $previous->getTimestamp(), 4 );
                    }
            }
        return $duration;
    }

    function getElapsedTime()
    {
        $elapsed = 0;
        if ( !is_null( $this->_initialTimestamp ) )
            {
                if ( $item = $this->getItem() )
                    {
                        $elapsed = round( $item->getTimestamp() - $this->_initialTimestamp, 4 );
                    }
            }
        return $elapsed;
    }

    function getDataHtml( \Hotlink\Framework\Model\Report\Data $data )
    {
        if ( $renderer = $data->getRenderer() )
            {
                $block = false;
                try
                    {
                        $block = $this->_layout->getBlockSingleton( $renderer );
                    }
                catch ( \Exception $e )
                    {
                    }
                if ( $block )
                    {
                        $block->setReportData( $data );
                        return $block->toHtml();
                    }
            }
        return false;
    }

}
