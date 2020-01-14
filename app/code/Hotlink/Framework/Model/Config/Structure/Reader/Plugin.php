<?php
namespace Hotlink\Framework\Model\Config\Structure\Reader;

class Plugin
{

    protected $template;

    public function __construct( \Hotlink\Framework\Model\Config\Template $template )
    {
        $this->template = $template;
    }

    public function afterRead( $subject, $result )
    {
        return $this->template->apply( $result );
    }

}
