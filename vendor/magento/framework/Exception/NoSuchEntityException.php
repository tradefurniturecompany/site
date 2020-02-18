<?php
/**
 * No such entity service exception
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Framework\Exception;

use Magento\Framework\Phrase;

/**
 * @api
 * @since 100.0.2
 */
class NoSuchEntityException extends LocalizedException
{
    /**
     * @deprecated
     */
    const MESSAGE_SINGLE_FIELD = 'No such entity with %fieldName = %fieldValue';

    /**
     * @deprecated
     */
    const MESSAGE_DOUBLE_FIELDS = 'No such entity with %fieldName = %fieldValue, %field2Name = %field2Value';

    /**
     * @param \Magento\Framework\Phrase $phrase
     * @param \Exception $cause
     * @param int $code
     */
    public function __construct(Phrase $phrase = null, \Exception $cause = null, $code = 0)
    {
        if ($phrase === null) {
            $phrase = new Phrase('No such entity.');
        }
        parent::__construct($phrase, $cause, $code);
    }

    /**
     * Helper function for creating an exception when a single field is responsible for finding an entity.
     *
     * @param string $fieldName
     * @param string|int $fieldValue
     * @return \Magento\Framework\Exception\NoSuchEntityException
     */
    public static function singleField($fieldName, $fieldValue)
    {
        $r = new self(
            new Phrase(
                'No such entity with %fieldName = %fieldValue',
                [
                    'fieldName' => $fieldName,
                    'fieldValue' => $fieldValue
                ]
            )
        );
		/**
		 * 2020-02-19 Dmitry Fedyuk https://www.upwork.com/fl/mage2pro
		 * 1) «No such entity with customerId = ...»: https://github.com/tradefurniturecompany/site/issues/17
		 * 2) My initial attempt was to lod the exception here: `df_log_e($r, __CLASS__);`.
		 * But it is wrong because some exceptions are not errors and handled by the core, e.g.:
		 * @see \Magento\Checkout\Model\Session::loadCustomerQuote():
		 *	try {
		 *		$customerQuote = $this->quoteRepository->getForCustomer($this->_customerSession->getCustomerId());
		 *	}
		 * 	catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
		 *		$customerQuote = $this->quoteFactory->create();
		 *	}
		 */
        return $r;
    }

    /**
     * Helper function for creating an exception when two fields are responsible for finding an entity.
     *
     * @param string $fieldName
     * @param string|int $fieldValue
     * @param string $secondFieldName
     * @param string|int $secondFieldValue
     * @return \Magento\Framework\Exception\NoSuchEntityException
     */
    public static function doubleField($fieldName, $fieldValue, $secondFieldName, $secondFieldValue)
    {
        return new self(
            new Phrase(
                'No such entity with %fieldName = %fieldValue, %field2Name = %field2Value',
                [
                    'fieldName' => $fieldName,
                    'fieldValue' => $fieldValue,
                    'field2Name' => $secondFieldName,
                    'field2Value' => $secondFieldValue,
                ]
            )
        );
    }
}
