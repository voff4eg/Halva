<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->RestartBuffer();

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
	return $arrXml["GeoObjectCollection"]["featureMember"]["0"]["GeoObject"]["Point"]["pos"];
}

global $DB,$USER;

// Построчное чтение файла
$filename = $_SERVER["DOCUMENT_ROOT"]."/events.txt";
$arUsers = array();
$keys = array("region","name","city","date","url");
if(is_readable($filename)){
	$handle = fopen ($filename, "r");
	while (!feof ($handle)) {
		$buffer = fgets($handle, 4096);
		if(strlen(trim($buffer)) > 0){
			$user = array();
			$user = explode(";", $buffer);
			$user = array_combine($keys, $user);
			$user["city"] = str_replace(" - ", "-",$user["city"]);
			$user["region"] = str_replace(" - ", "-",$user["region"]);
			$user["url"] = trim($user["url"]);
			if($date = strtotime($user["date"])){
				$user["date_from"] = $date;
				$user["date_to"] = $date;
			}else{
				$ar = explode(" - ",$user["date"]);
				$user["date_from"] = strtotime($ar[0]);
				$user["date_to"] = strtotime($ar[1]);
			}
			$arUsers[] = $user;
		}
	}
	fclose ($handle);
}else{
	echo "Не доступен для чтения";
}

if(!empty($arUsers) && CModule::IncludeModule("iblock")){
	foreach($arUsers as $user){
		$el = new CIBlockElement;

		$PROP = array();
		$PROP[33] = $user["city"];
		$PROP[32] = $user["region"];
		
		if(strlen(trim($user["city"])) > 0){
			$PROP[34] = getCoord($user["city"]);
		}elseif(strlen(trim($user["region"])) > 0){
			$PROP[34] = getCoord($user["region"]);
		}
		
		$arLoadProductArray = Array(
		  "MODIFIED_BY"    => $USER->GetID(), // элемент изменен текущим пользователем
		  "DATE_ACTIVE_FROM" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), $user["date_from"]),
		  "DATE_ACTIVE_TO" => date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL")), $user["date_to"]),
		  "IBLOCK_SECTION_ID" => false,          // элемент лежит в корне раздела
		  "IBLOCK_ID"      => 19,
		  "PROPERTY_VALUES"=> $PROP,
		  "NAME"           => $user["name"],
		  "ACTIVE"         => "Y",            // активен
		  "PREVIEW_TEXT"   => "",
		  "DETAIL_TEXT"    => trim($user["url"]),
		);

		if($PRODUCT_ID = $el->Add($arLoadProductArray))
		  echo "New ID: ".$PRODUCT_ID;
		else
		  echo "Error: ".$el->LAST_ERROR;
		  
		  sleep("1");
	}
}
?>
