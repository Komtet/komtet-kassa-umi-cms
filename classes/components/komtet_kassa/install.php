<?php

/**
 * @var array $INFO реестр модуля
 */
$INFO = [
    'name'                  => 'komtet_kassa', // Имя модуля
    'config' => '1', // У модуля есть настройки
    'default_method_admin' => 'config_module',
];

/**
 * @var array $COMPONENTS файлы модуля
 */
$COMPONENTS = [

    './classes/components/komtet_kassa/manifest/actions/KomtetKassaInit.php',
    './classes/components/komtet_kassa/manifest/install.xml',

    './classes/components/komtet_kassa/admin.php',
    './classes/components/komtet_kassa/class.php',
    './classes/components/komtet_kassa/customAdmin.php',
    './classes/components/komtet_kassa/customMacros.php',
    './classes/components/komtet_kassa/i18n.php', // языковые константы для Административной часи
    './classes/components/komtet_kassa/install.php',
    './classes/components/komtet_kassa/lang.php', //языковые константы для Клиентской части
    './classes/components/komtet_kassa/macros.php',
    './classes/components/komtet_kassa/permissions.php',
    './classes/components/komtet_kassa/events.php',
    './classes/components/komtet_kassa/handlers.php',

    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/Agent.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/AuthorisedPerson.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/Buyer.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/CalculationMethod.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/CalculationSubject.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/Cashier.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/Check.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/Client.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/Correction.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/CorrectionCheck.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/CourierManager.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/Nomenclature.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/Order.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/OrderManager.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/OrderPosition.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/Payment.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/Position.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/QueueManager.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/TaskManager.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/TaxSystem.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/Vat.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/Exception/ClientException.php',
    './classes/components/komtet_kassa/helpers/komtet-kassa-php-sdk/src/Exception/SdkException.php',
];

include 'init_komtet_kassa.php';

?>
