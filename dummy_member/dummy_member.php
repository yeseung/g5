<?php
$sub_menu = "200910";
require_once './_common.php';

// if (!(version_compare(phpversion(), '5.3.0', '>=') && defined('G5_BROWSCAP_USE') && G5_BROWSCAP_USE)) {
//     alert('사용할 수 없는 기능입니다.', correct_goto_url(G5_ADMIN_URL));
// }

if ($is_admin != 'super') {
    alert('최고관리자만 접근 가능합니다.');
}

$g5['title'] = '더미회원생성';
require_once './admin.head.php';
?>

<div id="processing">
    <p>더미 회원을 생성하시려면 아래 생성 버튼을 클릭해 주세요.</p>
    <button type="button" id="run_update">100명 생성</button>
</div>

<script>
    $(function() {
        $("#run_update").on("click", function() {
            $("#processing").html('<div class="update_processing"></div><p>더미 회원을 생성 중입니다.</p>');

            $.ajax({
                url: "./dummy_member_update.php",
                async: true,
                cache: false,
                dataType: "html",
                success: function(data) {
                    if (data != "") {
                        alert(data);
                        return false;
                    }

                    $("#processing").html("<div class='check_processing'></div><p>더미 회원을 생성 했습니다.<br><a href='./member_list.php'>회원관리</a>로 이동해서 확인해 주세요.</p>");
                }
            });
        });
    });
</script>

<?php
require_once './admin.tail.php';
?>