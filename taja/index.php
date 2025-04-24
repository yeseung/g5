<?php
include_once('./_common.php');

$g5['title'] = "게임";
include_once(G5_THEME_PATH.'/head.php');
?>

  <div class="mb-3">Time: <span id="timerr">30</span></div>

  <h3 id="test" class="mx-auto mt-5 mb-5 pt-3 pb-3 text-center bg-info text-white"></h3>
  <div class="">
    <form class="form">
      <input type="text" id="t1" class="form-control" onkeydown="setTimeout(abc, 50)"  placeholder="Type the word..."  autofocus autocomplete="off"  />
      </form>
    </div>

    <div id="hidden"></div>

    <h4 class="mt-2 text-center mt-3 pt-2 mb-3 pb-2 bg-success text-white" id="counttext">정답 : &nbsp;<span id="count">0</span></h4>

    <div class="mt-2 text-center"><a class="bth-restart" href="./"><button class="btn btn-primary">다시하기</button></a></div>


<script src="./js/script.js"></script>
<script>
  window.addEventListener('load', start);
</script>
<?php
  include_once(G5_THEME_PATH.'/tail.php');
