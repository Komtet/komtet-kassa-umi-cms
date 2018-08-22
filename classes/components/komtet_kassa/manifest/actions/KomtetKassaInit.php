<?php

namespace UmiCms\Manifest\KomtetKassa;

use UmiCms\Service;

class KomtetKassaInitAction extends \Action {

    /** @inheritdoc */
    public function execute() {
        include "standalone.php";

        $sql = "CREATE TABLE IF NOT EXISTS `cms3_order_fiscalization_status` (`id` int(10) NOT NULL AUTO_INCREMENT,`order_id` int(10) NOT NULL,`status` varchar(25),`description` varchar(25), PRIMARY KEY (`id`), KEY `order_id` (`order_id`) );"
        $connection = ConnectionPool::getInstance()->getConnection();
        $result = $connection->query($sql);

    }

    /** @inheritdoc */
    public function rollback() {
        return $this;
    }
}
