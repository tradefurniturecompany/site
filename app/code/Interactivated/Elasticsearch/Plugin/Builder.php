<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Interactivated\Elasticsearch\Plugin;

use Magento\Quote\Api\ChangeQuoteControlInterface;
use Magento\Framework\Exception\StateException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;

class Builder
{
    public function afterInitQuery($subject, $request)
    {
        if(isset($request['body']) && isset($request['body']['size']) && $request['body']['size']===0) {
            //$this->layer
            $request['body']['size'] = 180;
        }
        return $request;
    }
}