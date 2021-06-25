<?php
namespace Hotlink\Brightpearl\Model\Interaction\Webhooks\Set;

class Implementation extends \Hotlink\Framework\Model\Interaction\Implementation\AbstractImplementation
{
    protected $apiServiceIntegration;
    protected $helperData;
    protected $configWebhook;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Hotlink\Brightpearl\Helper\Api\Service\Integration $apiServiceIntegration,
        \Hotlink\Brightpearl\Helper\Data $helperData,
        \Hotlink\Brightpearl\Model\Config\Webhook $configWebhook
    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );
        $this->apiServiceIntegration = $apiServiceIntegration;
        $this->helperData = $helperData;
        $this->configWebhook = $configWebhook;
    }

    protected function _getName()
    {
        return 'Brightpearl webhooks registration';
    }

    function execute()
    {
        $report = $this->getReport();
        $env = $this->getEnvironment();

        // 1. get existing webhooks
        $report->info( 'Requesting existing webhooks' );
        $existingWebhooks = $report( $this->apiServiceIntegration, 'getWebhook', $env->getStoreId(), $env->getAccountCode() );

        $key = $this->configWebhook->getCallBackKey();
        $webhooksSpec = $this->getWebhooksSpec();
        if ( $existingWebhooks ) {
            // 2. delete already set webhooks (only the ones we're about to subscribe to and have been created with key)
            $report->info( 'Removing duplicate webhooks' );
            foreach( $existingWebhooks as $bpwebhook ) {

                foreach ($webhooksSpec  as $webhook ) {

                    list($resource,,,,,) = $webhook;

                    if ( $resource == $bpwebhook['subscribeTo'] ) {

                        $uri = \Zend_Uri::factory( $bpwebhook['uriTemplate'] );
                        $path = $uri->getPath();

                        if ( strpos($path, 'k/' . $key) !== false ) {
                            $report( $this->apiServiceIntegration, 'deleteWebhook',
                                     $env->getStoreId(),
                                     $env->getAccountCode(),
                                     $bpwebhook['id'] );
                        }

                        break;
                    }
                }
            }
        }

        // 3. register webhooks
        $report->info( 'Registering integration webhooks' );
        foreach( $webhooksSpec as $webhook ) {

            list($resource, $method, $callback, $bodyTemplate, $contentType, $idSetAccepted) = $webhook;

            $webhookId = $report( $this->apiServiceIntegration, 'createWebhook',
                                  $env->getStoreId(),
                                  $env->getAccountCode(),
                                  $resource,
                                  $method,
                                  $callback,
                                  $bodyTemplate,
                                  $contentType,
                                  $idSetAccepted );
            if ($webhookId) {
                $report->incSuccess()->info( 'webhook created [' . $webhookId . '] ');
            }
        }
    }

    protected function getWebhooksSpec()
    {
        $env = $this->getEnvironment();
        $key = $this->generateKey( $env->getStoreId() );
        $bodyTemplate = '{"accountCode": "${account-code}", "resourceType": "${resource-type}", "id": "${resource-id}", "lifecycleEvent": "${lifecycle-event}", "fullEvent": "${full-event}" }';

        $goodsOut = [ \Hotlink\Brightpearl\Model\Platform\Brightpearl\Events::GOODS_OUT_NOTE,
                      'POST',
                      $this->helperData->getBaseCallbackUrl( 'hotlink_brightpearl/webhook/goodsout', [ 'k' => $key ] ),
                      $bodyTemplate,
                      'application/json',
                      true ];

        $dropShip = [ \Hotlink\Brightpearl\Model\Platform\Brightpearl\Events::DROP_SHIP_NOTE,
                      'POST',
                      $this->helperData->getBaseCallbackUrl( 'hotlink_brightpearl/webhook/dropship', [ 'k' => $key ] ),
                      $bodyTemplate,
                      'application/json',
                      true ];

        $orderStatus = [ \Hotlink\Brightpearl\Model\Platform\Brightpearl\Events::ORDER_MODIFIED_ORDER_STATUS,
                         'POST',
                         $this->helperData->getBaseCallbackUrl( 'hotlink_brightpearl/webhook/orderstatus', [ 'k' => $key ] ),
                         $bodyTemplate,
                         'application/json',
                         true ];


        return [ $goodsOut, $dropShip, $orderStatus ];
    }

    protected function generateKey( $storeId )
    {
        $key = md5( uniqid( rand(), TRUE ) );
        $this->configWebhook->saveCallbackKey( $key, $storeId );

        return $key;
    }

}
