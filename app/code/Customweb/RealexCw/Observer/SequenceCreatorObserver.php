<?php
/**
  * You are allowed to use this API in your web application.
 *
 * Copyright (C) 2018 by customweb GmbH
 *
 * This program is licenced under the customweb software licence. With the
 * purchase or the installation of the software in your application you
 * accept the licence agreement. The allowed usage is outlined in the
 * customweb software licence which can be found under
 * http://www.sellxed.com/en/software-license-agreement
 *
 * Any modification or distribution is strictly forbidden. The license
 * grants you the installation in one application. For multiuse you will need
 * to purchase further licences at http://www.sellxed.com/shop.
 *
 * See the customweb software licence agreement for more details.
 *
 *
 * @category	Customweb
 * @package		Customweb_RealexCw
 *
 */

namespace Customweb\RealexCw\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\SalesSequence\Model\Builder;
use Magento\SalesSequence\Model\Config;
use Magento\Framework\Event\ObserverInterface;

class SequenceCreatorObserver implements ObserverInterface
{
    /**
     * @var Builder
     */
    private $sequenceBuilder;

    /**
     * @var Config
     */
    private $sequenceConfig;

    /**
     * Initialization
     *
     * @param Builder $sequenceBuilder
     * @param Config $sequenceConfig
     */
    public function __construct(
        Builder $sequenceBuilder,
        Config $sequenceConfig
    ) {
        $this->sequenceBuilder = $sequenceBuilder;
        $this->sequenceConfig = $sequenceConfig;
    }

    /**
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $storeId = $observer->getData('store')->getId();
        $this->sequenceBuilder->setPrefix($this->sequenceConfig->get('prefix'))
            ->setSuffix($this->sequenceConfig->get('suffix'))
	        ->setStartValue($this->sequenceConfig->get('startValue'))
	        ->setStoreId($storeId)
	        ->setStep($this->sequenceConfig->get('step'))
	        ->setWarningValue($this->sequenceConfig->get('warningValue'))
	        ->setMaxValue($this->sequenceConfig->get('maxValue'))
	        ->setEntityType('rexcw_trx')
	        ->create();
        return $this;
    }
}
