<?php
namespace Hotlink\Framework\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogClean extends \Hotlink\Framework\Console\Command\AbstractInteractionCommand
{

    const COMMAND_NAME = 'hotlink:log:clean';

    protected $interaction;

    function __construct(
        \Magento\Framework\App\State $magentoAppState,
        \Magento\Framework\Event\ManagerInterface $eventManager,

        \Hotlink\Framework\Model\Interaction\Log\Cleaning $interaction,

        string $name = null
    )
    {
        $name = $name ?? self::COMMAND_NAME;
        $this->interaction = $interaction;
        parent::__construct( $name, $magentoAppState, $eventManager );
    }

    protected function configure()
    {
        $this->setDescription( 'Deletes log records and corresponding log files' );

        $environment = $this->getEnvironment();

        $count = $environment->getParameter( 'count' );
        $sleep = $environment->getParameter( 'sleep' );

        $required = InputOption::VALUE_REQUIRED;
        
        $this->addOption( 'count', null, $required, $count->getNote(), $count->getDefault() );
        $this->addOption( 'sleep', null, $required, $sleep->getNote(), $sleep->getDefault() );

        parent::configure();
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $count = $input->getOption( 'count' );
        $sleep = $input->getOption( 'sleep' );

        $environment = $this->getEnvironment();

        $environment->getParameter( 'count' ) ->setValue( $count );
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
