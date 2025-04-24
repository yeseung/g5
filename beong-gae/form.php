<?php
include_once('./_common.php');

// 로그인 체크
if (!$is_member) {
    goto_url(G5_BBS_URL.'/login.php?url='.urlencode($_SERVER['REQUEST_URI']));
}

// 테이블이 없다면 생성
if(!sql_query(" DESC {$event_table} ", false)) {
    $sql = " CREATE TABLE IF NOT EXISTS {$event_table} (
                mt_id int(11) NOT NULL AUTO_INCREMENT,
                mb_id varchar(20) NOT NULL,
                name varchar(255) NOT NULL,
                phone1 varchar(4) NOT NULL,
                phone2 varchar(4) NOT NULL,
                phone3 varchar(4) NOT NULL,
                participants int(2) NOT NULL DEFAULT '1',
                reg_date datetime DEFAULT current_timestamp(),
                PRIMARY KEY (mt_id)
            ) ";
    sql_query($sql);
}

// 폼 제출 처리
if ($_POST['w'] == '') {
    if(isset($_POST['name']) && isset($_POST['phone1']) && isset($_POST['phone2']) && isset($_POST['phone3'])) {
        $sql = " INSERT INTO {$event_table}
                    SET mb_id = '{$member['mb_id']}',
                        name = '".sql_real_escape_string($_POST['name'])."',
                        phone1 = '".sql_real_escape_string($_POST['phone1'])."',
                        phone2 = '".sql_real_escape_string($_POST['phone2'])."',
                        phone3 = '".sql_real_escape_string($_POST['phone3'])."',
                        participants = '".sql_real_escape_string($_POST['participants'])."' ";
        sql_query($sql);
        goto_url('./');
    }
}

// 수정 시 기존 데이터 가져오기 부분
$mt = array();
if (isset($_GET['w']) && $_GET['w'] == 'u' && isset($_GET['mt_id'])) {
    // 먼저 해당 글의 소유자 확인
    $sql = " SELECT mb_id FROM {$event_table} WHERE mt_id = '".(int)$_GET['mt_id']."' ";
    $row = sql_fetch($sql);
    
    if (!$row['mb_id'] || $row['mb_id'] !== $member['mb_id']) {
        alert("자신의 글만 수정할 수 있습니다.", "./");
    }
    
    // 소유자 확인 후 데이터 가져오기
    $sql = " SELECT * FROM {$event_table}
                WHERE mt_id = '".(int)$_GET['mt_id']."' 
                AND mb_id = '{$member['mb_id']}' ";
    $mt = sql_fetch($sql);
    
    // 데이터가 없는 경우
    if (!$mt['mt_id']) {
        alert("존재하지 않는 데이터입니다.", "./");
    }
}

// 수정 처리
if ($_POST['w'] == 'u') {
    // 수정 전 권한 체크
    $sql = " SELECT mb_id FROM {$event_table} WHERE mt_id = '".(int)$_POST['mt_id']."' ";
    $row = sql_fetch($sql);
    
    if (!$row['mb_id'] || $row['mb_id'] !== $member['mb_id']) {
        alert("자신의 글만 수정할 수 있습니다.", "./");
    }
    
    // 입력값 검증
    if (empty($_POST['name']) || empty($_POST['phone1']) || empty($_POST['phone2']) || empty($_POST['phone3'])) {
        alert("모든 필드를 입력해주세요.");
    }
    
    // 전화번호 형식 검증
    if (!preg_match("/^01[0-9]$/", $_POST['phone1']) || 
        !preg_match("/^[0-9]{3,4}$/", $_POST['phone2']) || 
        !preg_match("/^[0-9]{4}$/", $_POST['phone3'])) {
        alert("올바른 전화번호 형식이 아닙니다.");
    }
    
    // 데이터 업데이트
    $sql = " UPDATE {$event_table}
                SET name = '".sql_real_escape_string($_POST['name'])."',
                    phone1 = '".sql_real_escape_string($_POST['phone1'])."',
                    phone2 = '".sql_real_escape_string($_POST['phone2'])."',
                    phone3 = '".sql_real_escape_string($_POST['phone3'])."',
                    participants = '".sql_real_escape_string($_POST['participants'])."'
                WHERE mt_id = '".(int)$_POST['mt_id']."'
                AND mb_id = '{$member['mb_id']}' ";
    sql_query($sql);
    alert("수정이 완료되었습니다.", "./");
}

$g5['title'] = '벙개 신청서';
include_once(G5_PATH.'/head.sub.php');
?>

<div class="meeting-form">
    <h2 class="form-title">벙개 신청서</h2>
    
    <form method="post" onsubmit="return meeting_submit(this);" class="form-container" autocomplete="off">
        <input type="hidden" name="w" value="<?php echo isset($_GET['w']) ? $_GET['w'] : ''; ?>">
        <input type="hidden" name="mt_id" value="<?php echo isset($mt['mt_id']) ? $mt['mt_id'] : ''; ?>">
        
        <div class="form-group">
            <label for="name">이름</label>
            <input type="text" name="name" id="name" required 
                   value="<?php echo isset($mt['name']) ? $mt['name'] : ''; ?>"
                   class="form-input">
        </div>
        
        <div class="form-group">
            <label>휴대폰번호</label>
            <div class="phone-group">
                <input type="text" name="phone1" maxlength="3" required 
                       value="<?php echo isset($mt['phone1']) ? $mt['phone1'] : '010'; ?>" 
                       class="phone-input" pattern="[0-9]{3}" 
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                <span class="phone-separator">-</span>
                <input type="text" name="phone2" required 
                       value="<?php echo isset($mt['phone2']) ? $mt['phone2'] : ''; ?>" 
                       class="phone-input"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');">
                <span class="phone-separator">-</span>
                <input type="text" name="phone3" required 
                       value="<?php echo isset($mt['phone3']) ? $mt['phone3'] : ''; ?>" 
                       class="phone-input"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '');">
            </div>
        </div>

        <div class="form-group">
            <label for="participants">참석 인원</label>
            <select name="participants" id="participants" class="form-input select-input" required>
                <?php for($i=1; $i<=9; $i++) { ?>
                <option value="<?php echo $i; ?>" <?php echo (isset($mt['participants']) && $mt['participants'] == $i) ? 'selected' : ''; ?>>
                    <?php echo $i; ?>명
                </option>
                <?php } ?>
            </select>
        </div>
        
        <div class="btn-group">
            <a href="./" class="btn btn-list">목록</a>
            <input type="submit" value="<?php echo isset($_GET['w']) && $_GET['w'] == 'u' ? '수정' : '등록'; ?>" 
                   class="btn btn-submit">
        </div>
    </form>
</div>

<script>
function meeting_submit(f) {
    if (f.name.value.trim() == "") {
        alert("이름을 입력해주세요.");
        f.name.focus();
        return false;
    }
    if (f.phone1.value.length != 3 || !/^01[0-9]$/.test(f.phone1.value)) {
        alert("휴대폰번호 첫 자리는 01로 시작하는 3자리 숫자를 입력해주세요.");
        f.phone1.focus();
        return false;
    }    
    if (f.phone2.value.length < 3) {
        alert("휴대폰번호 가운데 자리를 3~4자리로 입력해주세요.");
        f.phone2.focus();
        return false;
    }
    if (f.phone3.value.length != 4) {
        alert("휴대폰번호 마지막 자리를 4자리로 입력해주세요.");
        f.phone3.focus();
        return false;
    }
    // 참석인원 검증 추가
    if (f.participants.value < 1 || f.participants.value > 9) {
        alert("참석 인원을 선택해주세요.");
        f.participants.focus();
        return false;
    }
    return true;
}
</script>

<style>
/* 기본 스타일 */
* { box-sizing: border-box; }
body { margin: 0; padding: 20px; background: #f5f6f7; font-family: Arial, sans-serif; }

/* 폼 컨테이너 */
.meeting-form {
    max-width: 600px;
    margin: 0 auto;
    padding: 30px;
    background: white;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-title {
    margin: 0 0 30px;
    text-align: center;
    color: #333;
    font-size: 24px;
}

/* 폼 그룹 */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    color: #444;
    font-weight: bold;
}

.form-input {
    width: 100%;
    height: 45px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
}

/* 전화번호 입력 그룹 */
.phone-group {
    display: flex;
    align-items: center;
    gap: 8px;
    max-width: 100%;
}

.phone-input {
    height: 45px;
    width: 90px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 16px;
    text-align: center;
}

.phone-input:first-child {
    width: 70px;
}

.phone-separator {
    color: #666;
    font-weight: bold;
    flex-shrink: 0;
}

/* 버튼 스타일 */
.btn-group {
    display: flex;
    gap: 10px;
    margin-top: 30px;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 45px;
    flex: 1;
    padding: 0 20px;
    border: none;
    border-radius: 4px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    text-align: center;
    text-decoration: none;
    transition: background-color 0.2s;
}

.btn-submit {
    background: #4a90e2;
    color: white;
}

.btn-submit:hover {
    background: #357abd;
}

.btn-list {
    background: #6c757d;
    color: white;
}

.btn-list:hover {
    background: #5a6268;
}

/* 목록 페이지 버튼 */
.btn-sm {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 28px;
    padding: 0 10px;
    font-size: 13px;
    border-radius: 3px;
    margin: 0 2px;
    line-height: 1;
    min-width: 45px;
}

.btn-edit {
    background: #28a745;
    color: white;
}

.btn-edit:hover {
    background: #218838;
}

.btn-delete {
    background: #dc3545;
    color: white;
}

.btn-delete:hover {
    background: #c82333;
}

/* 셀렉트 박스 스타일 */
.select-input {
    -webkit-appearance: none;
    -moz-appearance: none;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23444' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 12px;
    padding-right: 35px;
    cursor: pointer;
}

.select-input::-ms-expand {
    display: none;
}

.select-input option {
    padding: 8px;
}

/* 반응형 디자인 */
@media (max-width: 480px) {
    body { 
        padding: 10px; 
    }
    .meeting-form { 
        padding: 20px; 
    }
    
    .phone-group {
        gap: 4px;
        justify-content: space-between;
    }
    
    .phone-input {
        width: 28%;
    }
    
    .phone-input:first-child {
        width: 25%;
    }
    
    .btn-group { 
        flex-direction: column; 
    }
    
    .btn {
        width: 100%;
    }
}
</style>

<?php
include_once(G5_PATH.'/tail.sub.php');
?>