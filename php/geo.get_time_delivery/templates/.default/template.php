<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();
?>
<?
$APPLICATION->RestartBuffer();
//echo ceil($arResult['route']['time'] / 60);
echo $arResult['route']['time'];
die();
?>
<?
//Example usage
//
//require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");
//
//$APPLICATION->IncludeComponent(
//	"obs:geo.get_time_delivery",
//	".default",
//	Array(
//		"IBLOCK_TYPE" => "restaurants",
//		"IBLOCK_ID_RESTAURANT" => "7",
//		"CITY" => $_REQUEST['city'], // Москва
//		"STREET" => $_REQUEST['street'], // Братская Улица
//		"NUMBER_HOME" => $_REQUEST['number_home'], // 1/45
//		"RESTAURANT_ID" => $_REQUEST['restaurant_id'], // 15860
//		"DELIVERY_TYPE" => $_REQUEST['delivery_type'], // any
//	)
//);

// Example url
// http://dostavka-sushi/1.php?city=%D0%9C%D0%BE%D1%81%D0%BA%D0%B2%D0%B0&street=%D0%91%D1%80%D0%B0%D1%82%D1%81%D0%BA%D0%B0%D1%8F+%D0%A3%D0%BB%D0%B8%D1%86%D0%B0&number_home=1%2F45&restaurant_id=15860&delivery_type=any
?>