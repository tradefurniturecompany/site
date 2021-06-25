<?php
namespace Hotlink\Framework\Model\Api\Message;

abstract class AbstractMessage extends \Magento\Framework\DataObject
{

    protected $_transaction = false;

    /**
     * @var \Hotlink\Framework\Model\Api\TransactionFactory
     */
    protected $interactionApiTransactionFactory;

    public function __construct(
        \Hotlink\Framework\Model\Api\TransactionFactory $interactionApiTransactionFactory,
        array $data = []
    ) {
        $this->interactionApiTransactionFactory = $interactionApiTransactionFactory;
        parent::__construct(
            $data
        );
    }


    public function setTransaction( \Hotlink\Framework\Model\Api\Transaction $transaction )
    {
        $this->_transaction = $transaction;
        return $this;
    }

    public function getTransaction()
    {
        if ( !$this->_transaction )
            {
                $transaction = $this->interactionApiTransactionFactory->create();
                $this->setTransaction( $transaction );
            }
        return $this->_transaction;
    }

}