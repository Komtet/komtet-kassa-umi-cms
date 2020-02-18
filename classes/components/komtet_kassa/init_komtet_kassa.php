<?php

use UmiCms\Service;

$GUIDS = include 'guids.php';

$objectTypesCollection = umiObjectTypesCollection::getInstance();
$objectsCollection = umiObjectsCollection::getInstance();
$fieldsCollection = umiFieldsCollection::getInstance();
$langsCollection = langsCollection::getInstance();
$domainCollection = domainsCollection::getInstance();

if (!$objectTypesCollection->getTypeIdByGUID($GUIDS['config_type_komtet_kassa'])) {
    $parentTypeId = $objectTypesCollection->getTypeIdByGUID("root-settings-type");

    $configTypeName = "komtet_kassa_config";
    $configTypeId = $objectTypesCollection->addType($parentTypeId, $configTypeName);

    $type = $objectTypesCollection->getType($configTypeId);
    $type->setGUID($GUIDS['config_type_komtet_kassa']);
    $type->setIsLocked(true);


    $taxSystemTypeId = $objectTypesCollection->addType($parentTypeId, "Система налогооблажения");
    $taxSystemType = $objectTypesCollection->getType($taxSystemTypeId);
    $taxSystemType->setIsGuidable(true);
    $taxSystemType->setIsLocked(true);
    $taxSystemType->setGUID($GUIDS['sno_komtet_kassa']);
    $taxSystemTypeFieldId = $fieldsCollection->addField('sno', 'СНО', 16);
    $taxSystemTypeField = $fieldsCollection->getField($taxSystemTypeFieldId);
    $taxSystemTypeField->setIsRequired(true);


    $_0 = $objectsCollection->getObject($objectsCollection->addObject("Общая система налогообложения", $taxSystemTypeId));
    $_0->setValue("sno", "0");
    $_0->commit();

    $_1 = $objectsCollection->getObject($objectsCollection->addObject("Упрощенная система налогообложения (Доход)", $taxSystemTypeId));
    $_1->setValue("sno", "1");
    $_1->commit();

    $_2 = $objectsCollection->getObject($objectsCollection->addObject("Упрощенная система налогообложения (Доход минус Расход)", $taxSystemTypeId));
    $_2->setValue("sno", "2");
    $_2->commit();

    $_3 = $objectsCollection->getObject($objectsCollection->addObject("Единый налог на вмененный доход", $taxSystemTypeId));
    $_3->setValue("sno", "3");
    $_3->commit();

    $_4 = $objectsCollection->getObject($objectsCollection->addObject("Единый сельскохозяйственный налог", $taxSystemTypeId));
    $_4->setValue("sno", "4");
    $_4->commit();

    $_5 = $objectsCollection->getObject($objectsCollection->addObject("Патентная система налогообложения", $taxSystemTypeId));
    $_5->setValue("sno", "5");
    $_5->commit();


    $taxTypeId = $objectTypesCollection->addType($parentTypeId, "Налоговая ставка");
    $taxType = $objectTypesCollection->getType($taxTypeId);
    $taxType->setIsGuidable(true);
    $taxType->setIsLocked(true);
    $taxType->setGUID($GUIDS['vat_komtet_kassa']);
    $taxTypeFieldId = $fieldsCollection->addField('vat', 'НДС', 16);
    $taxTypeField = $fieldsCollection->getField($taxTypeFieldId);

    $_18_tax = $objectsCollection->getObject($objectsCollection->addObject("НДС 18%", $taxTypeId));
    $_18_tax->setValue("vat", '18');
    $_18_tax->commit();

    $_10_tax = $objectsCollection->getObject($objectsCollection->addObject("НДС 10%", $taxTypeId));
    $_10_tax->setValue("vat", '10');
    $_10_tax->commit();

    $without_tax = $objectsCollection->getObject($objectsCollection->addObject("Без НДС", $taxTypeId));
    $without_tax->setValue("vat", 'no');
    $without_tax->commit();

    $_0_tax = $objectsCollection->getObject($objectsCollection->addObject("НДС 0%", $taxTypeId));
    $_0_tax->setValue("vat", '0');
    $_0_tax->commit();

    $_118_tax = $objectsCollection->getObject($objectsCollection->addObject("расчетный НДС 18/118", $taxTypeId));
    $_118_tax->setValue("vat", '118');
    $_118_tax->commit();

    $_110_tax = $objectsCollection->getObject($objectsCollection->addObject("расчетный НДС 10/110", $taxTypeId));
    $_110_tax->setValue("vat", '110');
    $_110_tax->commit();


    $type->addFieldsGroup("fisc_params", "Параметры фискаизации", true, true);
    $fiscGroup = $type->getFieldsGroupByName('fisc_params');
    $is_print_check_FieldId = $fieldsCollection->addField('is_print_check', 'Печатать ли чек', true);
    $is_print_check_Field = $fieldsCollection->getField($is_print_check_FieldId);
    $is_print_check_Field->setIsRequired(false);
    $is_print_check_Field->setIsVisible(true);
    $is_print_check_Field->setImportanceStatus(true);

    $sno_FieldId = $fieldsCollection->addField('sno', 'Система налогообложения', '');
    $sno_Field = $fieldsCollection->getField($sno_FieldId);
    $sno_Field->setIsRequired(false);
    $sno_Field->setIsVisible(true);
    $sno_Field->setImportanceStatus(true);

    $fiscGroup->attachField($is_print_check_FieldId);
    $fiscGroup->attachField($sno_Field);


    $type->commit();

    $permissionsCollection = permissionsCollection::getInstance();
    $guestID = $permissionsCollection->getGuestId();
    $permissionsCollection->setModulesPermissions($guestID, 'komtet_kassa', 'guest', false);
}

    include "standalone.php";
    $sql = "CREATE TABLE IF NOT EXISTS `cms3_order_fiscalization_status` (`id` int(10) NOT NULL AUTO_INCREMENT,`order_id` int(10) NOT NULL,`status` varchar(25),`description` varchar(25), PRIMARY KEY (`id`), KEY `order_id` (`order_id`) );";
    $pool = ConnectionPool::getInstance();
    $connection = $pool->getConnection();
    $result = $connection->query($sql);

?>