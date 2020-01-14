<?php
namespace Hotlink\Framework\Model\Interaction\Config;

class DefaultConfig extends \Hotlink\Framework\Model\Interaction\Config\AbstractConfig
{

    const CLASS_NAME = "\Hotlink\Framework\Model\Interaction\Config\DefaultConfig";

    //
    //  This is a mock class, automatically used when the developer does not explcitly define a config model for an interaction.
    //  Unlike all other config models, this class is instantiated using getModel rather than getSingleton to support
    //  multiple instances (one per interaction) each using a different config xml path.
    //

}
