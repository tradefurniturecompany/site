<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Magento;

class Website implements \Magento\Framework\Option\ArrayInterface
{
    protected $_options;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    protected function _initOptions()
    {
        $all = array(
            'value' => 0,
            'label' => 'All Websites'.' ['. $this->storeManager->getStore()->getCurrentCurrency()->getCode() .']'
        );

        $options[] = $all;

        foreach ($this->storeManager->getWebsites() as $website) {
            $data = array(
                'value' => $website->getId(),
                'label' => $website->getName().' ['. $website->getBaseCurrencyCode().']'
            );

            $options[] = $data;
        }

        $this->_options = $options;
        return $this;
    }

    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_initOptions();
        }
        return $this->_options;
    }

    public function toArray()
    {
        if (!$this->_options) {
            $this->_initOptions();
        }

        $options = array();
        foreach($this->_options as $_opt){
            $options[ $_opt['value'] ] = $_opt['label'];
        }
        return $options;
    }
}
