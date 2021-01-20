<?php
$json['response'] = 0;
$json['strTest0'] = "Just some string data to test ".rand(1,1000);
echo json_encode($json);
exit();
?>