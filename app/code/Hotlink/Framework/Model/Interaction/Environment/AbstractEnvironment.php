<?php
namespace Hotlink\Framework\Model\Interaction\Environment;

abstract class AbstractEnvironment extends \Hotlink\Framework\Model\AbstractModel implements \Hotlink\Framework\Html\IFormHelper, \Hotlink\Framework\Model\Report\IReport
{

    protected $_parameters = [];
    protected $_parametersInitialised = false;
    protected $_storeId = false;

    //
    //  Returns the associated interaction model name
    //  You may need to overload this if the environment and interaction reside within different modules
    //

    protected $interaction = false;
    protected $htmlFormEnvironmentHelper;
    protected $storeManager;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Hotlink\Framework\Helper\Html\Form\Environment $htmlFormEnvironmentHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        $storeId
    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );

        $this->interaction = $interaction;
        $this->htmlFormEnvironmentHelper = $htmlFormEnvironmentHelper;
        $this->storeManager = $storeManager;
        $this->_setStoreId( $storeId );
    }

    //
    //  Get implementation module
    //
    function getImplementationModel()
    {
        return $this->getConfig()->getImplementationModel( $this->getStoreId() );
    }

    protected function _setStoreId( $storeId )
    {
        if ( is_null( $storeId ) || $storeId === false || $storeId === '' )
            {
                $this->exception()->throwProcessing( 'invalid storeId in [class]', $this );
            }
        $this->_storeId = $storeId;
        return $this;
    }

    function getStoreId()
    {
        return $this->_storeId;
    }

    // function hasStoreId()
    // {
    //     return $this->_storeId !== FALSE;
    // }

    function isEnabled()
    {
        return $this->getConfig()->isEnabled( $this->getStoreId() );
    }

    function isTriggerEnabled( \Hotlink\Framework\Model\Trigger\AbstractTrigger $trigger )
    {
        return ( $trigger->getMagentoEvent()->getName() == 'hotlink_framework_trigger_admin_user_request' )
            ? true
            : $this->getConfig()->isTriggerEnabled( $trigger, $this->getStoreId() );
    }

    function getInteraction()
    {
        return $this->interaction;
    }

    //
    //  Overload to expose environment parameters
    //
    protected function _getParameterModels()
    {
        return array();
    }

    //
    //  IFormHelper
    //
    function getFormHelper()
    {
        return $this->htmlFormEnvironmentHelper;
    }

    function getConfig()
    {
        return $this->interaction->getConfig();
    }

    function getParameters()
    {
        if ( !$this->_parametersInitialised )
            {
                $models = $this->_getParameterModels();
                foreach ( $models as $model )
                    {
                        $parameter = $this->factory()->create( $model );
                        $key = $parameter->getKey();
                        if ( !isset( $this->_parameters[ $key ] ) )
                            {
                                $this->addParameter( $parameter );
                            }
                    }
                $this->_parametersInitialised = true;
            }
        return $this->_parameters;
    }

    function addParameter( \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter $parameter )
    {
        $key = $parameter->getKey();
        if ( isset( $this->_parameters[ $key ] ) )
            {
                $this->exception()->throwProcessing( 'Parameter ' . $key . ' already added in [class].', $this );
            }
        if ( $parameter->hasEnvironment() && ( $parameter->getEnvironment() !== $this ) )
            {
                $this->exception()->throwProcessing( 'Parameter ' . $key . ' cannot be added in [class], assigned to a different environment.', $this );
            }
        else
            {
                $parameter->setEnvironment( $this );
            }
        $this->_parameters[ $key ] = $parameter;
        return $this;
    }

    function setParameter( $key, \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter $parameter )
    {
        if ( $parameter->hasEnvironment() && ( $parameter()->getEnvironment() !== $this ) )
            {
                $this->exception()->throwProcessing( 'Parameter ' . $key . ' cannot be added in [class], as already assigned to a different environment.', $this );
            }
        else
            {
                $parameter->setEnvironment( $this );
            }
        $this->_parameters[ $key ] = $parameter;
        return $this;
    }

    function getParameter( $key )
    {
        $parameters = $this->getParameters();
        return ( $parameters && isset( $parameters[ $key ] ) ) ? $parameters[ $key ] : false;
    }

    function getParameterValue( $key )
    {
        $parameter = $this->getParameter( $key );
        return ( $parameter ) ? $parameter->getValue() : null;
    }

    function setParameterValue( $key, $value )
    {
        $parameter = $this->getParameter( $key );
        if ( $parameter )
            {
                return $parameter->setValue( $value );
            }
        return false;
    }

    function status()
    {
        $report = $this->getReport();
        $storeId = $this->getStoreId();
        $storeCode = $this->storeManager->getStore( $storeId )->getCode();
        $report->debug( "Environment parameters for store id " . $storeId . " ($storeCode)" );
        $parameters = $this->getParameters();
        $report->indent();
        if ( count( $parameters ) > 0 )
            {
                foreach ( $parameters as $parameter )
                    {
                        if ( $output = $parameter->asString() )
                            {
                                $report->debug( $output );
                            }
                    }
            }
        else
            {
                $report->debug( "none" );
            }
        $report->unindent();
    }

    //
    //  IReport
    //
    function getReportSection()
    {
        return 'environment';
    }

}
