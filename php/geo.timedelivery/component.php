<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

if (!CModule::IncludeModule("iblock")) {
    ShowError("iblock module not installed");
    return;
}

/**
 * Получим рестораны
 */
$res = CIBlockElement::GetList(
                array("SORT" => "ASC"), array(
            "IBLOCK_ID" => $arParams['IBLOCK_ID_RESTAURANT'],
            "ACTIVE" => "Y"
                ), false, false, array()
);
$arResult['RESTAURANTS'] = array();
while ($arField = $res->GetNext()) {
    $arResult['RESTAURANTS'][] = $arField;
}

/**
 * Путь к компоненту
 */
$arResult["PATH_COMPONENT"] = $this->GetPath();

$this->IncludeComponentTemplate();
?>
