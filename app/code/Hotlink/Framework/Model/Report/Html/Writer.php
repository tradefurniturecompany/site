<?php
namespace Hotlink\Framework\Model\Report\Html;

class Writer extends \Hotlink\Framework\Model\Report\Writer\AbstractWriter
{

    protected $_writer = false;
    protected $_itemRenderer = false;
    protected $_itemRendererJs = false;
    protected $_sections = array();
    protected $_sendJs = true;
    protected $_onlyLevels = array();
    protected $_dataBlock = false;

    protected $htmlStreamWriter;
    protected $layout;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Hotlink\Framework\Model\Stream\Http\Html\Writer $htmlStreamWriter,
        \Magento\Framework\View\LayoutInterface $layout
    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );
        $this->htmlStreamWriter = $htmlStreamWriter;
        $this->layout = $layout;
    }

    function getCode()
    {
        return 'html';
    }

    function setDirectHtmlWriter( \Hotlink\Framework\Model\Stream\Http\Base $value )
    {
        $this->htmlStreamWriter = $value;
        return $this;
    }

    function getDirectHtmlWriter()
    {
        return $this->htmlStreamWriter;
    }

    protected function _open( \Hotlink\Framework\Model\Report $report, $sendHeader = true, $sendJs = true, $restrictLevels = array() )
    {
        parent::_open( $report );
        if ( ! $this->getDirectHtmlWriter()->isOpen() )
            {
                $this->getDirectHtmlWriter()->open( $sendHeader );
            }
        $this->_itemRenderer = $this->layout->getBlockSingleton( '\Hotlink\Framework\Block\Adminhtml\Report\Data\Item' );
        $this->_itemRenderer->setArea( 'adminhtml' );
        $this->_sendJs = $sendJs;
        if ($this->_sendJs) {
            $this->_itemRendererJs = $this->layout
                ->createBlock( '\Magento\Framework\View\Element\Template' )
                ->setTemplate( 'Hotlink_Framework::report/interaction/report/item/js.phtml' )
                ->setArea( 'adminhtml' );
            $this->_writeJsHeader();
        }

        if ( !empty( $restrictLevels ) )
            {
                $this->_onlyLevels = $restrictLevels;
            }
        return $this;
    }

    protected function _writeJsHeader()
    {
        if ( ! $this->getDirectHtmlWriter()->isOpen() )
            {
                return;
            }
        $header = $this->layout->createBlock( '\Magento\Framework\View\Element\Template' );
        $header->setTemplate( 'Hotlink_Framework::report/interaction/report/head.phtml' );
        $header->setArea( 'adminhtml' );
        $header->setAutoscrollChecked( true );
        $html = $header->toHtml();
        $this->getDirectHtmlWriter()->write( $html );
        return $this;
    }

    protected function _writeItemJs( $section = false, $finished = false )
    {
        if ( ! $this->getDirectHtmlWriter()->isOpen() )
            {
                return;
            }
        if ( $this->_itemRendererJs )
            {
                $this->_itemRendererJs->setSection( $section );
                $this->_itemRendererJs->setFinished( $finished );
                $html = $this->_itemRendererJs->toHtml();
                $this->getDirectHtmlWriter()->write( $html );
            }
    }

    protected function _write( \Hotlink\Framework\Model\Report\Item $item )
    {
        if ( ! $this->getDirectHtmlWriter()->isOpen() )
            {
                return;
            }
        if ( $item && $this->_isLevelAllowed( $item->getLevel() ) )
            {
                $this->_itemRenderer->setItem( $item );
                $html = $this->_itemRenderer->toHtml();
                $this->getDirectHtmlWriter()->write( $html );
                $section = $item->getSection();
                if ( $this->_sendJs && !in_array( $section, $this->_sections ) )
                    {
                        $this->_sections[] = $section;
                        $this->_writeItemJs( $section );
                    }
            }
        return $this;
    }

    protected function _close()
    {
        if ( ! $this->getDirectHtmlWriter()->isOpen() )
            {
                return;
            }
        $this->_writeItemJs( false, true );

        $this->getDirectHtmlWriter()->close();
        return $this;
    }

    protected function _isLevelAllowed( $level )
    {
        if ( empty( $this->_onlyLevels ) )
            {
                return true;
            }
        if ( in_array($level, $this->_onlyLevels ) )
            {
                return true;
            }
        elseif ( array_key_exists( $level, $this->_onlyLevels ) && !empty( $this->_onlyLevels[$level] ) )
            {
                return true;
            }
        return false;
    }

    protected function _getDataBlock()
    {
        if ( !$this->_dataBlock )
            {
                $this->_dataBlock = $this->layout->getBlockSingleton( '\Hotlink\Framework\Block\Adminhtml\Report\Item\Data' );
            }
        return $this->_dataBlock;
    }

}