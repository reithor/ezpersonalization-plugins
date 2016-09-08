<?php

namespace Yoochoose\Tracking\Model\Observer;

use Magento\Framework\Event\Observer as ObserverData;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\View\Layout;

class SuccessfulOrder implements ObserverInterface
{

    /**
     * @var Layout
     */
    private $layout;

    public function __construct(Layout $layout)
    {
        $this->layout = $layout;
    }

    /**
     * @param ObserverData $observer
     */
    public function execute(ObserverData $observer)
    {
        $orderIds = $observer->getEvent()->getData('order_ids');
        if (empty($orderIds) || !is_array($orderIds)) {
            return;
        }

        $block = $this->layout->getBlock('yoochoose_tracking_block_head');
        if ($block) {
            $block->setData('orderId', end($orderIds));
        }
    }
}
