<?php
namespace Hotlink\Brightpearl\Controller\Webhook;

abstract class WebhookAbstract extends \Magento\Framework\App\Action\Action
{

    protected $eventManager;
    protected $storeManager;
    protected $configWebhook;
    protected $resultJsonFactory;

    abstract protected function _execute( $payload );

    function __construct(
        \Magento\Framework\App\Action\Context $context,

        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Brightpearl\Model\Config\Webhook $configWebhook
        )
    {
        $this->storeManager = $storeManager;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->configWebhook = $configWebhook;
        parent::__construct( $context );
        $this->eventManager = $context->getEventManager();
    }

    function execute()
    {
        $result = $this->resultJsonFactory->create();
        $code = 200;
        $data = [];
        try
            {
                if ( $this->validkey() )
                    {
                        $payload = $this->getPayload();
                        if ( $this->validPayload( $payload ) )
                            {
                                $extra = $this->_execute( $payload );
                                $data = [ 'response' => 'OK' ];
                                if ( $extra )
                                    {
                                        $data[ 'extra'] = $extra;
                                    }
                            }
                        else
                            {
                                $data = [ 'error' => "Invalid Payload" ];
                                $code = \Magento\Framework\Webapi\Exception::HTTP_NOT_ACCEPTABLE;
                            }
                    }
                else
                    {
                        $data = [ 'error' => "Invalid Key" ];
                        $code = \Magento\Framework\Webapi\Exception::HTTP_METHOD_NOT_ALLOWED;
                    }
            }
        catch ( \Exception $e )
            {
                $data = [ 'error' =>  $e->getMessage() ];
                $code = \Magento\Framework\Webapi\Exception::HTTP_NOT_FOUND;
            }
        $result->setHttpResponseCode( $code );
        $result->setData( $data );
        return $result;
    }

    protected function getPayload()
    {
        $contents = file_get_contents( "php://input" );
        $json = json_decode( $contents, true );
        return $json;
    }

    protected function validKey()
    {
        $key = $this->configWebhook->getCallBackKey( $this->storeManager->getStore()->getId() );
        $k   = $this->getRequest()->getParam( 'k' );

        return ( $k == $key );
    }

    protected function validPayload( $payload )
    {
        return ( !is_null( $payload ) && isset( $payload[ 'fullEvent' ] ) );
    }

}
