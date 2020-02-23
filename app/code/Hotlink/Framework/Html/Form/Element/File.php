<?php
namespace Hotlink\Framework\Html\Form\Element;

class File extends \Magento\Framework\Data\Form\Element\File
{

    function getElementHtml()
    {
        $html = $this->getBeforeHtml();
        $html .= '<input type="hidden" name="MAX_FILE_SIZE" value="' . $this->convertBytes( $this->getDataMaxSize() ) . '" />';
        $html .= '<input type="file" id="' . $this->getHtmlId() . '" name="'. $this->getName() .'" />';
        $html .= $this->getAfterHtml();
        return $html;
    }

    function getPostMaxSize()
    {
        return ini_get('post_max_size');
    }

    function getUploadMaxSize()
    {
        return ini_get('upload_max_filesize');
    }

    function getDataMaxSize()
    {
        return min( $this->getPostMaxSize(), $this->getUploadMaxSize() );
    }

    function convertBytes( $value )
    {
        if ( is_numeric( $value ) )
            {
                return $value;
            }
        else
            {
                $value_length = strlen( $value );
                $qty = substr( $value, 0, $value_length - 1 );
                $unit = strtolower( substr( $value, $value_length - 1 ) );
                switch ( $unit )
                    {
                    case 'k':
                        $qty *= 1024;
                        break;
                    case 'm':
                        $qty *= 1048576;
                        break;
                    case 'g':
                        $qty *= 1073741824;
                        break;
                    }
                return $qty;
            }
    }

}