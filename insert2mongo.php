<?
header("Content-Type: text/html; charset=UTF-8");
ini_set('memory_limit', '6024M');

set_time_limit(0);


function objectsIntoArray($arrObjData, $arrSkipIndices = array())
{
    $arrData = array();
   
    // if input is object, convert into array
    if (is_object($arrObjData)) {
        $arrObjData = get_object_vars($arrObjData);
    }
   
    if (is_array($arrObjData)) {
        foreach ($arrObjData as $index => $value) {
            if (is_object($value) || is_array($value)) {
                $value = objectsIntoArray($value, $arrSkipIndices); // recursive call
            }
            if (in_array($index, $arrSkipIndices)) {
                continue;
            }
            $arrData[$index] = $value;
        }
    }
    return $arrData;
}

function getCoord($geocode){
	$pageurl = "http://geocode-maps.yandex.ru/1.x/?geocode=".$geocode;
	$xmlStr = file_get_contents($pageurl);
	$xmlObj = simplexml_load_string($xmlStr);
	$arrXml = objectsIntoArray($xmlObj);
	return $arrXml["GeoObjectCollection"]["featureMember"]["GeoObject"]["Point"]["pos"];
}

$filename = "wfpoints.txt";
//$arAddresses = array();
$arPlaces = array();

if(is_readable($filename)){
	$handle = fopen ($filename, "r");
	while (!feof ($handle)) {
		$buffer = fgets($handle);
    if(strlen(trim($buffer))){
      $arAddresses = json_decode($buffer);
    }
    //echo "@<pre>";print_r(json_decode($buffer));echo "</pre>@";die;
		//$arAddresses = $buffer;
	}
	fclose ($handle);
}else{
	echo "Не доступен для чтения";
}

//echo count($arAddresses);die;

$All = array();
foreach($arAddresses as $key => $Adresses){
  //$c = count($Adresses);
  $cnt = count($arAddresses[$key]);
  for($i = 0; $i <= $cnt; $i++){
    $coord = getCoord(urlencode("Москва ".$arAddresses[0][ $i ]->{'address'}));
    if(strlen($coord) > 0){
      $c = explode(" ",$coord);
      $All[] = array(
        "address" => "Москва ".$arAddresses[0][ $i ]->{'address'},
        "place" => $arAddresses[0][ $i ]->{'place'},
        "cx" => $c[1],
        "cy" => $c[0],
      );  
    }
  }
}

echo "<pre>";print_r($All);echo "</pre>";
if(!empty($All)){

$mongo = new Mongo(); // соединяемся с сервером
$db = $mongo->halvadb; // выбираем базу данных
$collection = $db->items;

foreach($All as $a){
  $collection->insert(
              array(
                 'name' => $a["place"],
                 'address' => $a["address"],
                 'type' => 'wifi',
                 'icon' => '/images/icons/wifi.png',
                 'create_date' => time(),
                 'update_date' => time(),
                 'description' => '',
                 'cx' => $a["cx"], //Координаты на карте
                 'cy' => $a["cy"],
                 'author' => 'wifi4free'),
              array("safe" => 1)
  );
}
 // данный параметро сообщает MongoDB проследить за успешностью вставки, обычно этот параметр отключен
}

?>