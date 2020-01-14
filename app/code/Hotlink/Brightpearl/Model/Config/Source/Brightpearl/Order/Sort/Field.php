<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Sort;

class Field implements \Magento\Framework\Option\ArrayInterface
{

    const ORDER_ID                   = 'orderId';
    const ORDER_TYPE_ID              = 'orderTypeId';
    const ORDER_TYPE_NAMES           = 'orderTypeNames';
    const CONTACT_ID                 = 'contactId';
    const ORDER_STATUS_ID            = 'orderStatusId';
    const ORDER_STATUS_NAMES         = 'orderStatusNames';
    const ORDER_STOCK_STATUS_ID      = 'orderStockStatusId';
    const ORDER_STOCK_STATUS_NAMES   = 'orderStockStatusNames';
    const CREATED_ON                 = 'createdOn';
    const CREATED_BY_ID              = 'createdById';
    const CUSTOMER_REF               = 'customerRef';
    const ORDER_PAYMENT_STATUS_ID    = 'orderPaymentStatusId';
    const ORDER_PAYMENT_STATUS_NAMES = 'orderPaymentStatusNames';
    const UPDATED_ON                 = 'updatedOn';
    const PARENT_ORDER_ID            = 'parentOrderId';
    const PLACED_ON                  = 'placedOn';
    const DELIVERY_DATE              = 'deliveryDate';
    const SHIPPING_METHOD_ID         = 'shippingMethodId';
    const STAFF_OWNER_CONTACT_ID     = 'staffOwnerContactId';
    const PROJECT_ID                 = 'projectId';
    const DEPARTMENT_ID              = 'departmentId';
    const LEAD_SOURCE_ID             = 'leadSourceId';
    const EXTERNAL_REF               = 'externalRef';
    const INSTALLED_INTEGRATION_INSTANCE_ID = 'installedIntegrationInstanceId';


    public function toOptionArray()
    {
        return [
            [ 'value' => self::ORDER_ID,                          'label' => 'Order Id' ],
            [ 'value' => self::ORDER_TYPE_ID,                     'label' => 'Order Type Id' ],
            [ 'value' => self::ORDER_TYPE_NAMES,                  'label' => 'Order Type Names' ],
            [ 'value' => self::CONTACT_ID,                        'label' => 'Contact Id' ],
            [ 'value' => self::ORDER_STATUS_ID,                   'label' => 'Order Status Id' ],
            [ 'value' => self::ORDER_STATUS_NAMES,                'label' => 'Order Status Names' ],
            [ 'value' => self::ORDER_STOCK_STATUS_ID,             'label' => 'Order Stock Status Id' ],
            [ 'value' => self::ORDER_STOCK_STATUS_NAMES,          'label' => 'Order Stock Status Names' ],
            [ 'value' => self::CREATED_ON,                        'label' => 'Created On' ],
            [ 'value' => self::CREATED_BY_ID,                     'label' => 'Created By Id' ],
            [ 'value' => self::CUSTOMER_REF,                      'label' => 'Customer Ref' ],
            [ 'value' => self::ORDER_PAYMENT_STATUS_ID,           'label' => 'Order Payment Status Id' ],
            [ 'value' => self::ORDER_PAYMENT_STATUS_NAMES,        'label' => 'Order Payment Status Names' ],
            [ 'value' => self::UPDATED_ON,                        'label' => 'Updated On' ],
            [ 'value' => self::PARENT_ORDER_ID,                   'label' => 'Parent Order Id' ],
            [ 'value' => self::PLACED_ON,                         'label' => 'Placed On' ],
            [ 'value' => self::DELIVERY_DATE,                     'label' => 'Delivery Date' ],
            [ 'value' => self::SHIPPING_METHOD_ID,                'label' => 'Shipping Method Id' ],
            [ 'value' => self::STAFF_OWNER_CONTACT_ID,            'label' => 'Staff Owner Contact Id' ],
            [ 'value' => self::PROJECT_ID,                        'label' => 'Project Id' ],
            [ 'value' => self::DEPARTMENT_ID,                     'label' => 'Department Id' ],
            [ 'value' => self::LEAD_SOURCE_ID,                    'label' => 'Lead Source Id' ],
            [ 'value' => self::EXTERNAL_REF,                      'label' => 'External Ref' ],
            [ 'value' => self::INSTALLED_INTEGRATION_INSTANCE_ID, 'label' => 'Installed Integration Instance Id' ] ];
    }

    public function toArray()
    {
        return [
            self::ORDER_ID                   => 'Order Id',
            self::ORDER_TYPE_ID              => 'Order Type Id',
            self::ORDER_TYPE_NAMES           => 'Order Type Names',
            self::CONTACT_ID                 => 'Contact Id',
            self::ORDER_STATUS_ID            => 'Order Status Id',
            self::ORDER_STATUS_NAMES         => 'Order Status Names',
            self::ORDER_STOCK_STATUS_ID      => 'Order Stock Status Id',
            self::ORDER_STOCK_STATUS_NAMES   => 'Order Stock Status Names',
            self::CREATED_ON                 => 'Created On',
            self::CREATED_BY_ID              => 'Created By Id',
            self::CUSTOMER_REF               => 'Customer Ref',
            self::ORDER_PAYMENT_STATUS_ID    => 'Order Payment Status Id',
            self::ORDER_PAYMENT_STATUS_NAMES => 'Order Payment Status Names',
            self::UPDATED_ON                 => 'Updated On',
            self::PARENT_ORDER_ID            => 'Parent Order Id',
            self::PLACED_ON                  => 'Placed_On',
            self::DELIVERY_DATE              => 'Delivery Date',
            self::SHIPPING_METHOD_ID         => 'Shipping Method Id',
            self::STAFF_OWNER_CONTACT_ID     => 'Staff Owner Contact Id',
            self::PROJECT_ID                 => 'Project Id',
            self::DEPARTMENT_ID              => 'Department Id',
            self::LEAD_SOURCE_ID             => 'Lead Source Id',
            self::EXTERNAL_REF               => 'External Ref',
            self::INSTALLED_INTEGRATION_INSTANCE_ID => 'Installed Integration Instance Id' ];
    }

}
