<?php
include_once('./_common.php');

$q = [];

$sql = " select * from g5_member where (1) order by mb_datetime desc ";
$result = sql_query($sql);

for ($i=0; $row=sql_fetch_array($result); $i++) {
    //echo "<pre>";print_r($row);echo "</pre>";
    
    if ($row['mb_email']!=""){
        $q[] = strtolower(trim($row['mb_email']));
    }
    
}

//echo "<pre>";print_r($q);echo "</pre>";

$arr = array_unique($q);


foreach ($arr as $email) {
    //echo $email . "<br>".PHP_EOL;
    //echo "INSERT INTO email (em_email, em_datetime) VALUES ('{$email}', NOW());<br>" . PHP_EOL;
}




