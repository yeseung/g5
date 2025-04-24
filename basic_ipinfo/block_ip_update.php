<?php
include_once($_SERVER['DOCUMENT_ROOT'] .'/common.php');

header('Content-Type: application/json');

$response = array('success' => false, 'message' => '');

if (isset($_POST['ip_to_block']) && !empty($_POST['ip_to_block'])) {
    $ip_to_block = trim($_POST['ip_to_block']);

    if (!preg_match('/^(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}|\d{1,3}\.\d{1,3}\.\+)$/', $ip_to_block)) {
        $response['message'] = "유효하지 않은 IP 형식입니다.";
        echo json_encode($response);
        exit;
    }

    $pattern = array($ip_to_block);
    for ($i = 0; $i < count($pattern); $i++) {
        $pattern[$i] = trim($pattern[$i]);
        if (empty($pattern[$i])) {
            continue;
        }

        $pattern[$i] = str_replace(".", "\.", $pattern[$i]);
        $pattern[$i] = str_replace("+", "[0-9\.]+", $pattern[$i]);
        $pat = "/^{$pattern[$i]}$/";

        if (preg_match($pat, $_SERVER['REMOTE_ADDR'])) {
            $response['message'] = "현재 접속 IP: " . $_SERVER['REMOTE_ADDR'] . "가 차단될 수 있기 때문에, 다른 IP를 입력해 주세요.";
            echo json_encode($response);
            exit;
        }
    }

    $sql = "SELECT cf_intercept_ip FROM {$g5['config_table']} LIMIT 1";
    $result = sql_fetch($sql);
    $current_ips = trim($result['cf_intercept_ip']);

    $ip_list = $current_ips ? explode("\n", $current_ips) : array();
    if (in_array($ip_to_block, array_map('trim', $ip_list))) {
        $response['message'] = "이미 차단된 IP입니다.";
        echo json_encode($response);
        exit;
    }

    $new_ips = $current_ips ? $current_ips . "\n" . $ip_to_block : $ip_to_block;
    $pattern = explode("\n", trim($new_ips));
    for ($i = 0; $i < count($pattern); $i++) {
        $pattern[$i] = trim($pattern[$i]);
        if (empty($pattern[$i])) {
            continue;
        }

        $pattern[$i] = str_replace(".", "\.", $pattern[$i]);
        $pattern[$i] = str_replace("+", "[0-9\.]+", $pattern[$i]);
        $pat = "/^{$pattern[$i]}$/";

        if (preg_match($pat, $_SERVER['REMOTE_ADDR'])) {
            $response['message'] = "현재 접속 IP: " . $_SERVER['REMOTE_ADDR'] . "가 차단될 수 있기 때문에, 다른 IP를 입력해 주세요.";
            echo json_encode($response);
            exit;
        }
    }

    $sql = "UPDATE {$g5['config_table']} SET cf_intercept_ip = '" . sql_real_escape_string($new_ips) . "'";
    $query_result = sql_query($sql);

    if ($query_result) {
        $response['success'] = true;
    } else {
        $response['message'] = "DB 업데이트에 실패했습니다.";
    }
} else {
    $response['message'] = "IP가 제공되지 않았습니다.";
}

echo json_encode($response);
?>