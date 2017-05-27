<meta charset="utf-8">
<?php

require_once 'functions.php';

set_time_limit(999999999);

$url_tpl = "https://opendatabot.com/api/v1/court?number=%s&apiKey=kAWFWQ5KNRAP";

$conn = connect_to_db();




$result = mysqli_query($conn, "Select number from `hearings`");

$i = 0;
while ($row = mysqli_fetch_array($result)) {
    



    $url = sprintf($url_tpl, ($row[0]));

    $resp = get_fcontent($url);

	if (! in_array($resp[1]['http_code'], array(404, 200))) {
		//throw new Exception('Unknown http code.' . $resp[1]['http_code']);
		echo 'Unknown http code.' . $resp[1]['http_code'] . " url: " . $url;
		echo '<br>';
		continue;
	}

	if ($resp[1]['http_code'] == 404) {
		echo '404 !!!!' . $url;
		continue;
	} 

	$cases = json_decode($resp[0], true);

	foreach ($cases as $case) {
		print_pre($case);
		mysqli_query($conn, sprintf("INSERT into `resolutions` (`case_id`, `date`, `text`) VALUES ('%s', '%s', \"%s\")",
			$case['cause_num'],
			preg_replace('/([^+]+)\+/', '$1', $case['adjudication_date']),
			mysqli_real_escape_string($conn, $case['text'])
		));
		echo mysqli_error($conn);
	}


	$i++;
}


