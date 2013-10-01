<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$arComponentDescription = array(
    "NAME" => "Поиск ближайшего ресторана",
    "DESCRIPTION" => 'Компонент модуля (Поиск ближайшего ресторана)',
    "ICON" => "/images/poisk_blizhaishego_restorana.gif",
    "CACHE_PATH" => "Y",
    "SORT" => 20,
    "PATH" => array(
        "ID" => "GEO_OBS",
        "NAME" => "ГЕО (ОБР)",
        "SORT" => 500,
    ),
);
?>