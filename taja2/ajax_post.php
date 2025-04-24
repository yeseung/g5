<?php
include_once("./_common.php");
include_once("./config.php");

if ($_POST['mb_id'] == "")
    die("-8888");

// 오늘의 횟수를 체크
$sql = " select count(*) as cnt from `$taja_table` where mb_id = '{$_POST['mb_id']}' and taja_datetime like '".G5_TIME_YMD."' ";
$result = sql_fetch($sql);
if ($result['cnt'] > $today_max) {
    die("-9999");
}

$prnSpeedCur = (int) $_POST['prnSpeedCur'];
$prnAccuracyCur = (int) $_POST['prnAccuracyCur'];

// 최대 타자수를 넘으면 포인트를 기본만 준다. 그러기 위해 저속으로 타자 속도를 변경.
if ($prnSpeedCur >= $max_speed)
    $prnSpeedCur = 10;

$taja_table = G5_TABLE_PREFIX . "taja";

// 회원의 최고 타자점수를 찾는다
$sql = " select max(taja_point) as max_point from $taja_table where mb_id = '{$member['mb_id']}' ";

$result = sql_fetch($sql);

// 최고 타자포인트보다 높으면 20 포인트를, 아니면 그냥 포인트 10를 준다
if ($prnSpeedCur > $result['max_point'])
    $point = 20;
else
    $point = 10;

$point = (int) ($point * $prnAccuracyCur / 100);

// function insert_point($mb_id, $point, $content='', $rel_table='', $rel_id='', $rel_action='')
insert_point($_POST['mb_id'], $point, "게임 - 타자하기", "taja", G5_SERVER_TIME, "타자하기");

$sql = " insert into $taja_table set taja_point = '$prnSpeedCur', taja_datetime='".G5_TIME_YMDHIS."', mb_id = '{$_POST['mb_id']}' ";
sql_query($sql);

echo "$point";
