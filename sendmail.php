<?php
include_once('./_common.php');
header("Content-type:application/json; charset=utf-8");

include_once(G5_LIB_PATH.'/mailer.lib.php');

$admin_email_name = "한컴타자연습.kr";
$admin_email = "noreply@teml.net";

$sql = " SELECT em_email FROM `email` ORDER BY RAND() LIMIT 1 ";
$row = sql_fetch($sql);

$email = trim($row['em_email']);

$subject = "[광고] 여러분의 추억을 공유해 주세요! - https://한컴타자연습.kr";

$content = "";
$content .= "<h1>추억 속 타자 연습, 다시 시작해볼까요?</h1>";
$content .= "<br><br>";
$content .= "- 옛 감성을 느끼고 싶은 분들<br>";
$content .= "- 기본적인 타자 실력을 기르고 싶은 초보자<br>";
$content .= "- 아이들에게 안전한 환경에서 타자 연습을 시키고 싶은 부모님<br><br>";
$content .= "1990년대 컴퓨터를 사용하셨던 분이라면 한 번쯤 접해보셨을 한컴타자연습!<br>";
$content .= "익숙한 화면과 친숙한 인터페이스로 그 시절의 향수를 다시 한번 느껴보세요.<br><br>";
$content .= "* 재미있게 실력 향상 – 초보자도 쉽게 따라 할 수 있는 연습 모드<br>";
$content .= "* 추억 소환! – 90년대 감성 그대로, 향수를 자극하는 디자인과 구성<br>";
$content .= "* 안심하고 연습 – 아이들도 안전하게 사용할 수 있는 학습 환경<br><br>";
$content .= "지금 바로 한컴타자연습을 만나보세요!<br>";
$content .= "인터넷 주소창에 한글로 입력 : https://한컴타자연습.kr<br><br>";
$content .= "오랜만에 키보드를 두드리며, 그때 그 시절을 떠올려 보세요!<br><br><br><br><br><br>";
$content .= rand();

$mailer = mailer($admin_email_name, $admin_email, $email, $subject, $content, 1);


$q = [];
if ($mailer)
    $q['status'] = "S-1";
else
   $q['status'] = "F-1"; 
$q['message'] = "".$mailer." | ".$email;

echo json_encode($q);
exit;





