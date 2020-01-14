<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper;

class Customisation implements \Hotlink\Framework\Model\Report\IReport
{

    protected $_report;

    protected $reportHelper;
    protected $evaluate;
    protected $transform;
    protected $output;

    public function __construct(
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper\Customisation\Evaluate $evaluate,
        \Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper\Customisation\Transform $transform,
        \Hotlink\Brightpearl\Model\Interaction\Order\Export\Helper\Customisation\Output $output
    )
    {
        $this->reportHelper = $reportHelper;
        $this->evaluate = $evaluate;
        $this->transform = $transform;
        $this->output = $output;
    }

    public function apply( $configMap, $order, $data )
    {
        $report = $this->getReport();
        if ( count( $configMap ) )
            {
                $counter = 0;
                foreach ( $configMap as $definition )
                    {
                        $counter++;
                        $report->debug( "Applying customisation [$counter]" );

                        $expressionEvaluate  = trim( $definition[ 'evaluate' ] );
                        $expressionTransform = trim( $definition[ 'transform' ] );
                        $expressionOutput    = trim( $definition[ 'output' ] );
                        $onfailure =       $definition[ 'processing' ];

                        $value = null;
                        try
                            {
                                $report->indent();

                                $report->debug( "evaluate: $expressionEvaluate" );
                                $value = $this->getEvaluate()->apply( $expressionEvaluate, $order );
                                $report->debug( "evaluate: result = '$value'" );

                                if ( $expressionTransform )
                                    {
                                        $report->debug( "transform: '$value' with '$expressionTransform'" );
                                        $value = $this->getTransform()->apply( $expressionTransform, $value );
                                        $report->debug( "transform: result = '$value'" );
                                    }
                                else
                                    {
                                        $report->debug( "transform: skipping, not defined" );
                                    }

                                $report->debug( "output: '$value' to '$expressionOutput'" );
                                $target = $this->getOutput()->apply( $expressionOutput, $data );
                                $object = $target[ 'object' ];
                                $index  = $target[ 'index' ];
                                $object[ $index ] = $value;

                                $report->debug( "Applied customisation [$counter] : $expressionOutput = '$value'" );

                                $report->unindent();
                            }
                        catch ( \Exception $e )
                            {
                                $message = "Terminating customisation [$counter] : " . $e->getMessage();
                                switch ( $onfailure )
                                    {
                                        case 'skip':
                                            $report->indent()->debug( $message )->unindent();
                                            $report->unindent();
                                            continue;
                                            break;
                                        case 'warn':
                                            $report->indent()->warn( $message )->unindent();
                                            $report->unindent();
                                            continue;
                                            break;
                                        case 'stop':
                                            $report->indent()->error( $message )->unindent();
                                            $report->unindent();
                                            throw new \Exception( 'Failed to apply customisations', 2171828 );
                                            break 2;
                                    }
                            }
                    }
            }
        else
            {
                $report->debug( 'skipping : No customisation defined' );
            }
    }

    public function report()
    {
        return $this->reportHelper;
    }

    public function getEvaluate()
    {
        return $this->evaluate;
    }

    public function getTransform()
    {
        return $this->transform;
    }

    public function getOutput()
    {
        return $this->output;
    }

    //
    //  IReport
    //
    public function setReport( \Hotlink\Framework\Model\Report $report = null )
    {
        $this->_report = $report;
        return $this;
    }

    public function getReport( $safe = true )
    {
        if ( !$this->_report && $safe )
            {
                $this->setReport( $this->report()->create( $this ) );
            }
        return $this->_report;
    }

    public function getReportSection()
    {
        return 'customisation';
    }

}
