<?php

    use UmiCms\Service;
    use Komtet\KassaSdk\Payment as KomtetPayment;
    use Komtet\KassaSdk\Check as KomtetCheck;
    use Komtet\KassaSdk\Vat as KomtetVat;
    use Komtet\KassaSdk\Position as KomtetPosition;
    use Komtet\KassaSdk\Client as KomtetClient;
    use Komtet\KassaSdk\QueueManager as KomtetQueueManager;


    /** Класс обработчиков событий */
    class Komtet_kassaHandlers {
        /** @var appointment $module */
        public $module;

        public function onModifyObject(iUmiEventPoint $event) {
            if ($event->getMode() == 'after') {

                $object = $event->getRef('object');

                if (!$object instanceof iUmiObject) {
                    return false;
                }

                $typeId = umiObjectTypesCollection::getInstance()->getTypeIdByHierarchyTypeName('emarket', 'order');
                if ($object->getTypeId() != $typeId) {
                    return false;
                }

                $order = order::get($object->getId());
                $this->fiscalize($order, $order->getValue('payment_status_id'));

                return true;
            }

            return false;
        }

        public function onSystemModifyPropertyValue(iUmiEventPoint $event) {

            if ($event->getMode() == 'after' && $event->getParam('property') == 'payment_status_id') {
                $entity = $event->getRef('entity');
                $order = order::get($entity->getId());
                $this->fiscalize($order, $event->getParam('newValue'));
                return true;
            }

            return false;
        }


        public function onPaymentStatusChanged(iUmiEventPoint $event) {

            if ($event->getMode() == 'after') {
                $this->fiscalize($event->getRef('order'), $event->getParam('new-status-id'));
                return true;
            }

            return false;
        }

        private function fiscalize($order, $paymentStatus) {
            include "standalone.php";

            $sql = "SELECT * FROM cms3_order_fiscalization_status WHERE order_id = ".$order->getNumber();
            $connection = ConnectionPool::getInstance()->getConnection();
            $result = $connection->queryResult($sql);
            $singleRow = $result->fetch();

            $orderFiscStatus = null;
            if($singleRow) {
                $orderFiscStatus = $singleRow['status'];
            }

            $orderReturned = $order->getValue('delivery_status_id') == $order->getStatusByCode('return', 'order_delivery_status');

            if (!(($orderFiscStatus == 'done' && $orderReturned) || ($orderFiscStatus != 'done' && $order->isOrderPayed()))) {
                return false;
            }

            $umiRegistry = Service::Registry();

            $vatId = $umiRegistry->get('//modules/komtet_kassa/vat');
            $vatObject = umiObjectsCollection::getInstance()->getObject($vatId);
            $vat = $vatObject->getValue('vat');

            $snoId = $umiRegistry->get('//modules/komtet_kassa/sno');
            $snoObject = umiObjectsCollection::getInstance()->getObject($snoId);
            $sno = $snoObject->getValue('sno');

            $isPrintCheck = $umiRegistry->get('//modules/komtet_kassa/is_print_check');
            $queueId = $umiRegistry->get('//modules/komtet_kassa/queue_id');
            $shopId = $umiRegistry->get('//modules/komtet_kassa/shop_id');
            $secret = $umiRegistry->get('//modules/komtet_kassa/shop_secret');

            include_once (__DIR__."/helpers/komtet-kassa-php-sdk/src/Payment.php");
            include_once (__DIR__."/helpers/komtet-kassa-php-sdk/src/Check.php");
            include_once (__DIR__."/helpers/komtet-kassa-php-sdk/src/Vat.php");
            include_once (__DIR__."/helpers/komtet-kassa-php-sdk/src/Position.php");
            include_once (__DIR__."/helpers/komtet-kassa-php-sdk/src/Client.php");
            include_once (__DIR__."/helpers/komtet-kassa-php-sdk/src/QueueManager.php");
            include_once (__DIR__."/helpers/komtet-kassa-php-sdk/src/Exception/SdkException.php");
            include_once (__DIR__."/helpers/komtet-kassa-php-sdk/src/Exception/ClientException.php");

            if ($orderFiscStatus) {
                $sql = "UPDATE cms3_order_fiscalization_status SET status = 'pending' WHERE order_id = ".$order->getNumber();
                $pool = ConnectionPool::getInstance();
                $connection = $pool->getConnection();
                $result = $connection->query($sql);
            }
            else {
                $sql = "INSERT INTO cms3_order_fiscalization_status (order_id, status) VALUES(".$order->getNumber().", 'pending');";
                $pool = ConnectionPool::getInstance();
                $connection = $pool->getConnection();
                $result = $connection->query($sql);
            }

            $positions = $order->getItems();

            $payment = new KomtetPayment(KomtetPayment::TYPE_CARD, floatval($order->getActualPrice()));

            $method = $orderReturned ? KomtetCheck::INTENT_SELL_RETURN : KomtetCheck::INTENT_SELL;

            // Получаем email или телефон покупателя
            $customerId = $order->getValue('customer_id');
            $customer = umiObjectsCollection::getInstance()->getObject($customerId);
            $customerEmail = $customer->getValue('e-mail');
            $customerPhone = $customer->getValue('phone');
            $contact = $customerEmail ? $customerEmail : $customerPhone;

            $check = new KomtetCheck($order->getNumber(), $contact, $method, $sno);
            $check->setShouldPrint($isPrintCheck);
            $check->addPayment($payment);

            // version compatibility
            $vat = intval($vat);
            switch ($vat) {
                case 1:
                    $vatObject = new KomtetVat(KomtetVat::RATE_NO);
                    break;
                case 2:
                    $vatObject = new KomtetVat(KomtetVat::RATE_0);
                    break;
                case 3:
                    $vatObject = new KomtetVat(KomtetVat::RATE_10);
                    break;
                case 4:
                    $vatObject = new KomtetVat(KomtetVat::RATE_20);
                    break;
                case 5:
                    $vatObject = new KomtetVat(KomtetVat::RATE_110);
                    break;
                case 6:
                    $vatObject = new KomtetVat(KomtetVat::RATE_120);
                    break;
                case 7:
                    $vatObject = new KomtetVat(KomtetVat::RATE_22);
                    break;
                case 8:
                    $vatObject = new KomtetVat(KomtetVat::RATE_122);
                    break;
                case 9:
                    $vatObject = new KomtetVat(KomtetVat::RATE_5);
                    break;
                case 10:
                    $vatObject = new KomtetVat(KomtetVat::RATE_105);
                    break;
                case 11:
                    $vatObject = new KomtetVat(KomtetVat::RATE_7);
                    break;
                case 12:
                    $vatObject = new KomtetVat(KomtetVat::RATE_107);
                    break;
                default:
                    $vatObject = new KomtetVat(KomtetVat::RATE_NO);
            }

            foreach($positions as $position) {
                $positionObj = new KomtetPosition($position->getName(),
                                                  floatval($position->getActualPrice()),
                                                  floatval($position->getAmount()),
                                                  floatval($position->getTotalActualPrice()),
                                                  floatval($position->getDiscountValue()),
                                                  $vatObject);
                $check->addPosition($positionObj);
            }

            $check->applyDiscount($order->getDiscountValue());

            if (floatval($order->getDeliveryPrice()) > 0) {
                $shippingPosition = new KomtetPosition("Доставка",
                                                       floatval($order->getDeliveryPrice()),
                                                       1,
                                                       floatval($order->getDeliveryPrice()),
                                                       0,
                                                       $vatObject);
                $check->addPosition($shippingPosition);
            }

            $client = new KomtetClient($shopId, $secret);
            $queueManager = new KomtetQueueManager($client);

            $queueManager->registerQueue('print_queue', $queueId);

            try {
                $queueManager->putCheck($check, 'print_queue');
            } catch (SdkException $e) {
                echo $e->getMessage();
            }

            return true;
        }


    }