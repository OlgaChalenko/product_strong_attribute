<?php

namespace Maghos\ProductStrongAttribute\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
class ProductAttribute implements ObserverInterface
{
  /**
   * @var \Magento\Framework\App\Config\ScopeConfigInterface
   */
    protected $scopeConfig;
    protected $storeScope;

    /**
     * Recipient email config path
     */
    const CONFIG_PATH = 'catalog/fields_product_strong_attribute/words';
    const SCOPE_STORE = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

    /**
     * Below is the method that will fire whenever the event runs!
     *
     * @param Observer $observer
     */

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
        $this->storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
    }

    public function execute(Observer $observer)
    {
        if($product = $observer->getProduct()) {
            $rules = trim($this->scopeConfig->getValue(self::CONFIG_PATH, self::SCOPE_STORE));
            $rules = explode(',', preg_replace("/\r\n|\r|\n/u", ' , ', $rules));
            if($rules){
                usort($rules, function ($a, $b) {
                    return mb_strlen($a, 'UTF-8') - mb_strlen($b, 'UTF-8');
                });
                krsort($rules);
                $replace = [];
                foreach ($rules as $rule){
                    $replace[] = "<strong>{$rule}</strong>";
                }
                $old_description = $product->getDescription();
                $new_description = str_replace($rules, $replace, $old_description);
                $product->setDescription($new_description);
            }
        }
    }
}
