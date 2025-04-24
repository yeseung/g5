<?php
include_once("./_common.php");
$g5['title'] = "타자연습";

include_once(G5_PATH.'/_head.php');
?>
	<link rel="stylesheet" type="text/css" href="resources/css/app_template.css">

    <div class="ma-auto text-center">
        <section id="content">
			<div id="gamescreen" class="gamescreen"></div>
			<div class="typingmain" id="typingmain">
				<ul class="typingmenu" id="typingmenu">
					<li id="menuscreen">
						<h1 class="menu-title">게임 메뉴</h1>
						<p>
							<i>레벨을 선택하고 게임을 시작하세요!</i>
						</p>
						<p id="gameMode">
							<a href="#" id="modeBeginner">초급</a>
							<a href="#" id="modeMedium">중급</a>
							<a href="#" id="modePro">고급</a>
						</p>
					</li>
					<!-- end #menuscreen -->

					<li id="pausescreen">
						<h1 class="menu-title">일시중지!</h1>
						<p>
							<i>현재 게임으로 돌아가려면 "스페이스 키보드 터치"를 입력하십시오!</i>
						</p>
						<p>
							<a href="#" class="returnMenu">게임 메뉴</a>
						</p>
					</li>
					<!-- end #pausescreen -->

					<li id="scorescreen">
						<h1 class="menu-title">게임 종료</h1>
						<p id="gameresult">
						</p>
						<p>
							<a href="#" class="returnMenu">게임 메뉴로 돌아가기</a>
						</p>
					</li>
					<!-- end #scorescreen -->
				</ul>
			</div>

            <div id="stat">
                <div class="missed">
                    놓침
                    <span id="missed">00</span>
                </div>
                <div class="score">
                    시간
                    <span id="timer">00</span>
                    점수
                    <span id="score">00</span>
                </div>
            </div>
        </section>

    </div>

	<audio id="soundGoal">
		<source src="resources/sound/ogg/remove.ogg" />
		<source src="resources/sound/mp3/remove.mp3" />
		귀하의 브라우저는 HTML5 오디오를 지원하지 않습니다.
	</audio>

	<audio id="soundFailure">
		<source src="resources/sound/ogg/fail.ogg" />
		<source src="resources/sound/mp3/fail.mp3" />
		귀하의 브라우저는 HTML5 오디오를 지원하지 않습니다.
	</audio>

	<audio id="soundNope">
		<source src="resources/sound/ogg/nope.ogg" />
		<source src="resources/sound/mp3/nope.mp3" />
		귀하의 브라우저는 HTML5 오디오를 지원하지 않습니다.
	</audio>

	<audio id="soundEnd">
		<source src="resources/sound/ogg/tada.ogg" />
		<source src="resources/sound/mp3/tada.mp3" />
		귀하의 브라우저는 HTML5 오디오를 지원하지 않습니다.
	</audio>

<script type="text/javascript">
 var teuser = '<?php echo $member['mb_id'];?>';
</script>
	<script src="resources/js/gamelogic.js"></script>
	<script>
	$(document).ready(function(){
				$(function() {
					 var p = new Platform();
					  p.init();
				});
		});
 </script>
<?php
include_once(G5_PATH.'/_tail.php');
