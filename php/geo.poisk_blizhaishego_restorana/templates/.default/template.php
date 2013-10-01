<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
/* * *****************************************************************************
 * ��� ����� ������, �� � �������������!
 * **************************************************************************** */
?>
<script type="text/javascript">
    var nameSpaceR<?= $arParams["PREFIX"] ?> = {
        getNearbyRestaurants: function(){
            /**
             * ������� ��������� ���������
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
                duration: duration, // ����� �� ������� �� �����
                district: district, // �������� ������
                type: document.getElementById('typeR<?= $arParams["PREFIX"] ?>').value // ��� ���������
            },
            function(data){
                var log = document.getElementById('log<?= $arParams["PREFIX"] ?>');
                log.innerHTML = '';
                $.each(data, function(index, value){
                    log.innerHTML += "����� ���������: " + value.AddressEnd + "<br />";
                    log.innerHTML += "�����: " + value.District + "<br />";
                    log.innerHTML += "�����: " + (value.time/60).toFixed(0) + " ���" + "<br />";
                });
            },
            "json");
        }
    };
</script>

<table class="ui-widget">
    <tr>
        <td align="right">��� ���������:</td>
        <td>
            <select id="typeR<?= $arParams["PREFIX"] ?>" name="type">
                <option selected value="">�����</option>
                <? foreach ($arResult['type_restaurants'] as $arType) { ?>
                    <option value="<?= $arType['ID'] ?>"><?= $arType['NAME'] ?></option>
                <? } ?>
            </select>
        </td>
    </tr>
    <tr>
        <td></td>
        <td align="right">
            <input type="submit" value="�����" name="search" onclick="nameSpaceR<?= $arParams["PREFIX"] ?>.getNearbyRestaurants();">
        </td>
    </tr>
</table>

<div class="ui-widget" style="margin-top:2em; font-family:Arial">
    ��������� ���������:
    <div id="log<?= $arParams["PREFIX"] ?>" style="height: 200px; width: 500px; overflow: auto; text-align: left;" class="ui-widget-content"></div>
</div>
<p><h4>����� ���������� ���������</h4></p>
