<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

/**
 * Получим типы инфоблоков
 */
$arIBlockType = CIBlockParameters::GetIBlockTypes();

/**
 * Получим инфоблоки
 */
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arComponentParameters = array(
    "PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => "Тип инфоблока",
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID_RESTAURANT" => array(
			"PARENT" => "BASE",
			"NAME" => "Инфоблок рестораны",
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
		),
        "PREFIX" => array(
            "NAME" => "Уникальная строка указаная в компоненте выбор адреса из списка",
            "TYPE" => "STRING",
            "DEFAULT" => "",
            "PARENT" => "BASE",
        )
    )
);
?>
