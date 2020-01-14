<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Realtime\Import\Environment\YesNo;

abstract class AbstractYesNo extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    protected $configConfigSourceYesno;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment\Parameter $parameterHelper,

        \Magento\Config\Model\Config\Source\Yesno $configConfigSourceYesno
    )
    {
        parent::__construct( $exceptionHelper, $parameterHelper );
        $this->configConfigSourceYesno = $configConfigSourceYesno;
    }

    public function getOptions()
    {
        return $this->configConfigSourceYesno->toArray();
    }

    public function toOptionArray()
    {
        return $this->configConfigSourceYesno->toOptionArray();
    }

}
