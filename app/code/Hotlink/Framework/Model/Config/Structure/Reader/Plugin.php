<?php
namespace Hotlink\Framework\Model\Config\Structure\Reader;

class Plugin
{

    protected $template;
    protected $filter;

    public function __construct(
        \Hotlink\Framework\Model\Config\Structure\Initialise\Template $template,
        \Hotlink\Framework\Model\Config\Structure\Initialise\Filter $filter
    )
    {
        $this->template = $template;
        $this->filter = $filter;
    }

    public function afterRead( $subject, $result )
    {
        $result = $this->template->apply( $result );
        $result = $this->filter->apply( $result );
        return $result;
    }

}
