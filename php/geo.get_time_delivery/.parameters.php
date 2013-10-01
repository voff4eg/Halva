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
			"NAME" => "Инфоблок ресторанов",
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
		),
        "CITY" => array(
            "NAME" => "Город",
            "TYPE" => "STRING",
            "DEFAULT" => "Москва",
            "PARENT" => "BASE",
        ),
        "STREET" => array(
            "NAME" => "Улица",
            "TYPE" => "STRING",
            "DEFAULT" => "",
            "PARENT" => "BASE",
        ),
        "NUMBER_HOME" => array(
            "NAME" => "Номер дома",
            "TYPE" => "STRING",
            "DEFAULT" => "",
            "PARENT" => "BASE",
        ),
        "RESTAURANT_ID" => array(
            "NAME" => "ID ресторана",
            "TYPE" => "STRING",
            "DEFAULT" => "",
            "PARENT" => "BASE",
        ),
        "DELIVERY_TYPE" => array(
            "NAME" => "Тип доставки any, walking или driving",
            "TYPE" => "STRING",
            "DEFAULT" => "any",
            "PARENT" => "BASE",
        ),
    )
);
?>