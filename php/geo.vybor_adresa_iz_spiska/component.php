<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
die();

/**
* Путь до компонента относительно корня сайта
*/
$arResult["PATH_COMPONENT"] = $this->GetPath();

$this->IncludeComponentTemplate();
?>
