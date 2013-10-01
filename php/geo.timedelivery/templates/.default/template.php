<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); ?>
<script type="text/javascript">
    var nameSpaceO<?= $arParams["PREFIX"] ?> = {
    /**
     * Получим время движения между рестораном и клиентом
     */
    getTimeDelivery: function(){
        var city = nameSpace<?= $arParams["PREFIX"] ?>.city;
        var streetName = nameSpace<?= $arParams["PREFIX"] ?>.streetName;
        var numberHome = nameSpace<?= $arParams["PREFIX"] ?>.numberHome;
        // Объект ресторана
        var restaurant = document.getElementById('restaurant<?= $arParams["PREFIX"] ?>');
        // Объект типа доставки
        var delivery_type = document.getElementById('delivery_type<?= $arParams["PREFIX"] ?>');

//        if (streetName.length <= 0){
//            alert('Выберите улицу');
//            return false;
//        }
//        if (numberHome.length <= 0){
//            alert('Выберите номер дома');
//            return false;
//        }
//        if (restaurant.value <= 0){
//            alert('Выберите ресторан');
//            return false;
//        }
        $.get("<?= $arResult["PATH_COMPONENT"] . "/ajax_timedelivery.php" ?>",
        {
            addressStart: city + ' ' + streetName + ' ' + numberHome,
            restaurant: restaurant.value,
            deliveryType: delivery_type.value
        },
        function(data){
            //            alert(data.time + data.instruction + ' ' + data.route);
            document.getElementById('route<?= $arParams["PREFIX"] ?>').innerHTML = "Время движения " + data.time + ' сек. или ' + (data.time/60).toFixed(0) + " мин.<br>" + data.instruction + ' ' + data.route;
        },
        "json");
    }
    };
</script>

<p><h4>Определение времени доставки между рестораном и адресом</h4></p>
<br>
<table class="ui-widget">
    <tr>
        <td align="right">Ресторан:</td>
        <td>
            <select name="restaurant" id="restaurant<?= $arParams["PREFIX"] ?>">
                <option selected value="0">Выберите ресторан</option>
                <? foreach ($arResult['RESTAURANTS'] as $Restaurant): ?>
                    <option value="<?= $Restaurant['ID'] ?>"><?= $Restaurant['NAME'] ?></option>
                <? endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td align="right">Тип доставки:</td>
        <td>
            <select name="delivery_type" id="delivery_type<?= $arParams["PREFIX"] ?>">
                <option selected value="any">Любой</option>
                <option value="walking">Пешком</option>
                <option value="driving">На авто</option>
            </select>
        </td>
    </tr>
    <tr>
        <td></td>
        <td align="right">
            <input type="submit" value="OK" onclick="nameSpaceO<?= $arParams["PREFIX"] ?>.getTimeDelivery();">
        </td>
    </tr>
</table>
<div id="route<?= $arParams["PREFIX"] ?>" style="text-align: left;"></div>