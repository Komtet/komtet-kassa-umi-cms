<?php
/**
 * Класс функционала административной панели
 */
use UmiCms\Service;

class Komtet_kassaAdmin {

    use baseModuleAdmin;

    /**
     * @var cloud_payments $module
     */
    public $module;

    /**
     * Возвращает приветсвенную страницу модуля
     * @throws coreException
     * @throws expectElementException
     * @throws wrongElementTypeAdminException
     */

    public function config_module() {

        $config = mainConfiguration::getInstance();
        $umiRegistry = Service::Registry();

        $params = [
                'config' => [
                        'string:shop_id' => null,
                        'string:shop_secret' => null,
                        'string:queue_id' => null,
                        'select:sno' => null,
                        'select:vat' => null,
                        'boolean:is_print_check' => null,
                ]
        ];

        $mode = getRequest('param0');

        if ($mode == 'do') {
            $params = $this->expectParams($params);

            $umiRegistry->set('//modules/komtet_kassa/shop_id', $params['config']['string:shop_id']);
            $umiRegistry->set('//modules/komtet_kassa/shop_secret', $params['config']['string:shop_secret']);
            $umiRegistry->set('//modules/komtet_kassa/queue_id', $params['config']['string:queue_id']);
            $umiRegistry->set('//modules/komtet_kassa/is_print_check', $params['config']['boolean:is_print_check']);
            $umiRegistry->set('//modules/komtet_kassa/sno', $params['config']['select:sno']);
            $umiRegistry->set('//modules/komtet_kassa/vat', $params['config']['select:vat']);
            $this->chooseRedirect();
        }

        $params['config']['string:shop_id'] = (string) $umiRegistry->get('//modules/komtet_kassa/shop_id');
        $params['config']['string:shop_secret'] = (string) $umiRegistry->get('//modules/komtet_kassa/shop_secret');
        $params['config']['string:queue_id'] = (string) $umiRegistry->get('//modules/komtet_kassa/queue_id');
        $params['config']['boolean:is_print_check'] = (boolean) $umiRegistry->get('//modules/komtet_kassa/is_print_check');
        $params['config']['select:vat'] = $this->module->getVatsOption();
        $params['config']['select:sno'] = $this->module->getSnosOption();

        $this->setDataType('settings');
        $this->setActionType('modify');
        $data = $this->prepareData($params, 'settings');
        $this->setData($data);
        $this->doData();

    }
}
?>