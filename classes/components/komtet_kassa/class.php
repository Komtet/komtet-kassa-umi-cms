<?php
/**
 * Модуль заглушка
 * 
 */

use UmiCms\Service;


class komtet_kassa extends def_module {

    /** Конструктор */
    public function __construct() {
        parent::__construct();

        if (Service::Request()->isAdmin()) {

            $this->__loadLib('admin.php');
            $this->__implement('Komtet_kassaAdmin');

            $this->loadAdminExtension();

            $this->__loadLib('customAdmin.php');
            $this->__implement('Komtet_kassaCustomAdmin', true);
        }

        $this->__loadLib('macros.php');
        $this->__implement('Komtet_kassaMacros');

        $this->loadSiteExtension();

        $this->__loadLib('customMacros.php');
        $this->__implement('Komtet_kassaCustomMacros', true);

        $this->__loadLib('handlers.php');
        $this->__implement('Komtet_kassaHandlers');

        $this->loadCommonExtension();
        $this->loadTemplateCustoms();

    }

    /**
     * Возвращает ссылки на форму редактирования страницы модуля и
     * на форму добавления дочернего элемента к странице.
     * @param int $element_id идентификатор страницы модуля
     * @param string|bool $element_type тип страницы модуля
     * @return array
     */
    public function getEditLink($element_id, $element_type = false) {
        return [
            false,
            $this->pre_lang . "/admin/komtet_kassa/editPage/{$element_id}/"
        ];
    }

    public function getSnosOption() {
        $regEdit = Service::Registry();
        $GUIDS = include 'guids.php';
        $currentSno = $regEdit->get('//modules/komtet_kassa/sno');

        $objectTypesCollection = umiObjectTypesCollection::getInstance();
        $objectsCollection = umiObjectsCollection::getInstance();

        $snoTypeId = $objectTypesCollection->getTypeIdByGUID($GUIDS['sno_komtet_kassa']);
        $snosObjects = $objectsCollection->getGuidedItems($snoTypeId);

        $snosObjects['value'] = $currentSno;
        return $snosObjects;

    }

    public function getVatsOption() {
        $regEdit = Service::Registry();
        $GUIDS = include 'guids.php';
        $currentVat = $regEdit->get('//modules/komtet_kassa/vat');

        $objectTypesCollection = umiObjectTypesCollection::getInstance();
        $objectsCollection = umiObjectsCollection::getInstance();

        $vatTypeId = $objectTypesCollection->getTypeIdByGUID($GUIDS['vat_komtet_kassa']);
        $vatsObjects = $objectsCollection->getGuidedItems($vatTypeId);

        $vatsObjects['value'] = $currentVat;
        return $vatsObjects;
    }

};
?>
