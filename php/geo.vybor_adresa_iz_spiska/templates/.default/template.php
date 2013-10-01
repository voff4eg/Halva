<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();
/* * *****************************************************************************
 * Код который идет дальше не в коем случае не менять
 * **************************************************************************** */
?>
<script type="text/javascript"
        src=<?= $arResult['PATH_COMPONENT'] . "/jquery-1.6.2.min.js" ?> >
</script>

<script type="text/javascript"
        src=<?= $arResult['PATH_COMPONENT'] . "/jquery-ui-1.8.14.custom.min.js" ?> >
</script>

<script type="text/javascript"
        src=<?= $arResult['PATH_COMPONENT'] . "/NearestSubway.js" ?> >
</script>
<? $APPLICATION->AddHeadScript("http://maps.googleapis.com/maps/api/js?&language=ru&sensor=false&libraries=places"); ?>

<script type="text/javascript">
    var nameSpace<?= $arParams["PREFIX"] ?> = {
        run: function(){
            var that = this;
            that.streetCode = "";
            that.streetName = "";
            that.city = "Москва";
            that.streetType = "Улица";
            that.numberHome = '';
            that.stationName = '';
            that.duration = ''; // Время от клиента до метро
            that.district = ''; // Округ
            

            $(function(){
                /**
                 * Получим список улиц
                 */
                $("#street<?= $arParams["PREFIX"] ?>").autocomplete({
                    source: function( request, response ) {
                        $.ajax({
                            url: '<?= $arResult['PATH_COMPONENT'] ?>' + '/request_address.php',
                            dataType: 'json',
                            data: {
                                street: request.term
                            },
                            success: function( data ) {
                                response( $.map( data.streets, function( item ) {
                                    return {
                                        label: item.name,
                                        code : item.code
                                    };
                                }));
                            }
                        });
                    },
                    /**
                     * Сработает после выбора
                     */
                    select: function(event, ui) {
                        $("#number_home<?= $arParams["PREFIX"] ?>").focus(); // Установка фокуса
                        that.streetCode = ui.item.code;
                        that.streetName = ui.item.label;
                    },
                    delay: 3, // Задержка
                    minLength: 3 // Минимальная длина строки прежде чем сработает функция
                });

                /**
                 * Получим список домов
                 */
                $("#number_home<?= $arParams["PREFIX"] ?>").autocomplete({
                    source: function( request, response ) {
                        $.ajax({
                            url: '<?= $arResult['PATH_COMPONENT'] ?>' + '/request_home.php',
                            dataType: 'json',
                            data: {
                                numberHome: request.term,
                                streetCode: that.streetCode
                            },
                            success: function( data ) {
                                response( $.map( data.home, function( item ) {
                                    return {
                                        label: item.home, //
                                        code : item.code,
                                        ZIP  : item.ZIP,
                                        SOCR : item.SOCR
                                    };
                                }));
                            }
                        });
                    },
                    /**
                     * Сработает после выбора
                     */
                    select: function(event, ui) {
                        that.numberHome = ui.item.label;
                        yeho(that.city + ' ' + that.streetName + ' ' + that.numberHome);

                        /**
                         * Бесполезный код, но требуют
                         */
                        var o = document.getElementById("full_address<?= $arParams["PREFIX"] ?>");
                        o.value = ui.item.ZIP + ", " + that.city + ", " + that.streetType + ", " + that.streetName + ", " + ui.item.label;
                    },
                    delay: 3, // Задержка
                    minLength: 1 // Минимальная длина строки прежде чем сработает функция
                });
            });

            /**
             * Получим название округа, ближайшую станцию метро
             * время от клиента до метро
             */
            function yeho(addr) {
                // Создаем div элемент
                var parent = document.getElementsByTagName('BODY')[0];
                var newDiv = document.createElement('div');
                newDiv.id = 'map<?= $arParams["PREFIX"] ?>';
                newDiv.style.display = 'none';
                parent.appendChild(newDiv);

                var opts = {
                    center: new google.maps.LatLng(55.6378676,37.6621927),
                    zoom: 17,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                var map = new google.maps.Map(document.getElementById('map<?= $arParams["PREFIX"] ?>'), opts);

                var ns = new NearestSubway (addr,
                function (latlng, isOkay, errmessage) {
                    if (isOkay === true){
                        that.stationName = latlng.stationName;
                        that.duration = latlng.duration;
                        that.district = latlng.district;
                    }else{
                        that.stationName = "Станция метро не найдена";
                        that.duration = 0;
                        that.district = ""; 
                    }
        
                    document.getElementById('metro_station<?= $arParams["PREFIX"] ?>').value = that.stationName;
                    document.getElementById('district<?= $arParams["PREFIX"] ?>').value = that.district;
                },
                map,
                'walking'
            );

                ns.run();
            };
        }
    };
    nameSpace<?= $arParams["PREFIX"] ?>.run();
</script>

<?
/* * *****************************************************************************
 * Код который идет дальше можно менять, но с ОСТОРОЖНОСТЬЮ!
 * **************************************************************************** */
?>
<table class="ui-widget">
    <tr>
        <td align="right"><label for="street">Улица:&nbsp;</label></td>
        <td><input id="street<?= $arParams["PREFIX"] ?>" type="text" /></td>
    </tr>
    <tr>
        <td align="right"><label for="number_home">Номер дома:&nbsp;</label></td>
        <td><input id="number_home<?= $arParams["PREFIX"] ?>" type="text" /></td>
    </tr>
    <tr>
        <td align="right"><label for="metro_station">Ближайшая станция метро:&nbsp;</label></td>
        <td><input id="metro_station<?= $arParams["PREFIX"] ?>" type="text" /></td>
    </tr>
    <tr>
        <td align="right"><label for="district">Округ:&nbsp;</label></td>
        <td><input id="district<?= $arParams["PREFIX"] ?>" type="text" /></td>
    </tr>
</table>
<input type="hidden" id="full_address<?= $arParams["PREFIX"] ?>">