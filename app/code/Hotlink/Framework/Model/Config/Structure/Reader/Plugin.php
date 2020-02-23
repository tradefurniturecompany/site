<?php
namespace Hotlink\Framework\Model\Config\Structure\Reader;

class Plugin
{

    protected $template;

    function __construct( \Hotlink\Framework\Model\Config\Template $template )
    {
        $this->template = $template;
    }

    function afterRead( $subject, $result )
    {
        return $this->template->apply( $result );
    }

}
