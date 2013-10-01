<?php
	// Определяем положение с помощью geoip
	// Передаем параметром IP адрес
	$info = json_decode(file_get_contents('http://geoip.pidgets.com/?ip='.$_SERVER['REMOTE_ADDR'].'&format=json'),true);
	// В случае неудачи получаем false
	// Если false, то рассматриваем данные поступившие из ajax запроса
	if(!$info){
		$info = $_POST;
	}
	// Если город определился переводим его через yandex translate
	if($info['city']){
		$location = file_get_contents('http://translate.yandex.ru/tr.json/translate?lang=en-ru&text='.urlencode($info['city']));
		if($location !== '""') {
			$location = str_replace('"', "", $location);
		}
	} else {
		$location = false;
	}
	
	// Результат
	$response  = array('error'  => 0,
					   'lat'	=> $info['latitude'],
					   'lng' 	=> $info['longitude'],
						'city' 	=> $location);

    header('Content-type: application/json');
	echo json_encode($response);
	die();