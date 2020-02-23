<?php
namespace Hotlink\Framework\Model\Interaction\Environment\Parameter;

class Boolean extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    protected $htmlBooleanHelper;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment\Parameter $parameterHelper,

        \Hotlink\Framework\Html\Form\Environment\Parameter\Boolean $htmlBooleanHelper
    )
    {
        parent::__construct( $exceptionHelper, $parameterHelper );
        $this->htmlBooleanHelper = $htmlBooleanHelper;
    }

    function getDefault()
    {
        return false;
    }

    function getName()
    {
        return 'Boolean';
    }

    function getKey()
    {
        return 'boolean';
    }

    function getFormHelper()
    {
        return $this->htmlBooleanHelper;
    }

}
