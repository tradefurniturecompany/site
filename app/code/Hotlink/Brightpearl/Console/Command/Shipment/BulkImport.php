<?php
namespace Hotlink\Brightpearl\Console\Command\Shipment;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BulkImport extends \Hotlink\Framework\Console\Command\AbstractInteractionCommand
{

    const COMMAND_NAME = 'brightpearl:shipment:bulk-import';

    protected $interaction;

    function __construct(
        \Magento\Framework\App\State $magentoAppState,
        \Magento\Framework\Event\ManagerInterface $eventManager,

        \Hotlink\Brightpearl\Model\Interaction\Shipment\Bulk\Import $interaction,

        string $name = null
    )
    {
        $name = $name ?? self::COMMAND_NAME;
        $this->interaction = $interaction;
        parent::__construct( $name, $magentoAppState, $eventManager );
    }

    protected function configure()
    {
        $this->setDescription( 'Imports outstanding shipments from Brightpearl into Magento' );

        $environment = $this->getEnvironment();

        $batch = $environment->getParameter( 'batch' );
        $lookbehind = $environment->getParameter( 'lookbehind' );
        $sleep = $environment->getParameter( 'sleep' );

        $required = InputOption::VALUE_REQUIRED;

        $this->addOption( 'batch', null, $required, $batch->getNote(), $batch->getValue() );
        $this->addOption( 'lookbehind', null, $required, $lookbehind->getNote(), $lookbehind->getValue() );
        $this->addOption( 'sleep', null, $required, $sleep->getNote(), $sleep->getValue() );

        parent::configure();
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $batch = $input->getOption( 'batch' );
        $lookbehind = $input->getOption( 'lookbehind' );
        $sleep = $input->getOption( 'sleep' );

        $environment = $this->getEnvironment();

        $environment->getParameter( 'batch' ) ->setValue( $batch );
        $environment->getParameter( 'lookbehind' ) ->setValue( $lookbehind );
        $environment->getParameter( 'sleep' ) ->setValue( $sleep );

        return $this->_execute( $this->interaction, $input, $output );
    }

    protected function getEnvironment()
    {
        $environment = $this->interaction->getEnvironment( 0 );
        if ( ! $environment )
            {
                $environment = $this->interaction->createEnvironment( 0 );
            }
        return $environment;
    }

}
