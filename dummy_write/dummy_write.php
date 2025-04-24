<?php
// ChatGPT API 를 활용하여 더미 글을 작성해 줍니다.
// ChatGPT API 를 활용하면 비용이 발생하므로 아래 링크에서 사용량을 체크하셔야 합니다.
// https://platform.openai.com/account/usage
// ChatGPT API 를 활용하면 바로 바로 결과를 알려주는 것이 아니라 한번에 하나의 글만 작성해 줍니다.
// 2023-08-10 ChatGPT를 활용하여 개발하였음.

set_time_limit(0); // 시간 제한 없음

$sub_menu = "300100";
require_once './_common.php';

if ($is_admin != 'super') {
    alert('최고관리자만 접근 가능합니다.');
}

if(isset($_POST['bo_table'])) {
    $bo_table = $_POST['bo_table'];
    // 여기에서 필요한 처리를 수행
    // echo "Success {$bo_table}"; // 예시 응답
} else {
    die("오류 : bo_table 을 넘겨주세요.");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die('오류 : POST 방식으로 넘겨 주세요.');
}

// https://platform.openai.com/account/api-keys
// dummy_api_key.php 에 $openai_api_key 이 변수를 선언하세요.
$openai_api_key = "sk-************************************************"; 
require_once './dummy_api_key.php';

$hashtag_cnt = 5;
$subject_len = 20;
$content_len = 300;

$userMessage = "";
$userMessage .= "일상적인 이야기를 사람이 쓴것처럼 글을 1개만 작성해줘."; // 여러개 안됩니다. 속도가 너무 느림.
$userMessage .= "제목은 {$subject_len}글자 이상, 내용은 {$content_len}글자 이하로 작성해줘.";
$userMessage .= "해시태그는 {$hashtag_cnt}개를 작성해줘.";
$userMessage .= "결과는 json 데이터로 반환해줘.";
$userMessage .= "json 데이터에서 제목은 subject, 내용은 content, 해시태그는 hashtag 로 구분해줘.";

$data = array(
    "model" => "gpt-3.5-turbo",
    "messages" => array(
        array("role" => "system", "content" => "You are a helpful assistant."),
        array("role" => "user", "content" => $userMessage)
    ),
    "temperature" => 0.5
);

$options = array(
    'http' => array(
        'method'  => 'POST',
        'header'  => "Content-type: application/json\r\n" .
                        "Authorization: Bearer {$openai_api_key}\r\n",
        'content' => json_encode($data),
    ),
);
$context  = stream_context_create($options);
$json_data = file_get_contents('https://api.openai.com/v1/chat/completions', false, $context);

$response = json_decode($json_data, true);
$content_string = $response['choices'][0]['message']['content'];
$content_decoded = json_decode($content_string);

$subject = $content_decoded->subject;
$content = $content_decoded->content;
$hashtag = $content_decoded->hashtag;

// .(blank), ?(blank), !(blank) 뒤에 <br> 추가
$content = preg_replace('/([.?!])\s/', '$1<br>', $content);

// array_map 함수를 사용하여 배열의 각 문자열 앞에 # 붙이기
$hashtag = array_map(function($item) {
    return "#" . $item;
}, $hashtag);
// 배열을 하나의 문자열로 합치기
$hashtag = implode(" ", $hashtag);

// 이미지 라이선스 : https://unsplash.com/ko/%EB%9D%BC%EC%9D%B4%EC%84%A0%EC%8A%A4 
$image_html = '<div class="content_image" align="left"><img src="https://source.unsplash.com/random" border="0" style="width:250px;margin-top:20px;margin-bottom:20px;"></div>';
$content = '<div class="content_main">'.$content.'</div>';
$content = $content.'<p>&nbsp;</p><div class="content_hashtag">'.$hashtag.'</div>';

$subject = addslashes($subject);
$content = addslashes($content);
$content = preg_replace('/((.*?\<br\>){5})/', '$1'.$image_html, $content, 1); //5번째줄 뒤에 이미지 넣기

$write_table = $g5['write_prefix'] . $bo_table;
$next_wr_num = get_next_num($write_table);
$password = md5(time()*time());
$ip = rand(1, 255) . "." . rand(1, 255) . "." . rand(1, 255) . "." . rand(1, 255);

$sql = "select * from {$g5['member_table']} where mb_1 = 'dummy' order by rand() limit 1 ";
$row = sql_fetch($sql);
if ($row) {
    $mb_id = $row['mb_id'];
    $mb_name = $row['mb_nick'];
} else {
    $mb_id = $member['mb_id'];
    $mb_name = $member['mb_nick'];
}

$datetime = G5_TIME_YMDHIS;

$sql =
    "insert into {$write_table} 
        set wr_num = '{$next_wr_num}',
            wr_is_comment = 0,
            wr_option = 'html1',
            mb_id = '{$mb_id}',
            wr_name = '{$mb_name}',
            wr_password = '{$password}',
            wr_subject = '{$subject}',
            wr_content = '{$content}',
            wr_datetime = '{$datetime}',
            wr_ip = '$ip'
";
// die($sql);
sql_query($sql);

$wr_id = sql_insert_id();

// 부모 아이디에 UPDATE
sql_query(" update $write_table set wr_parent = '$wr_id' where wr_id = '$wr_id' ");

// 새글 INSERT
sql_query(" insert into {$g5['board_new_table']} ( bo_table, wr_id, wr_parent, bn_datetime, mb_id ) values ( '{$bo_table}', '{$wr_id}', '{$wr_id}', '{$datetime}', '{$mb_id}' ) ");

// 게시글 1 증가
sql_query("update {$g5['board_table']} set bo_count_write = bo_count_write + 1 where bo_table = '{$bo_table}'");


die(''); // OK
?>
