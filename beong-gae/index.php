<?php
include_once('./_common.php');

// 로그인 체크
if (!$is_member) {
    goto_url(G5_BBS_URL.'/login.php?url='.urlencode($_SERVER['REQUEST_URI']));
}

// 삭제 처리
if ($_POST['w'] == 'd') {
    $sql = " DELETE FROM {$event_table}
                WHERE mt_id = '".(int)$_POST['mt_id']."' 
                AND mb_id = '{$member['mb_id']}' ";
    sql_query($sql);
    goto_url('./');
}

// 참여인원 합계 먼저 조회
$sql = " SELECT SUM(participants) as total_participants FROM {$event_table} ";
$row = sql_fetch($sql);
$total_participants = $row['total_participants'];

// 데이터 조회
$sql = " SELECT * FROM {$event_table} ORDER BY mt_id DESC ";
$result = sql_query($sql);

$g5['title'] = '벙개 신청 목록';
include_once(G5_PATH.'/head.sub.php');

function mask_name($name) {
    $len = mb_strlen($name, 'UTF-8');
    if ($len <= 1) return $name;
    
    // 마지막 글자를 제외한 모든 글자를 *로 변경
    $masked = str_repeat('*', $len - 1) . mb_substr($name, -1, 1, 'UTF-8');
    return $masked;
}

// 전화번호 마스킹 함수
function mask_phone($phone1, $phone2, $phone3) {
    // 중간 번호의 뒤 두자리 마스킹
    $masked_phone2 = substr($phone2, 0, strlen($phone2)-2) . '**';
    
    // 마지막 번호 전체 마스킹
    $masked_phone3 = '****';
    
    return $phone1 . '-' . $masked_phone2 . '-' . $masked_phone3;
}
?>

<div class="list-container">
    <div class="header-area">
        <a href="<?php echo G5_URL; ?>" class="btn btn-home">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" class="home-icon">
                <path d="M12 3L4 9v12h5v-7h6v7h5V9z" fill="currentColor"/>
            </svg>
            홈으로
        </a>
    </div>

    <h2 class="list-title">벙개 신청 목록</h2>
    
    <div class="action-area">
        <div class="login-area">
            <?php if ($is_member) { ?>
                <span class="user-info"><?php echo $member['mb_nick']; ?>님</span>
                <a href="<?php echo G5_BBS_URL ?>/logout.php" class="btn btn-logout">로그아웃</a>
            <?php } else { ?>
                <a href="<?php echo G5_BBS_URL ?>/login.php?url=<?php echo urlencode($_SERVER['REQUEST_URI']); ?>" class="btn btn-login">로그인</a>
            <?php } ?>
        </div>
        <div class="btn-area">
            <a href="./form.php" class="btn btn-new">신청하기</a>
        </div>
    </div>
    
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>이름</th>
                    <th>인원</th>
                    <th>등록일</th>
                    <th>관리</th>
                </tr>
            </thead>
            <tbody>
            <?php
            for ($i=0; $row=sql_fetch_array($result); $i++) {
                $phone = mask_phone($row['phone1'], $row['phone2'], $row['phone3']);
                $name = mask_name($row['name']);
                $num = $i + 1;
                $is_owner = ($row['mb_id'] === $member['mb_id']);
            ?>
                <tr>
                    <td class="center"><?php echo $name; ?></td>
                    <td class="center"><?php echo $row['participants']; ?>명</td>
                    <td class="center"><?php echo substr($row['reg_date'], 5, 5); ?></td>
                    <td class="center">
                        <a href="form.php?w=u&mt_id=<?php echo $row['mt_id']; ?>" 
                           class="btn btn-sm <?php echo $is_owner ? 'btn-edit' : 'btn-disabled'; ?>">수정</a>
                        <button onclick="return meeting_delete(<?php echo $row['mt_id']; ?>)" 
                                class="btn btn-sm <?php echo $is_owner ? 'btn-delete' : 'btn-disabled'; ?>">삭제</button>
                    </td>
                </tr>
            <?php } ?>
            <?php if ($i == 0) { ?>
                <tr><td colspan="4" class="center">등록된 신청이 없습니다.</td></tr>
            <?php } else { ?>
                <tr class="total-row">
                    <td colspan="4" class="center bold">신청 <?php echo $num; ?>건 , 총 참석인원 : <?php echo number_format($total_participants); ?>명</td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function meeting_delete(mt_id) {
    // 먼저 해당 글의 작성자 정보를 확인
    var owner_check = false;
    
    // AJAX로 작성자 확인
    $.ajax({
        url: './ajax.check.php',  // 새로 만들 체크용 PHP 파일
        type: 'POST',
        data: {
            mt_id: mt_id,
            w: 'check'
        },
        async: false,  // 동기 처리
        success: function(data) {
            owner_check = (data.trim() === 'true');
        }
    });
    
    if (!owner_check) {
        alert("자신의 글만 삭제할 수 있습니다.");
        return false;
    }
    
    // 소유자 확인 후 삭제 진행
    if (confirm("정말 삭제하시겠습니까?")) {
        var f = document.createElement('form');
        f.setAttribute('method', 'post');
        f.setAttribute('action', './');
        
        var i = document.createElement('input');
        i.setAttribute('type', 'hidden');
        i.setAttribute('name', 'w');
        i.setAttribute('value', 'd');
        f.appendChild(i);
        
        var i = document.createElement('input');
        i.setAttribute('type', 'hidden');
        i.setAttribute('name', 'mt_id');
        i.setAttribute('value', mt_id);
        f.appendChild(i);
        
        document.body.appendChild(f);
        f.submit();
    }
    return false;
}
</script>

<style>
/* 기본 스타일 */
* { box-sizing: border-box; }
body { margin: 0; padding: 20px; background: #f5f6f7; font-family: Arial, sans-serif; }

/* 액션 영역 (로그인 + 신청하기) */
.action-area {
    display: flex;
    align-items: center;
    justify-content: space-between;  /* 변경: 양쪽 정렬로 수정 */
    margin-bottom: 30px;
    gap: 20px;
}

/* 버튼 영역 */
.btn-area {
    margin-bottom: 0;
    order: 2;  /* 추가: 순서를 뒤로 */
}

/* 로그인 영역 */
.login-area {
    display: flex;
    align-items: center;
    gap: 10px;
    order: 1;  /* 추가: 순서를 앞으로 */
}

/* 리스트 컨테이너 */
.list-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 30px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.list-title {
    margin: 0 0 20px;
    text-align: center;
    color: #333;
    font-size: 24px;
}

/* 테이블 스타일 */
.table-responsive {
    overflow-x: auto;
    margin-bottom: 20px;
}

table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 1rem;
    background-color: transparent;
}

th, td {
    padding: 12px;
    border-bottom: 1px solid #dee2e6;
}

th {
    background-color: #f8f9fa;
    font-weight: bold;
    text-align: center;
}

/* 테이블 셀 정렬 */
.center {
    text-align: center;
    vertical-align: middle;
    height: 48px; /* 테이블 행 높이 고정 */
}

/* 기본 버튼 재설정 */
.btn {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    vertical-align: middle;
    text-align: center;
    text-decoration: none;
    border: none;
    margin: 0;
    padding: 0;
    background: none;
    cursor: pointer;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

/* 신청하기 버튼 */
.btn-new {
    height: 40px;
    padding: 0 20px;
    background-color: #4a90e2;
    color: white;
    border-radius: 4px;
    font-size: 14px;
    font-weight: bold;
    min-width: 100px;
}

.btn-new:hover {
    background-color: #357abd;
}

/* 수정/삭제 버튼 공통 스타일 */
.btn-sm {
    height: 28px;
    line-height: 28px;
    padding: 0 10px;
    border-radius: 3px;
    font-size: 13px;
    font-weight: normal;
    min-width: 45px;
}

/* 수정 버튼 */
.btn-edit {
    background-color: #28a745;
    color: white;
    margin-right: 4px;
}

.btn-edit:hover {
    background-color: #218838;
}

/* 삭제 버튼 */
.btn-delete {
    background-color: #dc3545;
    color: white;
}

.btn-delete:hover {
    background-color: #c82333;
}

/* 버튼 컨테이너 스타일 */
td.center {
    line-height: 1;
    padding: 8px;
    white-space: nowrap;
}

/* 로그인 영역 스타일 */
.login-area {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-info {
    font-size: 14px;
    color: #333;
    font-weight: bold;
}

.btn-login,
.btn-logout {
    height: 32px;
    padding: 0 15px;
    border-radius: 4px;
    font-size: 13px;
    font-weight: bold;
    background-color: #6c757d;
    color: white !important;
    text-decoration: none !important;
}

.btn-login:hover,
.btn-logout:hover {
    background-color: #5a6268;
}

/* 컨테이너 위치 조정 */
.list-container {
    position: relative;
    margin-top: 40px;
}

/* 반응형 디자인 */
@media (max-width: 768px) {
    td.center {
        padding: 6px;
    }

    .btn-sm {
        height: 28px;
        line-height: 28px;
        padding: 0 8px;
        font-size: 12px;
        min-width: 40px;
    }

    .btn-login,
    .btn-logout {
        height: 28px;
        padding: 0 10px;
        font-size: 12px;
    }
}

/* 합계 행 스타일 추가 */
.total-row {
    background-color: #f8f9fa;
}

.total-row td {
    padding: 15px 12px;
    font-size: 15px;
}

.right {
    text-align: right;
}

.left {
    text-align: left;
}

.bold {
    font-weight: bold;
}

/* 반응형 디자인 수정 */
@media (max-width: 480px) {
    .action-area {
        flex-direction: column;
        align-items: stretch;  /* 변경: 전체 너비 사용 */
        gap: 15px;
    }

    .login-area {
        width: 100%;
        order: 2;  /* 모바일에서는 로그인 영역이 아래로 */
    }

    .btn-area {
        width: 100%;
        order: 1;  /* 모바일에서는 신청하기 버튼이 위로 */
    }

    .btn-new {
        width: 100%;
    }
}
</style>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>