<?php
	use UmiCms\Service;
	/**
	 * Класс макросов, то есть методов, доступных в шаблоне
	 */
	class Komtet_kassaMacros {
		/**
		 * @var komtet_kassa $module
		 */

		public $module;

		public function report() {
			include "standalone.php";

			$rawData = file_get_contents("php://input");
			$state = $_POST['state'];
			$order_id = $_POST['external_id'];
			$description = $_POST['error_description'];

			$sql = "UPDATE cms3_order_fiscalization_status SET status = '".$state."' , description = '".$description."' WHERE order_id = ".$order_id.";";

			$connection = ConnectionPool::getInstance()->getConnection();
			$result = $connection->queryResult($sql);
			
		}

	}
?>