<?php
namespace Hotlink\Framework\Model\Interaction\Environment\Parameter\Stream;

class Reader extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    protected $magentoModelReaderFactory;
    protected $parameterStreamReaderHelper;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment\Parameter $parameterHelper,

        \Hotlink\Framework\Model\Stream\Magento\Model\ReaderFactory $magentoModelReaderFactory,
        \Hotlink\Framework\Html\Form\Environment\Parameter\Stream\Reader $parameterStreamReaderHelper
    )
    {
        parent::__construct( $exceptionHelper, $parameterHelper );
        $this->magentoModelReaderFactory = $magentoModelReaderFactory;
        $this->parameterStreamReaderHelper = $parameterStreamReaderHelper;
    }

    function getName()
    {
        return 'Data Source';
    }

    function getKey()
    {
        return 'stream';
    }

    function getDefault()
    {
        return $this->magentoModelReaderFactory->create();
    }

    //
    //  IFormHelper
    //
    function getFormHelper()
    {
        return $this->parameterStreamReaderHelper;
    }

    function asString()
    {
        return false;
    }

}
