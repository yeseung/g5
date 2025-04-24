<?
$_POST['cha_evr'];
$_POST['cha_best'];
$_POST['cha_sum'];
$_POST['tries'];
$_POST['row'];
$_POST['res'];
$_POST['pass'];
$_POST['d'];
$_POST['q'];

$_GET['cha_evr'];
$_GET['cha_best'];
$_GET['cha_sum'];
$_GET['tries'];
$_GET['row'];
$_GET['res'];
$_GET['pass'];
$_GET['d'];
$_GET['q'];

ob_start();
//세션등록
session_start();
//평타
$_SESSION['cha_evr'];
//최고타
$_SESSION['cha_best'];
//총타수
$_SESSION['cha_sum'];
//시도
$_SESSION['tries'];
//연달아 100점 넘을때
$_SESSION['row'];
//시간측정
$_SESSION['res'];
//엔터만치는 횟수
$_SESSION['pass'];
//이전단어
$_SESSION['d'];
//총점
$_SESSION['q'];
$z=$d;


echo("$d ");
//단어화일 읽음
$dat=file("say.log");
//난수발생
srand((double)microtime()*1000000);
//배열크기 계산
$end=sizeof($dat);
$randval=rand(1,$end);
//단어추출
$d=$dat[$randval];
//공백지우고
$d=trim($d);
//도트이후 없애고 - 버그때문에 --;
// explode()문장이 뒤로 가야 실행됨. 
// $d = str_replace('"', '', $d); //따옴표 변환
// $d = str_replace('"', '&#34;', $d); //따옴표 변환
// $d = str_replace('"', '', $d); //따옴표 변환
// $d = str_replace('?', '', $d); // ? 없애
// $d = str_replace('!', '', $d); // ! 없애
// $d = str_replace('.', '', $d); // . 없애
// $d = str_replace(',', '', $d); // , 없애

// $d = preg_replace('/\s*(\d) \s*/', "", $d); // 숫자만 제거
// $d = preg_replace('/\s*(\d\d) \s*/', "", $d); // 숫자만 제거
// $d = preg_replace('/"/', '', $d); // "만 제거
// $d = preg_replace("/'/", "", $d); // '만 제거
// $d = preg_replace("/;/", "", $d); // ;만 제거
// $d = preg_replace("/-/", "", $d); // -만 제거

$d=explode(".",$d);
$d=$d[0];
?>

<a href=taja.php?$PHP_SELF><img src="./taja.gif" border=/"0/"></a><br>
<?
//문장출력
echo("행: $randval /$end <h4>$d</h4> <h4>$d</h4> <h4>$d</h4>");
// echo("행: $randval /$end <h4>$d</h4>");
?>

<!DOCTYPE html PUBLIC "-//w3c//dtd html 4.0 transitional//en">
<HTML><head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<TITLE>슬기 타자</TITLE>
<script type="text/javascript">

	function focusIt() {
		document.sulki.message.focus();
		return true;
	}

</script>

<style type="text/css">

BODY, TR, TH, TD {
 font-size : 10pt;
}
</style>
</HEAD>
<BODY BGCOLOR="#33ffcc" onload="focusIt()"> 



<? echo("<form method=post action=taja.php?$PHP_SELF?name=sulki>"); ?>
<input type=text size=80 name=message>
<a href="./taja.phps">소스보기</a> <? echo("<a href=taja.php?$PHP_SELF?die=1>그만두기</a>");?>
</form>
</body>
</html>
<?
//이름새김
if ($die=1) {
	if($q>3000) {
	echo("<form method=post action=taja.php?$PHP_SELF>");
	echo("이름<input type=text size=10 name=irum>");
	echo("<input type=hidden name=add value=1>");
	echo("<input type=hidden name=q value=$q>");
	echo("<input type=submit value=등록>");
	echo("<input type=hidden name=cha_evr value=$cha_evr>");
	echo("<input type=hidden name=cha_best value=$cha_best>");
	echo("</form>");
	}
	else if($q<3000) { echo("수고하셨습니다. 당시의 점수는 $q <a href=./taja.php>돌아가기</a>"); }
	session_destroy();
	exit;
}
//엔터를 여러번치면 끝냄
if ($pass>8) {
	echo("게임오버 당신의 점수는 $q");
	session_destroy();
	exit;
}
//이름저장
if ($irum) {
$day=date(y);
$mon=date(m);
$d=date(d);
$a=date(a);
if ($a=='am') {
$a=오전;
}
else
$a=오후;
$h=date(h);
$c=date(i);
	$fw=fopen("taja.dat","a");
	if($q<10000) {
	$g='1';
	}
	if($q>10000) {
	$g='2';
	}
	fwrite($fw,"\r\n<tr><td>${g}</td><td>${q}</td><td>$cha_evr</td><td>$cha_best</td><td> ${irum}</td><td>");
	fwrite($fw,"$day $mon $d $a ${h}시 ${c}분</td><td></tr>");
	session_destroy();
	echo("저장되었습니다. <a href=taja.php?$PHP_SELF>돌아가기</a>");
	exit;

}
//처음인경우 시간생성
if(!$res) {
	$res=mktime();
}
//서브밋 시간 계산
else if($res) {
	$res2=mktime();
	$cha=$res2-$res;
echo("시간1: $res, 시간2: $res2");
	$res=$res2;
}

echo(" 시간차는 $cha<p>");
if($q) {
$tries++;
}
if($message) {
$pass=0;
//입력시 따옴표등에서 슬래시를 없애고
$message=stripslashes($message);
//문장과, 입력을 단어구분해서 나눔
$e1=explode(" ",$z);
$e2=explode(" ",$message);
$l=sizeof($e1);
$l2=sizeof($e2);
//문장과 입력 비교
for($i=0;$i<$l;$i++) {
	$i2=$i-1;
	echo("<br>$i:$e1[$i] -> $i:$e2[$i] ");

	if($e1[$i]==$e2[$i]) {
	$m++;
	echo("ok");
	}
}
echo("<p>");
//입력치 정확도 출력
$p=$m/$l*100;
$p=floor($p);
}
//일치도가 100이면
if($p=='100') {
$row++;
	if($row>1) {
//연달이 100이 나오면
	$c=$row*50;
		echo("<b>$row hit 콤보</b> 콤보점수:$c ");
		$p+=50;
	}
}
else if($p!='100') {
//콤보실패시
$row=0;
}
if(!$message) {
	$p=$p-10;
	$row=0;
	$pass++;
//처음 방문시 스코어 출력
	if($tries<1) {
		echo("3000점이상 하이스코어러 리스트");
		if($view=='date') {
			echo(" <a href=taja.php?$PHP_SELF?view=null>성적순</a><p>");
			$data=file("taja.dat","r");
			echo("<table><tr><td>등급</td><td>점수</td><td>평타</td><td>최고타</td><td>이름</td><td>날짜</td></tr>");
			for($j=0;$j<sizeof($data);$j++) {
				echo $data[$j];
			}
			echo("</table>");
		}
		else if($view!='date') {
			echo(" <a href=taja.php?$PHP_SELF?view=date>날짜순</a><p>");
			$data=file("taja.dat","r");
			rsort($data);
			echo("<table><tr><td>등급</td><td>점수</td><td>평타</td><td>최고타</td><td>이름</td><td>날짜</td></tr>");
			for($j=0;$j<sizeof($data);$j++) {
				echo $data[$j];
			}
			echo("</table>");
		}
	exit;
	}
}
//시간차가 0일때 에러방지
if($cha<1) {
$cha=1;
}
//점수계산
$r=$p/$cha*20;
//짧은글 보상루틴 - 긴글칠수록 유리
$cha=4000/$cha;
$cha=$cha*($l2/$l);
if(!$message) {
	$cha=1;
}
$cha=floor($cha);
//긴글보상루틴
//$cha=$cha+($l2*15);
//평타계산루틴
$cha_sum+=$cha;
if($tries<1) {
	$tries=1;
}
if(!cha_sum) {
	$cha_sum=$cha;
}
if($tries>=1) {
	$cha_evr=$cha_sum/$tries;
}
if($tries<1) {
	$cha_evr=$cha;
}
$cha_evr=floor($cha_evr);
//최고타계산루틴
	if(!$cha_best) {
		$cha_best=$cha_evr;
	}
	if($cha_best<$cha) {
		$cha_best=$cha;
	}

$r=floor($r);
$q+=$r;
if($p>100) {
$p=100;
}
echo("속도:${cha}타 평타:${cha_evr} 최고타:${cha_best} 정확도:$p% 점수:${r}점 총점: $q <br> 시도: $tries 포기: $pass<p>문장:$z<br>입력:$message $p%");
exit;
?>
