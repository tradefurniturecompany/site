<?php
namespace Hotlink\Framework\Model\Trigger\Admin\User;

class Request extends \Hotlink\Framework\Model\Trigger\AbstractTrigger implements \Magento\Framework\Event\ObserverInterface
{

    const FORM_NAME = 'interactions';

    protected $htmlHelper;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Hotlink\Framework\Model\UserFactory $userFactory,

        \Hotlink\Framework\Helper\Html $htmlHelper
    )
    {
        parent::__construct(
            $exceptionHelper,
            $reflectionHelper,
            $reportHelper,
            $factoryHelper,
            $storeManager,
            $configMap,
            $userFactory
        );
        $this->htmlHelper = $htmlHelper;
    }

    /*
      Unlike most triggers, this one overloads _execute to identify interactions from html, and changes the source whilst
      iterating over interactions.
    */
    function getMagentoEvents()
    {
        return [ 'Initiated manually via admin' => 'hotlink_framework_trigger_admin_user_request' ];
    }

    function getContexts()
    {
        return [ 'hotlink_framework_trigger_admin_user_request' => 'Initiated manually via admin' ];
    }

    protected function _getName()
    {
        return 'Admin user request';
    }

    protected function _execute()
    {
        $request = $this->getMagentoEvent()->getRequest();
        $interactions = $request->getParam( self::FORM_NAME );
        if ( is_null( $interactions ) )
            {
                $interactions = [];
            }
        elseif ( ! is_array( $interactions ) )
            {
                $interactions = [ $interactions ];
            }

        foreach ( $interactions as $helperHtml => $data )
            {
                $helper = $this->htmlHelper->decode( $helperHtml );
                $interaction = $this->factory()->get( $helper )->getObject( $data, null );
                $interaction->setTrigger( $this );

                //
                //  Bind filters to streams (if any)
                //
                foreach ( $interaction->getEnvironments() as $environment )
                    {
                        $stream = $environment->getParameter( 'stream' );
                        if ( $stream )
                            {
                                $filter = $environment->getParameter( 'filter' );
                                if ( $filter )
                                    {
                                        $stream->getValue()->open( $filter->getValue() );
                                    }
                            }
                    }
                $report = $interaction->getReport();
                $report->addHtmlWriter();
                if ( !$interaction->canExecute( $this ) )
                    {
                        $htmlWriter = $report->getWriter( 'html' );
                        $directHtmlWriter = $htmlWriter->getDirectHtmlWriter();
                        if ( ! $directHtmlWriter->isOpen() )
                            {
                                $directHtmlWriter->open();
                            }
                        $directHtmlWriter->write( 'EXECUTION DENIED (probably by configuration)' );
                        $directHtmlWriter->close();
                    }
                $interaction->execute();
            }
    }

}