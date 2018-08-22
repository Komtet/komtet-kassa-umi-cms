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
    
    './classes/components/komtet_kassa/helpers/KomtetCheck.php',
    './classes/components/komtet_kassa/helpers/KomtetClient.php',
    './classes/components/komtet_kassa/helpers/KomtetPayment.php',
    './classes/components/komtet_kassa/helpers/KomtetPosition.php',
    './classes/components/komtet_kassa/helpers/KomtetQueueManager.php',
    './classes/components/komtet_kassa/helpers/KomtetVat.php',
    './classes/components/komtet_kassa/helpers/Exception/ClientException.php',
    './classes/components/komtet_kassa/helpers/Exception/SdkException.php',
];

include 'init_komtet_kassa.php';

?>
