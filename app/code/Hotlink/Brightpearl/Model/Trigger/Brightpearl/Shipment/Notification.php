<?php
namespace Hotlink\Brightpearl\Model\Trigger\Brightpearl\Shipment;

class Notification extends \Hotlink\Framework\Model\Trigger\AbstractTrigger
{

    const KEY_GOODS_OUT_NOTE = 'on_goods_out_note_received';
    const KEY_DROP_SHIP_NOTE = 'on_drop_ship_note_received';

    const LABEL_GOODS_OUT_NOTE = 'On Goods-Out Note notification (webhook)';
    const LABEL_DROP_SHIP_NOTE = 'On Drop-Ship Note notification (webhook)';

    const GOODS_OUT_NOTE_MODIFIED_SHIPPED = 'goods-out-note.modified.shipped';
    const DROP_SHIP_NOTE_MODIFIED_SHIPPED = 'drop-ship-note.modified.shipped';

    protected function _getName()
    {
        return 'Shipment notification';
    }

    function getMagentoEvents()
    {
        return [ 'On Goods-Out Note notification received' => 'hotlink_brightpearl_shipping_goods_out_callback_received',
                 'On Drop-Ship Note notification received' => 'hotlink_brightpearl_shipping_drop_ship_callback_received' ];
    }

    function getContexts()
    {
        return [ self::KEY_GOODS_OUT_NOTE => self::LABEL_GOODS_OUT_NOTE,
                 self::KEY_DROP_SHIP_NOTE => self::LABEL_DROP_SHIP_NOTE ];
    }

    function getContext()
    {
        $event = $this->getMagentoEvent();
        $note = $event->getNote();

        $context = null;
        switch ($event->getName()) {

        case 'hotlink_brightpearl_shipping_goods_out_callback_received':
            if ($note['fullEvent'] == self::GOODS_OUT_NOTE_MODIFIED_SHIPPED) {
                $context = self::KEY_GOODS_OUT_NOTE;
            }
            break;

        case 'hotlink_brightpearl_shipping_drop_ship_callback_received':
            if ($note['fullEvent'] == self::DROP_SHIP_NOTE_MODIFIED_SHIPPED) {
                $context = self::KEY_DROP_SHIP_NOTE;
            }
            break;
        }

        return $context;
    }

    protected function getNoteType($context)
    {
        $type = null;

        switch ($context) {

        case self::KEY_GOODS_OUT_NOTE:
            $type = \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Shipment\Type::GOODS_OUT;
            break;

        case self::KEY_DROP_SHIP_NOTE:
            $type = \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Shipment\Type::DROP_SHIP;
            break;
        }

        return $type;
    }

    protected function _execute()
    {
        if ($context = $this->getContext()) {

            $note = $this->getMagentoEvent()->getNote();
            $noteId = $note['id'];
            $noteType = $this->getNoteType($context);

            $storeId = $this->getStoreId();

            foreach ($this->getInteractions() as $interaction) {
                $interaction->setTrigger($this);

                if (!$interaction->hasEnvironment($storeId)) {
                    $interaction->createEnvironment($storeId);
                }

                $environment = $interaction->getEnvironment($storeId);
                $environment->getParameter('goodsounote_id')->setValue($noteId);
                $environment->getParameter('note_type')->setValue($noteType);

                $interaction->execute();
            }
        }
    }

}
