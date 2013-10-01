<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$arComponentDescription = array(
    "NAME" => "Определение времени доставки между рестораном и адресом",
    "DESCRIPTION" => 'Компонент модуля (определение времени доставки между рестораном и адресом)',
    "ICON" => "/images/timedelivery.gif",
    "CACHE_PATH" => "Y",
    "SORT" => 10,
    "PATH" => array(
        "ID" => "GEO_OBS",
        "NAME" => "ГЕО (ОБР)",
        "SORT" => 500,
    ),
);
?>