<?php

	use UmiCms\Service;
	use Komtet\KassaSdk\KomtetPayment;
	use Komtet\KassaSdk\KomtetCheck;
	use Komtet\KassaSdk\KomtetVat;
	use Komtet\KassaSdk\KomtetPosition;
	use Komtet\KassaSdk\KomtetClient;
	use Komtet\KassaSdk\KomtetQueueManager;


	/** Класс обработчиков событий */
	class Komtet_kassaHandlers {
		/** @var appointment $module */
		public $module;

		public function onSystemModifyPropertyValue(iUmiEventPoint $event) {

			if ($event->getParam('property') != 'payment_status_id') {
				return false;
			}

			if ($event->getMode() != 'after') {
				return false;
			}

			if ($event->getParam('oldValue') == $event->getParam('newValue')) {
				return false;
			}

			// 327 - object-prinyata
			// 326 - object-otklonena
			if (!in_array($event->getParam('newValue'), Array('327', '326'))) {
				return false;
			}

			$entity = $event->getRef('entity');
			$objectsCollection = umiObjectsCollection::getInstance();
			$order = order::get($entity->getId());

			$sql = "SELECT * FROM cms3_order_fiscalization_status WHERE order_id = ".$order->getNumber();
			$connection = ConnectionPool::getInstance()->getConnection();
			$result = $connection->queryResult($sql);
			$singleRow = $result->fetch();
			$order_status = $singleRow['status'];

			if ($order_status == 'done' || ($order_status == 'done' && $event->getParam('newValue') != '326')) {
				return false;
			}

			if($order_status) {
				$order_exists = true;
			}
			else {
				$order_exists = false;
			}

			$this->fiscalize($order, $event->getParam('newValue'), $order_exists);

			return true;
		}


		public function onPaymentStatusChanged(iUmiEventPoint $event) {

			if ($event->getMode() != 'after') {
				return false;
			}

			if ($event->getParam('old-status-id') == $event->getParam('new-status-id')) {
				return false;
			}

			// 327 - object-prinyata
			// 326 - object-otklonena
			if (!in_array($event->getParam('new-status-id'), Array('327', '326'))) {
				return false;
			}

			$order = $event->getRef('order');

			$sql = "SELECT * FROM cms3_order_fiscalization_status WHERE order_id = ".$order->getNumber();
			$connection = ConnectionPool::getInstance()->getConnection();
			$result = $connection->queryResult($sql);
			$singleRow = $result->fetch();
			$order_status = $singleRow['status'];

			if ($order_status == 'done' || ($order_status == 'done' && $event->getParam('new-status-id') != '326')) {
				return false;
			}

			if($order_status) {
				$order_exists = true;
			}
			else {
				$order_exists = false;
			}

			$this->fiscalize($order, $event->getParam('new-status-id'), $order_exists);

			return true;
		}

		private function fiscalize($order, $paymentStatus, $order_exists) {
			include "standalone.php";

			$umiRegistry = Service::Registry();

			$vatId = $umiRegistry->get('//modules/komtet_kassa/vat');
			$sql = "SELECT varchar_val FROM cms3_object_content WHERE obj_id = ".$vatId;
			$connection = ConnectionPool::getInstance()->getConnection();
			$result = $connection->queryResult($sql);
			$singleRow = $result->fetch();
			$vat = $singleRow['varchar_val'];

			$snoId = $umiRegistry->get('//modules/komtet_kassa/sno');
			$sql = "SELECT varchar_val FROM cms3_object_content WHERE obj_id = ".$snoId;
			$connection = ConnectionPool::getInstance()->getConnection();
			$result = $connection->queryResult($sql);
			$singleRow = $result->fetch();
			$sno = intval($singleRow['varchar_val']);

			$is_print_check = $umiRegistry->get('//modules/komtet_kassa/is_print_check');
			$queue_id = $umiRegistry->get('//modules/komtet_kassa/queue_id');
			$shop_id = $umiRegistry->get('//modules/komtet_kassa/shop_id');
			$secret = $umiRegistry->get('//modules/komtet_kassa/shop_secret');

			include_once (__DIR__."/helpers/KomtetPayment.php");
			include_once (__DIR__."/helpers/KomtetCheck.php");
			include_once (__DIR__."/helpers/KomtetVat.php");
			include_once (__DIR__."/helpers/KomtetPosition.php");
			include_once (__DIR__."/helpers/KomtetClient.php");
			include_once (__DIR__."/helpers/KomtetQueueManager.php");

			if ($order_exists) {
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

			$payment = KomtetPayment::createCard(floatval($order->getActualPrice()));

			// 327 - object-prinyata
			// 326 - object-otklonena

			$payment_status = $order->getPaymentStatus();
			$method = $payment_status == '326' ? KomtetCheck::INTENT_SELL_RETURN : KomtetCheck::INTENT_SELL;

			$customer_id = $order->getCustomerId();
			$customer = customer::get($customer_id);

			$check = new KomtetCheck($order->getNumber(), $customer->getEmail(), $method, $sno);
			$check->setShouldPrint($is_print_check);
			$check->addPayment($payment);

			if ($vat) {
				$vat_object = new KomtetVat($vat);
			}
			else{
				$vat_object = null;
			}
			foreach( $positions as $position )
			{
				$positionObj = new KomtetPosition($position->getName(),
												  floatval($position->getActualPrice()),
												  floatval($position->getAmount()),
												  floatval($position->getTotalActualPrice()),
												  floatval($position->getDiscountValue()),
												  $vat_object);
				$check->addPosition($positionObj);
			}

			if (floatval($order->getDeliveryPrice()) > 0) {
				$shippingPosition = new KomtetPosition("Доставка",
												 	   floatval($order->getDeliveryPrice()),
												 	   1,
												 	   floatval($order->getDeliveryPrice()),
												 	   0,
												 	   $vat_object);
				$check->addPosition($shippingPosition);
			}

			$client = new KomtetClient($shop_id, $secret);
			$queueManager = new KomtetQueueManager($client);

			$queueManager->registerQueue('print_queue', $queue_id);

			// print_r("ok");
			// die();

			try {
			    $queueManager->putCheck($check, 'print_queue');
			} catch (SdkException $e) {
			    echo $e->getMessage();
			}
			return true;
		}


	}