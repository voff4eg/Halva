<?header("Content-Type: text/html; charset=UTF-8");
ini_set('memory_limit', '6024M');

set_time_limit(0);

$mongo = new Mongo(); // соединяемся с сервером
$db = $mongo->halvadb; // выбираем базу данных
$collection = $db->items;
$cursor = $collection->find();
$WiFi = array();
foreach ($cursor as $obj) {
 //  $ar = array(
	// 	"name" => $obj['name'],
	// 	"address" => $obj['address'],
	// 	"href" => "",
	// 	"pic" => "/images/wifi.png",
	// 	"coord" => array($obj['cx'],$obj['cy'])
	// );
 	$ar = array($obj['cx'], $obj['cy'], '"'.stripcslashes($obj['name'].' '.$obj['address']).'"');	
 	$WiFi[] = $ar;
}
// disconnect from server
$mongo->close();
 
if(!empty($WiFi)){
	//$Content = "var doctorsJSON = ".json_encode($WiFi).";";
	$Content = "var addressPoints = ".json_encode($WiFi).";";
	$filename = "doctorsJSON.js";

	// Вначале давайте убедимся, что файл существует и доступен для записи.
	if (is_writable($filename)) {

		// В нашем примере мы открываем $filename в режиме "записи в конец".
		// Таким образом, смещение установлено в конец файла и
		// наш $somecontent допишется в конец при использовании fwrite().
		if (!$handle = fopen($filename, 'w+')) {
			 echo "Не могу открыть файл ($filename)";
			 exit;
		}

		// Записываем $somecontent в наш открытый файл.
		if (fwrite($handle, $Content) === FALSE) {
			echo "Не могу произвести запись в файл ($filename)";
			exit;
		}

		echo "Ура! Записали в файл $filename";

		fclose($handle);

	} else {
		echo "Файл $filename недоступен для записи";
	}	
}
?>