<?php
namespace Hotlink\Framework\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AbstractInteractionCommand extends \Symfony\Component\Console\Command\Command
{

    const INPUT_OPT_MONOCHROME       = 'monochrome';
    const INPUT_OPT_MONOCHROME_SHORT = 'm';

    protected $eventManager;
    protected $magentoAppState;

    function __construct(
        string $name = null,
        \Magento\Framework\App\State $magentoAppState,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {
        $this->magentoAppState = $magentoAppState;
        $this->eventManager = $eventManager;
        parent::__construct( $name );
    }

    protected function configure()
    {
        $this->addOption
            ( self::INPUT_OPT_MONOCHROME,
              self::INPUT_OPT_MONOCHROME_SHORT,
              null,
              'Monochrome output only in shell (disable colour printing)'
            );
        return parent::configure();
    }

    protected function _execute( \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
                                 InputInterface $input,
                                 OutputInterface $output )
    {
        $this->magentoAppState->setAreaCode( \Magento\Framework\App\Area::AREA_ADMINHTML );
        $monochrome = $input->getOption( self::INPUT_OPT_MONOCHROME );
        $level = $this->getLevel( $output );

        $eventData = \Hotlink\Framework\Model\Trigger\Cli\Command::getEventConfiguration
                   (
                       $interaction,
                       ! $monochrome,
                       $level
                   );
        $this->eventManager->dispatch( \Hotlink\Framework\Model\Trigger\Cli\Command::EVENT, $eventData );
        return 0;
    }

    protected function getLevel( $output )
    {
        if ( $output->isDebug() )
            {
                return \Hotlink\Framework\Model\Report\Item::LEVEL_TRACE;
            }
        if ( $output->isVeryVerbose() )
            {
                return \Hotlink\Framework\Model\Report\Item::LEVEL_TRACE;
            }
        elseif ( $output->isVerbose() )
            {
                return \Hotlink\Framework\Model\Report\Item::LEVEL_DEBUG;
            }
        return \Hotlink\Framework\Model\Report\Item::LEVEL_INFO;
    }

}
