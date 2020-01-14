<?php
namespace Hotlink\Framework\Model\Stream\Http\Html\Writer;

class Renderer extends \Magento\Framework\HTTP\PhpEnvironment\Response
{

    public function emit( $something )
    {
        $this->contentSent = false;
        $this->setContent( $something );
        $this->sendContent();
    }

}
