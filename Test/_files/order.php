<?php
use Magento\Sales\Model\Order\Payment;

require __DIR__ . '/../_files/product_add.php';
require __DIR__ . '/../_files/order_mapper.php';
/** @var \Magento\Catalog\Model\Product $product */

$addressData = include __DIR__ . '/address_data.php';

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$date = new \DateTime();
foreach ($orderMapper as $incrementId => $orderData) {
    $billingAddress = $objectManager->create('Magento\Sales\Model\Order\Address', ['data' => $addressData]);
    $billingAddress->setAddressType('billing');

    $shippingAddress = clone $billingAddress;
    $shippingAddress->setId(null)->setAddressType('shipping');

    /** @var Payment $payment */
    $payment = $objectManager->create(Payment::class);
    $payment->setMethod('checkmo')
        ->setAdditionalInformation([
            'token_metadata' => [
                'token' => 'f34vjw',
                'customer_id' => 1
            ]
        ]);

    /** @var \Magento\Sales\Model\Order\Item $orderItem */
    $orderItem = $objectManager->create('Magento\Sales\Model\Order\Item');
    $orderItem->setProductId($orderData['product_id'])->setQtyOrdered($orderData['qty_ordered']);
    $orderItem->setBasePrice($orderData['product_price']);
    $orderItem->setPrice($orderData['product_price']);
    $orderItem->setRowTotal($orderData['product_price']);
    $orderItem->setProductType('simple');
    $orderItem->setSku('simple-'.$orderData['product_id']);
    $orderItem->setCreatedAt($date);

    /** @var \Magento\Sales\Model\Order $order */
    $order = $objectManager->create('Magento\Sales\Model\Order');
    $order->loadByIncrementId($incrementId);
    if ($order->getId()) {
        continue;
    }
    $order->setIncrementId(
        $incrementId
    )->setState(
        \Magento\Sales\Model\Order::STATE_PROCESSING
    )->setCreatedAt(
        $date
    )->setStatus(
        $order->getConfig()->getStateDefaultStatus(\Magento\Sales\Model\Order::STATE_PROCESSING)
    )->setSubtotal(
        $orderData['product_price'] * $orderData['qty_ordered']
    )->setGrandTotal(
        $orderData['product_price'] * $orderData['qty_ordered']
    )->setBaseSubtotal(
        $orderData['product_price'] * $orderData['qty_ordered']
    )->setBaseGrandTotal(
        $orderData['product_price'] * $orderData['qty_ordered']
    )->setCustomerIsGuest(
        true
    )->setCustomerEmail(
        'customer@null.com'
    )->setBillingAddress(
        $billingAddress
    )->setShippingAddress(
        $shippingAddress
    )->setStoreId(
        $objectManager->get('Magento\Store\Model\StoreManagerInterface')->getStore()->getId()
    )->addItem(
        $orderItem
    )->setPayment(
        $payment
    );
    $order->save();
}
