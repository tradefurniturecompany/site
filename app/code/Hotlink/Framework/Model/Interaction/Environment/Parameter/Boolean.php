<?php
namespace Hotlink\Framework\Model\Interaction\Environment\Parameter;

class Boolean extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    protected $htmlBooleanHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment\Parameter $parameterHelper,

        \Hotlink\Framework\Html\Form\Environment\Parameter\Boolean $htmlBooleanHelper
    )
    {
        parent::__construct( $exceptionHelper, $parameterHelper );
        $this->htmlBooleanHelper = $htmlBooleanHelper;
    }

    public function getDefault()
    {
        return false;
    }

    public function getName()
    {
        return 'Boolean';
    }

    public function getKey()
    {
        return 'boolean';
    }

    public function getFormHelper()
    {
        return $this->htmlBooleanHelper;
    }

}
