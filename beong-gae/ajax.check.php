<?php
// ajax.check.php
include_once('./_common.php');

if (!$is_member) {
    die('false');
}

$mt_id = isset($_POST['mt_id']) ? (int)$_POST['mt_id'] : 0;

// 작성자 확인
$sql = " SELECT mb_id FROM {$event_table} WHERE mt_id = '{$mt_id}' ";
$row = sql_fetch($sql);

if ($row['mb_id'] === $member['mb_id']) {
    echo 'true';
} else {
    echo 'false';
}
?>