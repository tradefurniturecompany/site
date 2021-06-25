<?php
namespace Hotlink\Brightpearl\Console\Command\Shipment;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SpecificImport extends \Hotlink\Framework\Console\Command\AbstractInteractionCommand
{

    const COMMAND_NAME = 'brightpearl:shipment:specific-import';

    protected $interaction;

    public function __construct(
        \Magento\Framework\App\State $magentoAppState,
        \Magento\Framework\Event\ManagerInterface $eventManager,

        \Hotlink\Brightpearl\Model\Interaction\Shipment\Specific\Import $interaction,

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

        $noteid = $environment->getParameter( 'goodsoutnote_id' );
        $notetype = $environment->getParameter( 'note_type' );
        $notify = $environment->getParameter( 'notify_customer' );

        $required = InputOption::VALUE_REQUIRED;

        
        $this->addArgument( 'noteid', $required, $noteid->getNote(), $noteid->getDefault() );
        $this->addOption( 'notetype', null, $required, $notetype->getNote(), $notetype->getDefault() );
        $this->addOption( 'notify', null, $required, $notify->getNote(), $notify->getDefault() );

        parent::configure();
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $noteid = $input->getArgument( 'noteid' );
        $notetype = $input->getOption( 'notetype' );
        $notify = $input->getOption( 'notify' );

        if ( is_null( $noteid ) )
            {
                throw new \Exception( 'noteid is a required argument' );
            }

        if ( is_null( $notetype ) )
            {
                throw new \Exception( 'notetype is a required argument' );
            }

        $environment = $this->getEnvironment();

        $environment->getParameter( 'goodsoutnote_id' ) ->setValue( $noteid );
        $environment->getParameter( 'note_type' ) ->setValue( $notetype );
        $environment->getParameter( 'notify_customer' ) ->setValue( $notify );

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
