<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
//echo '<div align="left"><pre>';
//print_r($arParams);
//echo '</pre></div>';
/**
 * Подключение модуля КЛАДР
 */
if (!CModule::IncludeModule("grain.kladr")) {
    ShowError("grain.kladr module not installed");
    return;
}

if (!CModule::IncludeModule("iblock")) {
    ShowError("iblock module not installed");
    return;
}

$arFilter = Array("IBLOCK_ID" => "4", "ACTIVE" => "Y");
$res = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter, false, false, array());
$arResult['type_restaurants'] = array();
while ($arField = $res->GetNext()) {
    $arResult['type_restaurants'][] = $arField;
}
/**
 * Set params
 */
$dirCachePach = $_SERVER["DOCUMENT_ROOT"] . 'bitrix/managed_cache/address/';
$fileForkPach = $_SERVER["DOCUMENT_ROOT"] . 'bitrix/modules/search_nearest_restaurant/admin/fork.php';

$dirPermissions = $arParams['DIR_PERMISSIONS'];        // Права на создаваемые папки
$filePermissions = $arParams['FILE_PERMISSIONS'];       // Права на создаваемые файлы
$timeLifeCache = $arParams['TIME_LIFE_CACHE'];        // Время жизни кеша


$address = rand(5, 10);

/**
 * Check directory
 */
if (!file_exists($dirCachePach))
    if (mkdir($dirCachePach))
        chmod($dirCachePach, $arParams['DIR_PERMISSIONS']);

/**
 * Check cache
 * If is no cache then create cache
 * If the cache is outdated then create new cache
 */
//$cacheBad = false;
//$cacheName = md5($address);
//if (file_exists($dirCachePach . $cacheName)){
//    if (time() - filemtime($dirCachePach . $cacheName) > $arParams['TIME_LIFE_CACHE']){
//        $cacheBad = true;
//    }
//}else{
//    $cacheBad = true;
//}
//if ($cacheBad){
//    exec("php $fileForkPach $address {$_SERVER['DOCUMENT_ROOT']}", $val);
//    $arResult = unserialize(file_get_contents($dirCachePach . $cacheName));
//    echo '<div align="left"><pre>';
//    print_r($arResult);
//    echo '</pre></div>';
//}else{
//    $arResult = unserialize(file_get_contents($dirCachePach . $cacheName));
//    echo '<div align="left"><pre>';
//    print_r($arResult);
//    echo '</pre></div>';
//}
    
/**
 * Путь к компоненту
 */
$arResult["PATH_COMPONENT"] = $this->GetPath();

$this->IncludeComponentTemplate();
?>
