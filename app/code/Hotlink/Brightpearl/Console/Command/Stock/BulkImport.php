<?php
namespace Hotlink\Brightpearl\Console\Command\Stock;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BulkImport extends \Hotlink\Framework\Console\Command\AbstractInteractionCommand
{

    const COMMAND_NAME = 'brightpearl:stock:bulk-import';

    protected $interaction;

    public function __construct(
        \Magento\Framework\App\State $magentoAppState,
        \Magento\Framework\Event\ManagerInterface $eventManager,

        \Hotlink\Brightpearl\Model\Interaction\Stock\Bulk\Import $interaction,

        string $name = null
    )
    {
        $name = $name ?? self::COMMAND_NAME;
        $this->interaction = $interaction;
        parent::__construct( $name, $magentoAppState, $eventManager );
    }

    protected function configure()
    {
        $this->setDescription( 'Imports warehouse stock levels for all Magento sku' );

        $environment = $this->getEnvironment();

        # shared config...
        #skip_unmanaged
        #put_back_instock
        #set_qty_zero_when_missing

        $batch    = $environment->getParameter( 'batch' );
        $sleep    = $environment->getParameter( 'sleep' );
        $limit    = $environment->getParameter( 'query_limit' );
        #$timeout  = $environment->getParameter( 'api_timeout' );
        #$instock  = $environment->getParameter( 'put_instock' );
        #$outstock = $environment->getParameter( 'put_outofstock' );
        #$ttl      = $environment->getParameter( 'ttl' );

        $required = InputOption::VALUE_REQUIRED;

        $this->addOption( 'batch',    null, $required, $batch->getNote(), $batch->getValue() );
        $this->addOption( 'sleep',    null, $required, $sleep->getNote(), $sleep->getValue() );
        $this->addOption( 'limit',    null, $required, $limit->getNote(), $limit->getValue() );
        #$this->addOption( 'timeout',  null, $required, $timeout->getNote(),  $timeout->getValue()  );
        #$this->addOption( 'instock',  null, $required, $instock->getNote(),  $instock->getValue()  );
        #$this->addOption( 'outstock', null, $required, $outstock->getNote(), $outstock->getValue() );

        #$this->addOption( 'ttl',      null, $required, $ttl->getNote(),      $ttl->getValue()      );

        parent::configure();
    }

    protected function execute( InputInterface $input, OutputInterface $output )
    {
        $batch    = $input->getOption( 'batch' );
        $sleep    = $input->getOption( 'sleep' );
        $limit    = $input->getOption( 'limit' );
        #$timeout  = $input->getOption( 'timeout' );
        #$instock  = $input->getOption( 'instock' );
        #$outstock = $input->getOption( 'outstock' );
        #$ttl      = $input->getOption( 'ttl' );

        $environment = $this->getEnvironment();

        $environment->getParameter( 'batch' ) ->setValue( $batch );
        $environment->getParameter( 'sleep' ) ->setValue( $sleep );
        $environment->getParameter( 'query_limit' ) ->setValue( $limit );
        #$environment->getParameter( 'api_timeout' ) ->setValue( $timeout );
        #$environment->getParameter( 'put_instock' ) ->setValue( $instock );
        #$environment->getParameter( 'put_outofstock' ) ->setValue( $outstock );
        #$environment->getParameter( 'ttl' ) ->setValue( $ttl );

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
