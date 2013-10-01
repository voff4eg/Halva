<?php
ini_set('memory_limit', '4024M');
header("Content-Type: text/html; charset=UTF-8");

global $user_agents;
$user_agents = array(
    'Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.12) Gecko/20070731 Ubuntu/dapper-security Firefox/1.5.0.12' => 5,
    'Opera/9.25 (Windows NT 5.1; U; ru)' => 20,
    'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US) AppleWebKit/532.5 (KHTML, like Gecko) Chrome/4.1.249.1064 Safari/532.5' => 50,
    'Mozilla/5.0 (Windows; U; Windows NT 5.1; it; rv:1.8.1.11) Gecko/20071127 Firefox/2.0.0.11' => 100,
);
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

function dnl2array($domnodelist) {
    $return = array();
    for ($i = 0; $i < $domnodelist->length; ++$i) {
        $return[] = $domnodelist->item($i);
    }
    return $return;
}


$rnd = rand(1,100);

function getRandomHeader($rnd){
	global $user_agents;
	foreach($user_agents as $key => $variant){
		if($variant >= $rnd){
			return $key;
		}
	}
}
//echo getRandomHeader($rnd);
$html = array();
//Настройки
$ch = curl_init(); // Создаём объект-страницу с помощью cURL.
 // Указываем откуда спарсить содержимое в наш объект.
curl_setopt($ch, CURLOPT_HEADER, 0); //Включать или не включать header 

 // Прикинемся браузером.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //Возвращать трансфер или печатать напрямую
curl_setopt($ch, CURLOPT_COOKIEFILE, "cookies.txt"); //С некоторого времени yandex перестал отдавать выдачу без печеньев
curl_setopt($ch, CURLOPT_COOKIEJAR, "cookies.txt");   //поэтому cookie у нас тоже будет
//curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:8118");
for($i=1; $i <=9; $i++){
	//$i = 1;echo $i."<br>";
	curl_setopt($ch, CURLOPT_USERAGENT, getRandomHeader($rnd));
	$url = "http://wifi4free.ru/msk/hotspots/page".$i."/";
	curl_setopt($ch, CURLOPT_URL, $url);
	$html[] = curl_exec($ch);
}
 // Получаем страницу в виде строки.
curl_close($ch); // Удаляем объект-страницу, исходный код страницы в $result.
//echo "<pre>";print_r($html);echo "</pre>";

if(!empty($html)){
	//echo "<pre>";print_r($html);echo "</pre>";die;
	$Urls = array();
	$Places = array();
	$Addresses = array();
	$WFPoints = array();
	//$html = CP1251toUTF8($html);
	$dom = new DOMDocument();
	foreach($html as $key => $code){
		$dom->loadHTML($code);
		$dom->preserveWhiteSpace = false;
		$xpath = new DOMXPath($dom);
		//$tags = $xpath->query('//table[@class = "hotspots-table"]//tr');		
		//foreach ($tags as $tag) {
			//echo $tag->nodeValue."<br>";
			//if(strpos(utf8_decode($tag->nodeValue),"yabs.yandex.ru/count/") !== false){
				//$Urls[] = utf8_decode($tag->nodeValue);
			$dom->loadHTML($tag);
			$xpath = new DOMXPath($dom);
			$ads = $xpath->query('//div[@class = "address"]');
			$pls = $xpath->query('//div[@class = "place"]');
			$pls = dnl2array($pls);
			$Addresses = array();
			foreach($ads as $j => $ad){
				//echo $tg->nodeValue."<br>";
				//$pl = objectsIntoArray($pls[ $j ]);
				//echo $pls[ $j ]->nodeValue;die;						
				$Addresses[] = array("address" => trim(strip_tags($ad->nodeValue)), "place" => trim(strip_tags($pls[$j]->nodeValue)));
			}			
			
			// foreach($pls as $j => $pl){
				// $Addresses[ $j ]["place"] = trim(strip_tags($pl->nodeValue));
				//$Places[ $key ][] = trim(strip_tags($pl->nodeValue));
			// }
			$WFPoints[ $key ] = $Addresses;			
			//}
		//}
	}
	// echo "<pre>";print_r($WFPoints);echo "</pre>";
	/*if(!empty($Addresses[ $i ])){
		if(is_writable('adresses.txt')){
			//file_put_contents('adresses.txt', print_r($Addresses, true));
			foreach($Addresses[ $i ] as $address){
				file_put_contents('adresses.txt',strip_tags(trim($address))."\n",FILE_APPEND);
			}
			//echo "<pre>";print_r($Addresses[ $i ]);echo "</pre>";
		}else{
			echo "нет прав на запись в adresses.txt";
		}
	}else{
		echo "Пустой массив Adresses";
	}
	if(!empty($Places[ $i ])){
		if(is_writable('places.txt')){
			//file_put_contents('places.txt', print_r($Places, true));
			foreach($Places[ $i ] as $place){
				file_put_contents('places.txt',strip_tags(trim($place))."\n",FILE_APPEND);
			}
			//echo "<pre>";print_r($Places[ $i ]);echo "</pre>";
		}else{
			echo "нет прав на запись в places.txt"; 
		}
	}else{
		echo "Пустой массив Places";
	}*/
	if(!empty($WFPoints)){
		if(is_writable('wfpoints.txt')){
			//file_put_contents('places.txt', print_r($Places, true));
			file_put_contents('wfpoints.txt',json_encode($WFPoints)."\n",FILE_APPEND);
			//echo "<pre>";print_r($Places[ $i ]);echo "</pre>";
		}else{
			echo "нет прав на запись в wfpoints.txt"; 
		}
	}else{
		echo "Пустой массив wfpoints";
	}
}

/*$regulyar='#<title>(?s)(.*)</title>#'; //Читаем title
$rezregulyar = preg_match_all($regulyar, $result, $out); // Выдергиваем вхождение в строку $result регулярного выражения $regulyar, результаты отправляем в массив $out.
	if($rezregulyar){     //Если хоть одно вхождение нашлось.
 		if(preg_match_all('#[а-я]*?\b (.*)страни#', $out[1][0], $tit))	{  //Если имеется нужная строка
 
 		$tit1=str_replace('млн', '000000', $tit[1][0]); //Заменяем "млн" шестью нулями
 		$tit1=str_replace('тыс.', '000', $tit1);        //Заменяем "тыс." тремя нулями
 		$tit1=str_replace(' ', '', $tit1);              //Удаляем пробелы
 		$tit1=str_replace('&nbsp;', '', $tit1);         //Удаляем пробелы
 		echo "В индексе ".$tit1." страниц";             //Выводим результат
 
 	  }      
 
 	  if(preg_match_all('#ничего не найдено#', $out[1][0], $tit))	{  //Если "ничего не найдено"
 	     echo "В индексе 0 страниц";   //Выводим результат
 	    }    
    } //if($ismatches)      
 
	if(preg_match_all('#The document has moved#', $result, $tit))	{ //Если попалась капча
 	  echo "Нарвались на капчу"; //Выводим результат
	}*/    
?>