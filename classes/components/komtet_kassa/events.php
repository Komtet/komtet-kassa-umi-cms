<?php
    $handler = new umiEventListener('systemModifyObject', 'komtet_kassa', 'onModifyObject');
    $handler->setIsCritical(true);

    new umiEventListener('systemModifyPropertyValue', 'komtet_kassa', 'onSystemModifyPropertyValue');
    new umiEventListener('order-payment-status-changed', 'komtet_kassa', 'onPaymentStatusChanged');
