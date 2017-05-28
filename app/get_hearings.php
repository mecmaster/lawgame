<meta charset="utf-8">
<?php

set_time_limit(999999999);

require_once 'functions.php';


$conn = connect_to_db();



function get_court_hearings($court_id, $date) {
	$url_tpl = "https://opendatabot.com/api/v1/schedule?date=%s&court_id=%s&apiKey=3ETWXFcWna3X";

	$url = sprintf($url_tpl, $date, $court_id);

	$resp = get_fcontent($url);

	//$resp = file_get_contents($url);


	if (! in_array($resp[1]['http_code'], array(404, 200))) {
		//throw new Exception('Unknown http code.' . $resp[1]['http_code']);
		echo 'Unknown http code.' . $resp[1]['http_code'] . " url: " . $url;
		echo '<br>';
	}

	if ($resp[1]['http_code'] == 404) {
		return [];
	} 

	return json_decode($resp[0], true);
}

$court_codes = get_court_codes();


$court_hearings = [];

foreach ($court_codes as $court_code) {
	$code = $court_code['id'];

	if ($code < 66) continue;

	$court_hearings_num = 0;

	for ($i = 3; $i >= -3; $i--) {
		$date = date('Y-m-d', time() + $i * 24 * 60 * 60);

		$hearings = get_court_hearings($code, $date);

		foreach ($hearings as $hearing) {
			mysqli_query($conn, sprintf("INSERT into `hearings` (`court_id`, `judge`, `forma`, `number`, `involved`, `description`, `date`) VALUES (%s, '%s', '%s', '%s', '%s', '%s', '%s')",
				$code,
				$hearing['judge'],
				$hearing['forma'],
				$hearing['number'],
				$hearing['involved'],
				$hearing['description'],
				$hearing['date']
				));
		}




		$court_hearings_num += count($hearings);
	}

	$court_hearings[$code] = $court_hearings_num;



	//echo $code . "<br>";
	//


}

//$court_hearings = get_court_hearings('290', date('Y-m-d', time() - 50 * 24 * 60 * 60));


print_pre($court_hearings);


