<?php
	new umiEventListener('systemModifyPropertyValue', 'komtet_kassa', 'onSystemModifyPropertyValue');
	new umiEventListener('order-payment-status-changed', 'komtet_kassa', 'onPaymentStatusChanged');
