<meta charset="utf-8">
<?php

require_once 'functions.php';

set_time_limit(999999999);

$conn = connect_to_db();

$court_codes = get_court_codes();

foreach ($court_codes as $row) {

	mysqli_query($conn, sprintf("INSERT into `court_names` (id, name) VALUES (%s, '%s')", $row['id'], $row['name']));
}