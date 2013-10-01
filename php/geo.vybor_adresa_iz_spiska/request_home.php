<?

/**
 * Подключение cms bitrix
 */
require_once($_SERVER['DOCUMENT_ROOT'] . "/bitrix/modules/main/include/prolog_before.php");

if (!CModule::IncludeModule("grain.kladr")){
    ShowError("grain.kladr module not installed");
    return;
}

/**
 * Получим данные от клиета
 */
$streetCode = htmlspecialchars($_GET["streetCode"]); // Код улицы
$numberHome = htmlspecialchars($_GET["numberHome"]); // Номер дома

/**
 * Получим коды домов по коду улицы
 */
$arResult = SKladrEntry::SearchZip(
                $streetCode, Array("SORT" => "ASC", "NAME" => "ASC"), Array("ACTIVE" => "Y"), "ASC", true, false
);

/**
 * Получим массив номеров домов
 */
$arHomeToJSON = array();
foreach ($arResult["REFERENCE"] as $key => $value){
    if ($value == "")
        continue;
    $stringHome = SKladr::GetZip($arResult["REFERENCE_ID"][$key]);

    $arHome = explode(",", $stringHome["NAME"]);

    foreach ($arHome as $home){
        $arHomeToJSON["home"][] = array(
            "code" => cp1251_to_utf8($arResult["REFERENCE_ID"][$key]),
            "home" => cp1251_to_utf8($home),
            "ZIP" => cp1251_to_utf8($stringHome["ZIP"]),
            "SOCR" => cp1251_to_utf8($stringHome["SOCR"])
        );
    }
}

/**
 * Отсортируем массив по полю home в порядке возростания
 */
usort($arHomeToJSON["home"], "cmp");


//echo '<div align="left"><pre>'; print_r($arHomeToJSON);
//echo '</pre></div>';

/**
 * Фильтруем массив
 */
$arHomeToJSON["home"] = array_filter($arHomeToJSON["home"], "home_filter");


//echo '<div align="left"><pre>'; print_r($arHomeToJSON);
//echo '</pre></div>';

/**
 * Преобразуем в JSON
 */
echo json_encode($arHomeToJSON);

function home_filter($var)
{
    global $numberHome;
    if (preg_match("/^" . $numberHome . ".*/", $var["home"]))
        return $var;
}

function cmp($a, $b)
{
    return strcmp($a["home"], $b["home"]);
}

function cp1251_to_utf8($txt)
{
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

function utf8_to_cp1251($txt)
{
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
