<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

/**
 * ������� ���� ����������
 */
$arIBlockType = CIBlockParameters::GetIBlockTypes();

/**
 * ������� ���������
 */
$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("TYPE" => $arCurrentValues["IBLOCK_TYPE"], "ACTIVE"=>"Y"));
while($arr=$rsIBlock->Fetch())
	$arIBlock[$arr["ID"]] = "[".$arr["ID"]."] ".$arr["NAME"];

$arComponentParameters = array(
    "PARAMETERS" => array(
		"IBLOCK_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => "��� ���������",
			"TYPE" => "LIST",
			"VALUES" => $arIBlockType,
			"REFRESH" => "Y",
		),
		"IBLOCK_ID_RESTAURANT" => array(
			"PARENT" => "BASE",
			"NAME" => "�������� ����������",
			"TYPE" => "LIST",
			"ADDITIONAL_VALUES" => "Y",
			"VALUES" => $arIBlock,
		),
        "CITY" => array(
            "NAME" => "�����",
            "TYPE" => "STRING",
            "DEFAULT" => "������",
            "PARENT" => "BASE",
        ),
        "STREET" => array(
            "NAME" => "�����",
            "TYPE" => "STRING",
            "DEFAULT" => "",
            "PARENT" => "BASE",
        ),
        "NUMBER_HOME" => array(
            "NAME" => "����� ����",
            "TYPE" => "STRING",
            "DEFAULT" => "",
            "PARENT" => "BASE",
        ),
        "RESTAURANT_ID" => array(
            "NAME" => "ID ���������",
            "TYPE" => "STRING",
            "DEFAULT" => "",
            "PARENT" => "BASE",
        ),
        "DELIVERY_TYPE" => array(
            "NAME" => "��� �������� any, walking ��� driving",
            "TYPE" => "STRING",
            "DEFAULT" => "any",
            "PARENT" => "BASE",
        ),
    )
);
?>