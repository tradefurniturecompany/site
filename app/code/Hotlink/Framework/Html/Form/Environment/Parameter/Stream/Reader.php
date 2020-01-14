<?php
namespace Hotlink\Framework\Html\Form\Environment\Parameter\Stream;

class Reader extends \Hotlink\Framework\Html\Form\Environment\Parameter\Stream\AbstractStream
{

    protected $factoryHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Fieldset $fieldsetHelper,
        \Hotlink\Framework\Helper\Html $htmlHelper,

        \Hotlink\Framework\Helper\Factory $factoryHelper
    )
    {
        parent::__construct( $exceptionHelper, $fieldsetHelper, $htmlHelper );
        $this->factoryHelper = $factoryHelper;
    }

    public function getHtmlKey()
    {
        return 'parameter_stream_reader';
    }

    protected function _addFields( $fieldset, \Hotlink\Framework\Model\Interaction\Environment\Parameter\Stream\Reader $parameter )
    {
        $this->_initObjectHeader( $parameter, $fieldset );
    }

    public function getObject( $form, $environment )
    {
        return $this->factoryHelper->create( $this->_getClass( $form ) );
    }
}