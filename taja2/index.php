<?php
include_once("./_common.php");
include_once("./config.php");

$g5['title'] = "타자연습";

include_once(G5_PATH.'/_head.php');

if(!$member['mb_id']) {
  alert("회원전용 페이지입니다. 로그인 후 이용하세요.", G5_BBS_URL."/login.php");
}

// 오늘의 횟수를 체크
$sql = " select count(*) as cnt from $taja_table where mb_id = '{$member['mb_id']}' and taja_datetime like '".G5_TIME_YMD."%' ";
$result = sql_fetch($sql);
if ($result['cnt'] > $today_max) {
    alert("오늘의 횟수 $today_max 를 초과하셨습니다. 과도한 게임은 서버의 건강에 좋지 않습니다.");
}
?>
<div <?php echo isset($g5['body_script']) ? $g5['body_script'] : "";?> <?php if (!$member['mb_level'] < 9) { ?>oncontextmenu='return false' onderagstart='return false' onselectstart='return false'<?php } ?>>
<link type="text/css" rel="stylesheet" media="all" href="css/style.css" />
	<div class="top-summary">
	<h2>타자게임 이란?</h2>
	<div class="content">
		<ul>
			<li>타 프로그램의 타자속도 및 통상적인 타자속도와 다르므로 착오없으시기 바랍니다.</li>
			<li>과도한 게임은 서버의 CPU 건강에 좋지 않습니다. CPU 건강을 위해 하루 <?php echo $today_max?> 회로 제한 됩니다.</li>
			<li>타자 정확도의 백분율에 따라 포인트가 차감 됩니다 (예: 80% 정확도*10포인트= 8포인트지급)</li>
			<li>1번 게임에 <?php echo $p1?> 포인트, 본인의 기록을 갱신하면 <?php echo $p2?> 포인트를 지급 합니다.</li>
			<li>비회원은 포인트가 지급되지 않으며, 비정상적 타수(<?php echo $max_speed?> 타 이상)는 기본 포인트만 지급 됩니다.</li>
      <?php
	    $sql = " select max(taja_point) as max_point from $taja_table";
	    $result = sql_fetch($sql);
	    $max_taja = $result['max_point'];
	    $sql = " select mb_id from $taja_table where taja_point = '$max_taja' ";
	    $result = sql_fetch($sql);
	    $mb = get_member($result['mb_id']);
	    $max_nick = $mb['mb_nick'];
      ?>
    	<li><?php echo $config[cf_title]?> 최고기록은 <b><?php echo $max_nick?></b>님의 <b><?php echo number_format($max_taja)?> 타</b> 입니다.</li>
      <?php
      if ($member['mb_id']) {
    	    $sql = " select max(taja_point) as max_point from $taja_table where mb_id = '{$member['mb_id']}' ";
	        $result = sql_fetch($sql);
    	    $max_mb_taja = $result['max_point'];
      ?>
    			<li><?php echo $member['mb_nick']?>님의 최고기록은 <?php echo number_format($max_mb_taja)?> 타 입니다.</li>
			<? } ?>
			</ul>
	</div>
</div>

<body onload="http.setHttp()">
	<div id="httpBox">

		<div id="exString" class="smallBox">타자 연습도 하고~ 타자 왕도 되고~</div>
		<div class="ph"></div>
		<div class="smallBox">
			<input class="textbox" id="httpInputString" onkeypress="return http.keyUp()" onkeyup="http.chkMiss()" onkeydown="http.chkTime()" onpaste="javascript:return false;" type="text">
		</div>

		<div class="ph"></div>

		<div id="status" class="smallBox">
			<table width="100%">
				<colgroup><col width="80">
				<col width="">
				<col width="80">
				</colgroup>
				<tbody>
				<tr>
					<th>정확도</th>
					<td>
						<div class="barBgcolor">
							<div id="barAccuracyCur" class="bar" style="background-color: rgb(51, 204, 255); width: 88%;"></div>
						</div>
					</td>
					<td class="nums">
						<span id="prnAccuracyCur">88</span>%
					</td>
				</tr>
				<tr>
					<th>평균 정확도</th>
					<td>
						<div class="barBgcolor">
							<div id="barAccuracyTotal" class="bar" style="background-color: rgb(51, 153, 204); width: 88%;"></div>
						</div>
					</td>
					<td class="nums">
						<span id="prnAccuracyTotal">88</span>%
					</td>
				</tr>
				<tr>
					<th>현재 속도</th>
					<td>
						<div class="barBgcolor">
							<div id="barSpeedCur" class="bar" style="background-color: rgb(255, 204, 51); width: 11.3%;"></div>
						</div>
					</td>
					<td class="nums">
						<span id="prnSpeedCur">113</span>타/분
					</td>
				</tr>
				<tr>
					<th>최고 속도</th>
					<td>
						<div class="barBgcolor">
							<div id="barSpeedMax" class="bar" style="background-color: rgb(204, 153, 51); width: 11.3%;"></div>
						</div>
					</td>
					<td class="nums">
						<span id="prnSpeedMax">113</span>타/분
					</td>
				</tr>
			</tbody></table></div></div>
			</div>

<script type="text/javascript">
//<![CDATA[

		var http = new Http();

		function Http(){
			this.arrStrs = new Array(
          <?
          for ($i=0; $i < count($typo); $i++) {
              if ( $i != 0 )
                  $strMake .= ",";
             $strMake .= "'". $typo[$i] . "'";
          }
          echo $strMake;
          ?>
      );

			this.exString="";
			this.inputString="";
			this.speedCur=0;
			this.speedMax=0;

			this.accuracyTotal=0; //정확도
			this.accuracyCur=0; //정확도

			this.lengthTotal=0; //전체 글자수
			this.lengthTotalTrue=0; //전체 맞은 글자수
			this.lengthCurTrue=0; //현재 맞은 글자수

			this.timerInt;
			this.timerStopped=true;
			this.timerSec=0;

			this.setHttp=function(){
				/*** 임의 문장 선택 ***/
				var idx = Math.floor(Math.random(1)*this.arrStrs.length);
				this.exString = this.arrStrs[idx];

				/*** 문장/입력 객체 가져오기***/
				var objInputString = this.obj("httpInputString");
				var objExString = this.obj("exString");

				/*** 타이머 초기화 ***/
				this.timerStopped=true;
				this.timerInt=window.clearInterval(http.timerInt);
				this.timerSec=0;

				/*** 문장 초기화 ***/
				objExString.innerHTML=this.exString;
				objInputString.value="";
				objInputString.focus();
			}
			this.keyUp=function(){
				var objInputString = this.obj("httpInputString");

				this.chkMiss();

				/*** 다른 문장으로 넘김 ***/
				if(this.exString.length<=objInputString.value.length){

					/*** 정확도 계산/출력 ***/
					this.lengthTotal += this.exString.length;
					this.lengthTotalTrue += this.lengthCurTrue;

					this.accuracyCur = Math.floor(this.lengthCurTrue/this.exString.length*100);
					this.accuracyTotal = Math.floor(this.lengthTotalTrue/this.lengthTotal*100);
					this.obj("prnAccuracyCur").innerHTML=this.accuracyCur;
					this.obj("prnAccuracyTotal").innerHTML=this.accuracyTotal;
					this.obj("barAccuracyCur").style.width=this.accuracyCur+"%";
					this.obj("barAccuracyTotal").style.width=this.accuracyTotal+"%";

					/*** 속도 계산/출력 ***/
					this.speedCur = Math.floor(this.lengthCurTrue / this.timerSec * 6000);
					if(this.speedMax<this.speedCur)this.speedMax = this.speedCur;
					this.obj("prnSpeedCur").innerHTML = this.speedCur;
					this.obj("prnSpeedMax").innerHTML = this.speedMax;
					this.obj("barSpeedCur").style.width=this.speedCur/10+"%";
					this.obj("barSpeedMax").style.width=this.speedMax/10+"%";

          /** 결과를 ajax로 저장 ***/
          $.ajax({
            type: 'POST',
            url: './ajax_post.php',
            data: {
                'mb_id': encodeURIComponent("<?php echo $member['mb_id']?>"),
                'prnAccuracyCur' : this.obj('prnAccuracyCur').innerHTML,
                'prnSpeedCur' : this.obj('prnSpeedCur').innerHTML
            },
            cache: false,
            async: false,
            success: function(result) {
                    if (result == '-9999') {
                        alert('오늘의 횟수를 초과 하셨습니다')
                        location.href = G5_URL;
                    } else if (result == '-8888') {
                        alert( ' 회원가입 하시면 포인트 적립이 가능 합니다' );
                    } else {
                        alert( result + ' 포인트를 획득하셨습니다' );
                    }
                }
          });
					this.setHttp();

					return false;
				}
				return true;
			}
			this.obj=function(id){
				return document.getElementById(id);
			}
			this.chkMiss=function(){
				var result="";
				this.lengthCurTrue=0;

				var objInputString = this.obj("httpInputString");
				this.inputString = objInputString.value;

				for(var i=0;i<this.exString.length;i++){
					if(this.exString.substring(i,i+1)!=this.inputString.substring(i,i+1) && i<this.inputString.length)
						result+="<span style='color:red'>"+this.exString.substring(i,i+1)+"</span>";
					else{
						result+=this.exString.substring(i,i+1);
						this.lengthCurTrue++;
					}
				}
				var objExString = this.obj("exString");
				objExString.innerHTML=result;
			}
			this.chkTime=function(){
				if(this.timerStopped){
					this.timerStopped=false;
					this.timerSec=0;
					this.timerInt=window.setInterval("http.addSec()",10);
				}
			}
			this.addSec=function(){
				this.timerSec++;
			}
		}

	</script>

<?php
include_once(G5_PATH.'/_tail.php');
