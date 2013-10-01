<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$arComponentDescription = array(
    "NAME" => "Выбор адреса из списка",
    "DESCRIPTION" => 'Компонент модуля (выбор адреса из списка)',
    "ICON" => "/images/select_address.gif",
    "CACHE_PATH" => "Y",
    "SORT" => 10,
    "PATH" => array(
        "ID" => "GEO_OBS",
        "NAME" => "ГЕО (ОБР)",
        "SORT" => 500,
    ),
);
?>