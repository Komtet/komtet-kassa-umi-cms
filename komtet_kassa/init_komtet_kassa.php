<?php

use UmiCms\Service;

$GUIDS = include 'guids.php';

$objectTypesCollection = umiObjectTypesCollection::getInstance();
$objectsCollection = umiObjectsCollection::getInstance();
$fieldsCollection = umiFieldsCollection::getInstance();
$langsCollection = langsCollection::getInstance();
$domainCollection = domainsCollection::getInstance();

if (!$objectTypesCollection->getTypeIdByGUID($GUIDS['config_type_komtet_kassa'])) {
    $parentTypeId = $objectTypesCollection->getTypeIdByGUID("root-guides-type");

    $configTypeName = "komtet_kassa_config";
    $configTypeId = $objectTypesCollection->addType($parentTypeId, $configTypeName);

    $configType = $objectTypesCollection->getType($configTypeId);
    $configType->setGUID($GUIDS['config_type_komtet_kassa']);
    // $configType->setIsLocked(true);

    $configType->addFieldsGroup("fisc_params", "Параметры фискаизации", true, true);
    $fiscGroup = $configType->getFieldsGroupByName('fisc_params');

    $snoTypeId = $objectTypesCollection->addType($parentTypeId, "Система налогооблажения");
    $snoType = $objectTypesCollection->getType($snoTypeId);
    $snoType->setIsGuidable(true);
    $snoType->setIsLocked(true);
    $snoType->setGUID($GUIDS['sno']);
    $snoType->addFieldsGroup("sno_settings", "Система налогооблажения", true, true);
    $snoType->commit();
    $snoTypeGroup = $snoType->getFieldsGroupByName('sno_settings');
    $snoTypeFieldId = $fieldsCollection->addField('sno', 'СНО', 16);
    $snoTypeField = $fieldsCollection->getField($snoTypeFieldId);
    $snoTypeField->setIsRequired(true);
    $snoTypeGroup->attachField($snoTypeFieldId);

    $_osn_sno = $objectsCollection->getObject($objectsCollection->addObject("Общая система налогообложения", $snoTypeId));
    $_osn_sno->setValue("sno", "0");
    $_osn_sno->commit();

    $_usn_sno = $objectsCollection->getObject($objectsCollection->addObject("Упрощенная система налогообложения (Доход)", $snoTypeId));
    $_usn_sno->setValue("sno", "1");
    $_usn_sno->commit();

    $_usndr_sno = $objectsCollection->getObject($objectsCollection->addObject("Упрощенная система налогообложения (Доход минус Расход)", $snoTypeId));
    $_usndr_sno->setValue("sno", "2");
    $_usndr_sno->commit();

    $_esn_sno = $objectsCollection->getObject($objectsCollection->addObject("Единый сельскохозяйственный налог", $snoTypeId));
    $_esn_sno->setValue("sno", "4");
    $_esn_sno->commit();

    $_patent_sno = $objectsCollection->getObject($objectsCollection->addObject("Патентная система налогообложения", $snoTypeId));
    $_patent_sno->setValue("sno", "5");
    $_patent_sno->commit();


    $vatTypeId = $objectTypesCollection->addType($parentTypeId, "Налоговая ставка");
    $vatType = $objectTypesCollection->getType($vatTypeId);
    $vatType->setIsGuidable(true);
    $vatType->setIsLocked(true);
    $vatType->setGUID($GUIDS['vat']);
    $vatType->addFieldsGroup('vat_settings', "Налоговая ставка", true, true);
    $vatType->commit();
    $vatTypeGroup = $vatType->getFieldsGroupByName('vat_settings');
    $vatTypeFieldId = $fieldsCollection->addField('vat', 'НДС', 16);
    $vatTypeField = $fieldsCollection->getField($vatTypeFieldId);
    $vatTypeField->setIsRequired(true);
    $vatTypeGroup->attachField($vatTypeFieldId);

    $without_vat = $objectsCollection->getObject($objectsCollection->addObject("Без НДС", $vatTypeId));
    $without_vat->setValue('vat', 1);
    $without_vat->commit();

    $_0_vat = $objectsCollection->getObject($objectsCollection->addObject("НДС 0%", $vatTypeId));
    $_0_vat->setValue('vat', 2);
    $_0_vat->commit();

    $_10_vat = $objectsCollection->getObject($objectsCollection->addObject("НДС 10%", $vatTypeId));
    $_10_vat->setValue('vat', 3);
    $_10_vat->commit();

    $_20_vat = $objectsCollection->getObject($objectsCollection->addObject("НДС 20%", $vatTypeId));
    $_20_vat->setValue('vat', 4);
    $_20_vat->commit();

    $_110_vat = $objectsCollection->getObject($objectsCollection->addObject("расчетный НДС 10/110", $vatTypeId));
    $_110_vat->setValue('vat', 5);
    $_110_vat->commit();

    $_120_vat = $objectsCollection->getObject($objectsCollection->addObject("расчетный НДС 20/120", $vatTypeId));
    $_120_vat->setValue('vat', 6);
    $_120_vat->commit();

    $isPrintTypeId = $objectTypesCollection->addType($parentTypeId, "Печатать ли чек");
    $isPrintType = $objectTypesCollection->getType($isPrintTypeId);
    $isPrintType->setIsGuidable(true);
    $isPrintType->setIsLocked(true);
    $isPrintType->commit();

    $is_print_check_FieldId = $fieldsCollection->addField('is_print_check', "Печатать ли чек", '1', true);
    $is_print_check_Field = $fieldsCollection->getField($is_print_check_FieldId);
    $is_print_check_Field->setIsRequired(false);
    $is_print_check_Field->setIsVisible(true);
    $is_print_check_Field->setImportanceStatus(true);
    $fiscGroup->attachField($is_print_check_FieldId);

    $sno_FieldId = $fieldsCollection->addField('sno', "Система налогообложения", 16);
    $sno_Field = $fieldsCollection->getField($sno_FieldId);
    $sno_Field->setIsRequired(false);
    $sno_Field->setIsVisible(true);
    $sno_Field->setImportanceStatus(true);
    $sno_Field->setGuideId($snoTypeId);
    $fiscGroup->attachField($sno_Field);

    $vat_FieldId = $fieldsCollection->addField('vat', "Налоговая ставка", 16);
    $vat_Field = $fieldsCollection->getField($vat_FieldId);
    $vat_Field->setIsRequired(false);
    $vat_Field->setIsVisible(true);
    $vat_Field->setImportanceStatus(true);
    $vat_Field->setGuideId($vatTypeId);
    $fiscGroup->attachField($vat_Field);

    $configType->commit();

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