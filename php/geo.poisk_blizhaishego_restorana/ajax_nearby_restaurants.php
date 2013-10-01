<?

/**
 * Подключим классы
 */
include_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/geo_pbr/admin/classes/general/router/includeAll.php";

/**
 * Подключение bitrix
 */
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

/**
 * Подключение модуля КЛАДР
 */
if (!CModule::IncludeModule("grain.kladr")) {
    die("grain.kladr module not installed");
    return;
}

/**
 * Подключение модуля Информационные блоки
 */
if (!CModule::IncludeModule("iblock")) {
    ShowError("iblock module not installed");
    return;
}

/**
 * Установка параметров
 */
//$dirCachePach = $_SERVER["DOCUMENT_ROOT"] . '/bitrix/managed_cache/address/';
//$forkPach = $_SERVER["DOCUMENT_ROOT"] . '/bitrix/modules/search_nearest_restaurant/admin/fork.php';

$dirPermissions = 0777;             // Права на создаваемые папки
$filePermissions = 0666;            // Права на создаваемые файлы
$timeLifeCache = '604800';            // Время жизни кеша

$addressStart = "Воронежская Улица 13стр2"; // Получаем адрес ресторана
$iBlockIdRestaurant = COption::GetOptionString("geo_pbr", "IdIBlockRestaurants");                    // ID инфоблока "Рестораны"

$obInSubwayRouter = new InSubwayRouter();

//$obInSubwayRouter->build($_GET['stationName'], $endStation);

/**
 * Получим свойства инфоблока "Рестораны"
 */
$arSelect = Array("IBLOCK_ID", 'ID', 'NAME', 'PROPERTY_address', 'PROPERTY_type_of_restaurant', 'PROPERTY_nearest_metro_stations', 'PROPERTY_arr_time_to_subway');
$arFilter = Array("IBLOCK_ID" => $iBlockIdRestaurant, "ACTIVE" => "Y", "PROPERTY_type_of_restaurant" => "{$_GET['type']}");
$res = CIBlockElement::GetList(Array("SORT" => "ASC"), $arFilter, false, false, $arSelect);
$arRestaurants = array();
while ($arField = $res->GetNext()) {
    $arRestaurants[] = $arField;
}

$arMinTimeAddressAddressRuter = array();
$arTimeAddressAddressRuter = array();
foreach ($arRestaurants as $keyRestaurant => $arRestaurant) {
    $minTimeInMetro = NULL;
    foreach ($arRestaurant["PROPERTY_NEAREST_METRO_STATIONS_VALUE"] as $keyMetro => $IDMetro) {
        $obMetro = CIBlockElement::GetByID($IDMetro);
        $arMetro = $obMetro->GetNext();
        $arTimeFromSubwayRuter = explode(",", $arRestaurant["PROPERTY_ARR_TIME_TO_SUBWAY_VALUE"]);
        if ($arMetro) {
            $arRestaurants[$keyRestaurant]["PROPERTY_NEAREST_METRO_STATIONS_ARRAY"][$keyMetro] = $arMetro;
            try {
                $t = $obInSubwayRouter->build($_GET['stationName'], cp1251_to_utf8($arMetro['NAME']));
            } catch (Exception $e) {
            }
            $arRestaurants[$keyRestaurant]["PROPERTY_NEAREST_METRO_STATIONS_ARRAY"][$keyMetro]['time'] = $t['time'];
            if ($minTimeInMetro === NULL) {

                $minTimeInMetro = $arRestaurants[$keyRestaurant]["PROPERTY_NEAREST_METRO_STATIONS_ARRAY"][$keyMetro];
                preg_match("/$IDMetro:([0-9].*)/", $arTimeFromSubwayRuter[$keyMetro], $matches);
                $timeFromSubwayRuter = $matches["1"];
                $arMinTimeAddressAddressRuter = array(
                    'AddressStart' => utf8_to_cp1251($_GET['addressStart']),
                    'AddressEnd' => $arRestaurant['PROPERTY_ADDRESS_VALUE'],
                    'SubwayStart' => utf8_to_cp1251($_GET['stationName']),
                    'SubwayEnd' => $arMetro['NAME'],
                    'District' => utf8_to_cp1251($_GET['district']),
                    'time' => (int) $_GET['duration'] + (int) $minTimeInMetro['time'] + (int) $timeFromSubwayRuter,
                    'timeToSubway' => (int) $_GET['duration'],
                    'timeInSubway' => (int) $minTimeInMetro['time'],
                    'timeFromSubway' => (int) $timeFromSubwayRuter
                );
            }elseif ($minTimeInMetro[$keyRestaurant]["PROPERTY_NEAREST_METRO_STATIONS_ARRAY"][$keyMetro]['time'] < $arRestaurants[$keyRestaurant]["PROPERTY_NEAREST_METRO_STATIONS_ARRAY"][$keyMetro]['time']) {

                $minTimeInMetro = $arRestaurants[$keyRestaurant]["PROPERTY_NEAREST_METRO_STATIONS_ARRAY"][$keyMetro];
                preg_match("/$IDMetro:([0-9].*)/", $arTimeFromSubwayRuter[$keyMetro], $matches);
                $timeFromSubwayRuter = $matches["1"];
                $arMinTimeAddressAddressRuter = array(
                    'AddressStart' => utf8_to_cp1251($_GET['addressStart']),
                    'AddressEnd' => $arRestaurant['PROPERTY_ADDRESS_VALUE'],
                    'SubwayStart' => utf8_to_cp1251($_GET['stationName']),
                    'SubwayEnd' => $arMetro['NAME'],
                    'District' => utf8_to_cp1251($_GET['district']),
                    'time' => (int) $_GET['duration'] + (int) $minTimeInMetro['time'] + (int) $timeFromSubwayRuter,
                    'timeToSubway' => (int) $_GET['duration'],
                    'timeInSubway' => (int) $minTimeInMetro['time'],
                    'timeFromSubway' => (int) $timeFromSubwayRuter
                );
            }
        }
    }
    $arTimeAddressAddressRuter[] = $arMinTimeAddressAddressRuter;

}
//echo '<div align="left"><pre>';
//print_r($minTimeInMetro);
//echo '</pre></div>';
foreach ($arTimeAddressAddressRuter as $key => $value){
    $arTimeAddressAddressRuter[$key]['AddressStart'] = cp1251_to_utf8($arTimeAddressAddressRuter[$key]['AddressStart']);
    $arTimeAddressAddressRuter[$key]['AddressEnd'] = cp1251_to_utf8($arTimeAddressAddressRuter[$key]['AddressEnd']);
    $arTimeAddressAddressRuter[$key]['SubwayStart'] = cp1251_to_utf8($arTimeAddressAddressRuter[$key]['SubwayStart']);
    $arTimeAddressAddressRuter[$key]['SubwayEnd'] = cp1251_to_utf8($arTimeAddressAddressRuter[$key]['SubwayEnd']);
    $arTimeAddressAddressRuter[$key]['District'] = cp1251_to_utf8($arTimeAddressAddressRuter[$key]['District']);
}

usort($arTimeAddressAddressRuter, "cmp");

echo json_encode($arTimeAddressAddressRuter);

//print_r($arRestaurants);

function cmp($a, $b)
{
    if ($a["time"] == $b["time"]) {
        return 0;
    }
    return ($a["time"] < $b["time"]) ? -1 : +1;
}

function cp1251_to_utf8($txt) {
    $in_arr = array(
        chr(208), chr(192), chr(193), chr(194),
        chr(195), chr(196), chr(197), chr(168),
        chr(198), chr(199), chr(200), chr(201),
        chr(202), chr(203), chr(204), chr(205),
        chr(206), chr(207), chr(209), chr(210),
        chr(211), chr(212), chr(213), chr(214),
        chr(215), chr(216), chr(217), chr(218),
        chr(219), chr(220), chr(221), chr(222),
        chr(223), chr(224), chr(225), chr(226),
        chr(227), chr(228), chr(229), chr(184),
        chr(230), chr(231), chr(232), chr(233),
        chr(234), chr(235), chr(236), chr(237),
        chr(238), chr(239), chr(240), chr(241),
        chr(242), chr(243), chr(244), chr(245),
        chr(246), chr(247), chr(248), chr(249),
        chr(250), chr(251), chr(252), chr(253),
        chr(254), chr(255)
    );

    $out_arr = array(
        chr(208) . chr(160), chr(208) . chr(144), chr(208) . chr(145),
        chr(208) . chr(146), chr(208) . chr(147), chr(208) . chr(148),
        chr(208) . chr(149), chr(208) . chr(129), chr(208) . chr(150),
        chr(208) . chr(151), chr(208) . chr(152), chr(208) . chr(153),
        chr(208) . chr(154), chr(208) . chr(155), chr(208) . chr(156),
        chr(208) . chr(157), chr(208) . chr(158), chr(208) . chr(159),
        chr(208) . chr(161), chr(208) . chr(162), chr(208) . chr(163),
        chr(208) . chr(164), chr(208) . chr(165), chr(208) . chr(166),
        chr(208) . chr(167), chr(208) . chr(168), chr(208) . chr(169),
        chr(208) . chr(170), chr(208) . chr(171), chr(208) . chr(172),
        chr(208) . chr(173), chr(208) . chr(174), chr(208) . chr(175),
        chr(208) . chr(176), chr(208) . chr(177), chr(208) . chr(178),
        chr(208) . chr(179), chr(208) . chr(180), chr(208) . chr(181),
        chr(209) . chr(145), chr(208) . chr(182), chr(208) . chr(183),
        chr(208) . chr(184), chr(208) . chr(185), chr(208) . chr(186),
        chr(208) . chr(187), chr(208) . chr(188), chr(208) . chr(189),
        chr(208) . chr(190), chr(208) . chr(191), chr(209) . chr(128),
        chr(209) . chr(129), chr(209) . chr(130), chr(209) . chr(131),
        chr(209) . chr(132), chr(209) . chr(133), chr(209) . chr(134),
        chr(209) . chr(135), chr(209) . chr(136), chr(209) . chr(137),
        chr(209) . chr(138), chr(209) . chr(139), chr(209) . chr(140),
        chr(209) . chr(141), chr(209) . chr(142), chr(209) . chr(143)
    );

    $txt = str_replace($in_arr, $out_arr, $txt);
    return $txt;
}

function utf8_to_cp1251($txt) {
    $in_arr = array(
        chr(208), chr(192), chr(193), chr(194),
        chr(195), chr(196), chr(197), chr(168),
        chr(198), chr(199), chr(200), chr(201),
        chr(202), chr(203), chr(204), chr(205),
        chr(206), chr(207), chr(209), chr(210),
        chr(211), chr(212), chr(213), chr(214),
        chr(215), chr(216), chr(217), chr(218),
        chr(219), chr(220), chr(221), chr(222),
        chr(223), chr(224), chr(225), chr(226),
        chr(227), chr(228), chr(229), chr(184),
        chr(230), chr(231), chr(232), chr(233),
        chr(234), chr(235), chr(236), chr(237),
        chr(238), chr(239), chr(240), chr(241),
        chr(242), chr(243), chr(244), chr(245),
        chr(246), chr(247), chr(248), chr(249),
        chr(250), chr(251), chr(252), chr(253),
        chr(254), chr(255)
    );

    $out_arr = array(
        chr(208) . chr(160), chr(208) . chr(144), chr(208) . chr(145),
        chr(208) . chr(146), chr(208) . chr(147), chr(208) . chr(148),
        chr(208) . chr(149), chr(208) . chr(129), chr(208) . chr(150),
        chr(208) . chr(151), chr(208) . chr(152), chr(208) . chr(153),
        chr(208) . chr(154), chr(208) . chr(155), chr(208) . chr(156),
        chr(208) . chr(157), chr(208) . chr(158), chr(208) . chr(159),
        chr(208) . chr(161), chr(208) . chr(162), chr(208) . chr(163),
        chr(208) . chr(164), chr(208) . chr(165), chr(208) . chr(166),
        chr(208) . chr(167), chr(208) . chr(168), chr(208) . chr(169),
        chr(208) . chr(170), chr(208) . chr(171), chr(208) . chr(172),
        chr(208) . chr(173), chr(208) . chr(174), chr(208) . chr(175),
        chr(208) . chr(176), chr(208) . chr(177), chr(208) . chr(178),
        chr(208) . chr(179), chr(208) . chr(180), chr(208) . chr(181),
        chr(209) . chr(145), chr(208) . chr(182), chr(208) . chr(183),
        chr(208) . chr(184), chr(208) . chr(185), chr(208) . chr(186),
        chr(208) . chr(187), chr(208) . chr(188), chr(208) . chr(189),
        chr(208) . chr(190), chr(208) . chr(191), chr(209) . chr(128),
        chr(209) . chr(129), chr(209) . chr(130), chr(209) . chr(131),
        chr(209) . chr(132), chr(209) . chr(133), chr(209) . chr(134),
        chr(209) . chr(135), chr(209) . chr(136), chr(209) . chr(137),
        chr(209) . chr(138), chr(209) . chr(139), chr(209) . chr(140),
        chr(209) . chr(141), chr(209) . chr(142), chr(209) . chr(143)
    );

    $txt = str_replace($out_arr, $in_arr, $txt);
    return $txt;
}

?>
