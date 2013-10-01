<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); ?>
<script type="text/javascript">
    var nameSpaceO<?= $arParams["PREFIX"] ?> = {
    /**
     * ������� ����� �������� ����� ���������� � ��������
     */
    getTimeDelivery: function(){
        var city = nameSpace<?= $arParams["PREFIX"] ?>.city;
        var streetName = nameSpace<?= $arParams["PREFIX"] ?>.streetName;
        var numberHome = nameSpace<?= $arParams["PREFIX"] ?>.numberHome;
        // ������ ���������
        var restaurant = document.getElementById('restaurant<?= $arParams["PREFIX"] ?>');
        // ������ ���� ��������
        var delivery_type = document.getElementById('delivery_type<?= $arParams["PREFIX"] ?>');

//        if (streetName.length <= 0){
//            alert('�������� �����');
//            return false;
//        }
//        if (numberHome.length <= 0){
//            alert('�������� ����� ����');
//            return false;
//        }
//        if (restaurant.value <= 0){
//            alert('�������� ��������');
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
            document.getElementById('route<?= $arParams["PREFIX"] ?>').innerHTML = "����� �������� " + data.time + ' ���. ��� ' + (data.time/60).toFixed(0) + " ���.<br>" + data.instruction + ' ' + data.route;
        },
        "json");
    }
    };
</script>

<p><h4>����������� ������� �������� ����� ���������� � �������</h4></p>
<br>
<table class="ui-widget">
    <tr>
        <td align="right">��������:</td>
        <td>
            <select name="restaurant" id="restaurant<?= $arParams["PREFIX"] ?>">
                <option selected value="0">�������� ��������</option>
                <? foreach ($arResult['RESTAURANTS'] as $Restaurant): ?>
                    <option value="<?= $Restaurant['ID'] ?>"><?= $Restaurant['NAME'] ?></option>
                <? endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td align="right">��� ��������:</td>
        <td>
            <select name="delivery_type" id="delivery_type<?= $arParams["PREFIX"] ?>">
                <option selected value="any">�����</option>
                <option value="walking">������</option>
                <option value="driving">�� ����</option>
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