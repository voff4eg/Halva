<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
/* * *****************************************************************************
 * Код можно менять, но с ОСТОРОЖНОСТЬЮ!
 * **************************************************************************** */
?>
<script type="text/javascript">
    var nameSpaceR<?= $arParams["PREFIX"] ?> = {
        getNearbyRestaurants: function(){
            /**
             * Получим ближайшие рестораны
             */
            var numberHome = nameSpace<?= $arParams["PREFIX"] ?>.numberHome;
            var streetName = nameSpace<?= $arParams["PREFIX"] ?>.streetName;
            var stationName = nameSpace<?= $arParams["PREFIX"] ?>.stationName;
            var duration = nameSpace<?= $arParams["PREFIX"] ?>.duration;
            var district = nameSpace<?= $arParams["PREFIX"] ?>.district;
        
            $.get("<?= $arResult["PATH_COMPONENT"] . "/ajax_nearby_restaurants.php" ?>",
            {
                addressStart: streetName + " " + numberHome,
                stationName: stationName,
                duration: duration, // Время от клиента до метро
                district: district, // Название округа
                type: document.getElementById('typeR<?= $arParams["PREFIX"] ?>').value // Тип ресторана
            },
            function(data){
                var log = document.getElementById('log<?= $arParams["PREFIX"] ?>');
                log.innerHTML = '';
                $.each(data, function(index, value){
                    log.innerHTML += "Адрес ресторана: " + value.AddressEnd + "<br />";
                    log.innerHTML += "Округ: " + value.District + "<br />";
                    log.innerHTML += "Время: " + (value.time/60).toFixed(0) + " мин" + "<br />";
                });
            },
            "json");
        }
    };
</script>

<table class="ui-widget">
    <tr>
        <td align="right">Тип ресторана:</td>
        <td>
            <select id="typeR<?= $arParams["PREFIX"] ?>" name="type">
                <option selected value="">Любой</option>
                <? foreach ($arResult['type_restaurants'] as $arType) { ?>
                    <option value="<?= $arType['ID'] ?>"><?= $arType['NAME'] ?></option>
                <? } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td></td>
        <td align="right">
            <input type="submit" value="Поиск" name="search" onclick="nameSpaceR<?= $arParams["PREFIX"] ?>.getNearbyRestaurants();">
        </td>
    </tr>
</table>

<div class="ui-widget" style="margin-top:2em; font-family:Arial">
    Ближайшие рестораны:
    <div id="log<?= $arParams["PREFIX"] ?>" style="height: 200px; width: 500px; overflow: auto; text-align: left;" class="ui-widget-content"></div>
</div>
<p><h4>Поиск ближайшего ресторана</h4></p>
