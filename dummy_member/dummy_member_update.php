<?php
ini_set('memory_limit', '-1');

$sub_menu = "200910";
require_once './_common.php';

// clean the output buffer
ob_end_clean();

// if (!(version_compare(phpversion(), '5.3.0', '>=') && defined('G5_BROWSCAP_USE') && G5_BROWSCAP_USE)) {
//     die('사용할 수 없는 기능입니다.');
// }

if ($is_admin != 'super') {
    die('최고관리자만 접근 가능합니다.');
}

function generate_member() {
    // 한글 성씨 및 이름 문자열
    $korean_surnames = "김이박최정강조윤장임한오서신서권황안손류전홍고문양손배조백허유남심노정하계성차주";
    $korean_names = "가나다라마바사아자차카타파하견영민지선은희동준현호예영재백혜진수웅윤창훈령주성미기림";

    // 회원 아이디 생성
    $user_id_length = rand(5, 20);
    $user_id = chr(rand(97, 122)); // 첫 글자는 영문 소문자로 시작
    $user_id .= substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyz0123456789', ceil(($user_id_length-1) / 36))), 0, $user_id_length-1);
    
    // 이름 생성
    $surname = mb_substr($korean_surnames, rand(0, mb_strlen($korean_surnames) - 1), 1);
    $name_length = rand(1, 2);
    $name = '';
    for ($i = 0; $i < $name_length; $i++) {
        $name .= mb_substr($korean_names, rand(0, mb_strlen($korean_names) - 1), 1);
    }
    $full_name = $surname . $name;
    
    // 패스워드 생성
    $password_length = rand(8, 20);
    $password = substr(str_shuffle(str_repeat('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789', ceil($password_length / 62))), 0, $password_length);
    
    // 이메일 생성
    $domain_endings = ["com", "net", "org", "xyz", "tld"];
    $domain_ending = $domain_endings[array_rand($domain_endings)];
    $domain = $user_id . "." . $domain_ending;
    $email = $user_id . "@" . $domain;

    // IP 주소 생성
    $ip = rand(1, 255) . "." . rand(1, 255) . "." . rand(1, 255) . "." . rand(1, 255);
    
    return [
        "user_id" => $user_id,
        "name" => $full_name,
        "password" => $password,
        "email" => $email,
        "ip" => $ip
    ];
}

// 10명의 dummy 회원 데이터 생성
for ($i = 0; $i < 100; $i++) {
    $user = generate_member();

    $row = sql_fetch("select count(*) as cnt from  {$g5['member_table']} where mb_id = '{$user['user_id']}' ");
    if ($row['cnt']) {
        // 이미 있으므로 저장하지 않음
        continue;
    }

    $sql = " 
         insert into {$g5['member_table']} 
            set mb_id       = '{$user['user_id']}',
                mb_name     = '{$user['name']}',
                mb_nick     = '{$user['name']}',
                mb_password = '{$user['password']}',
                mb_email    = '{$user['email']}',
                mb_level    = '{$config['cf_register_level']}',
                mb_datetime = NOW(),
                mb_ip       = '{$user['ip']}',
                mb_1        = 'dummy' 
    ";
    sql_query($sql);
}    


die('');
?>