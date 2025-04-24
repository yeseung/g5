<?php
/**
 * skin file : /theme/THEME_NAME/skin/connect/basic_ipinfo/current_connect.skin.html.php
 */
if (!defined('_EYOOM_')) exit;

//==============================================================================
// 관리자 국가,IP정보 기본 출력
//------------------------------------------------------------------------------
$token = 'ACCESS_TOKEN';	// ipinfo.io 개인 액세스 토큰
$is_flag = false;			// 국가 출력여부 설정
$is_ipInfo = false;			// IP정보 출력여부 설정

function getIpInfo($ip, $token = null)
{
	global $is_admin;

	// 로컬 IP 처리
    if ($ip === '127.0.0.1' || $ip === '::1') {

		$info = [
            'ip' => $ip,
            'hostname' => 'localhost',
            'city' => 'N/A',
            'region' => 'N/A',
            'country' => 'N/A',
            'flag_url' => 'https://flagcdn.com/w80/un.png',
            'loc' => 'N/A',
            'postal' => 'N/A',
            'org' => 'N/A',
            'timezone' => 'N/A',
            'asn' => 'N/A',
            'ip_ranges' => 'N/A'
		];

		return $info;
    }

    $url = 'https://ipinfo.io/' . $ip . '?token=' . $token;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    if (!$data) {
        return "IP 정보를 가져오는 데 실패했습니다.";
    }

	// IP 마스킹
    $data['ip'] = $is_admin ? $data['ip'] : preg_replace("/(\d+)\.(\d+)\.(\d+)\.(\d+)/", G5_IP_DISPLAY, $data['ip']);

    // 국가 코드 소문자로 변환
    $countryCode = strtolower($data['country'] ?? '');
    $flagUrl = $countryCode ? "https://flagcdn.com/w80/$countryCode.png" : 'https://flagcdn.com/w80/un.png';

    // IP 대역 정보 가져오기
    $ipRanges = getIpRanges($data['ip']);

    // 정보 배열로 분리하여 반환
    $info = [
        'ip' => $data['ip'],
        'hostname' => $data['hostname'],
        'city' => $data['city'],
        'region' => $data['region'],
        'country' => $data['country'],
        'flag_url' => $flagUrl,
        'loc' => $data['loc'],
        'postal' => $data['postal'],
        'org' => $data['org'],
        'timezone' => $data['timezone'],
        'asn' => $data['asn'],
        'ip_ranges' => displayIpRanges($ipRanges)
    ];

    return $info;
}

function getIpRanges($ip)
{
    $hierarchy = [];
    $ranges[] = getBaseIp($ip, 8) . "/8";
    $ranges[] = getBaseIp($ip, 16) . "/16";
    $ranges[] = getBaseIp($ip, 24) . "/24";


    $ranges[] = $ip; // 현재 IP 추가

    return $ranges;
}

function getBaseIp($ip, $prefix)
{
    $ipParts = explode('.', $ip);

    if ($prefix <= 8) {
        return "{$ipParts[0]}.0.0.0";
    } elseif ($prefix <= 16) {
        return "{$ipParts[0]}.{$ipParts[1]}.0.0";
    } elseif ($prefix <= 24) {
        return "{$ipParts[0]}.{$ipParts[1]}.{$ipParts[2]}.0";
    }

    return $ip;
}

function displayIpRanges($ipRanges)
{
    $output = implode(' > ', $ipRanges);
    return $output;
}
?>

<style>
.current-connect {font-size:.9375rem}
.current-connect .table-list-eb .td-location {white-space:normal;word-break:break-all;}
.current-connect .table-list-eb .td-mobile td {padding-top:0;color:#959595;white-space:normal;word-break:break-all;}
.current-connect .table-list-eb .td-mobile td a {color:#959595}
.current-connect .connect-nameview {color:#3949ab !important}
.current-connect .connect-nameview .sv_wrap > a {color:#3949ab}
.current-connect .connect-nameview .sv_wrap .profile_img {display:none}
.current-connect .connect-nameview .sv_wrap .sv {text-align:left;color:#151515}
.current-connect .connect-url:hover {color:#000;text-decoration:underline}
@media (max-width:767px) {
    .current-connect .table-list-eb table {white-space:inherit}
    .current-connect .table-list-eb .td-mlt td {border-bottom:0}
    .current-connect .table-list-eb .td-num {font-weight:700}
}

/* 접속자 IP정보 */
#ip_info_wrap {display: none;width: 100%;height: 100%;position: fixed;top: 0;left: 0;z-index: 10000;background: rgba(0, 0, 0, 0.5);backdrop-filter: blur(2px);animation: fadeIn 0.3s ease-in-out;}
#ip_info_wrap .ip_info {position: absolute;top: 50%;left: 50%;transform: translate(-50%, -50%);background: #ffffff;border-radius: 12px;box-shadow: 0 4px 24px rgba(0, 0, 0, 0.15);width: 90%;max-width: 600px;max-height: 85vh;overflow-y: auto;padding: 0;}
#ip_info_wrap .ip_info h3 {margin: 0;padding: 20px 24px;font-size: 1.5rem;font-weight: 600;color: #1a1a1a;border-bottom: 1px solid #e8ecef;}
#ip_info_wrap .ip_info_cls {position: absolute;right: 16px;top: 16px;background: none;border: none;cursor: pointer;width: 32px;height: 32px;display: flex;align-items: center;justify-content: center;transition: transform 0.2s ease;}
#ip_info_wrap .ip_info_cls:hover {transform: scale(1.1);}
#ip_info_wrap .ip_info_cls img {width: 20px;height: 20px;filter: invert(40%);}
#ip_info_wrap .cont_info_wrap {padding: 16px 24px;border-bottom: 1px solid #f1f3f5;font-size: 0.95rem;line-height: 1.6;}
#ip_info_wrap .cont_info_wrap:last-child {border-bottom: none;}
#ip_info_wrap .cont_info_wrap dl {display: flex;flex-wrap: wrap;align-items: flex-start;gap: 12px; margin:0px;}
#ip_info_wrap .cont_info_wrap dt {width: 100px;font-weight: 600;color: #495057;}
#ip_info_wrap .cont_info_wrap dd {flex: 1;margin: 0;color: #212529;word-break: break-word;}
#ip_info_wrap .block-ip {display: inline-block;padding: 6px 12px;border-radius: 6px;background: #ff4d4f;color: #fff;text-decoration: none;font-size: 0.85rem;transition: background 0.2s ease;}
#ip_info_wrap .block-ip:hover {background: #e63946;}
#ip_info_wrap #flag {vertical-align: middle;width: 24px;height: 16px;margin-left: 8px;border: 1px solid #dee2e6;border-radius: 2px;}
@keyframes fadeIn {
    from {opacity: 0;}
    to {opacity: 1;}
}
@media (max-width: 600px) {
    #ip_info_wrap .ip_info {width: 95%;max-height: 90vh;}
    #ip_info_wrap .ip_info h3 {font-size: 1.25rem;padding: 16px;}
    #ip_info_wrap .cont_info_wrap {padding: 12px 16px;font-size: 0.9rem;}
    #ip_info_wrap .cont_info_wrap dt {width: 80px;font-size: 0.9rem;}
    #ip_info_wrap .block-ip {padding: 5px 10px;font-size: 0.8rem;}
}
@media (max-width: 400px) {
    #ip_info_wrap .cont_info_wrap dt {width: 100%;margin-bottom: 4px;}
    #ip_info_wrap .cont_info_wrap dd {width: 100%;}
}
</style>

<div class="current-connect">
    <div class="table-list-eb">
        <div class="board-list-body">
            <table class="table">
                <thead>
                    <tr>
                        <th class="text-start width-60px">번호</th>
						<?php if($is_flag || $is_admin == 'super'){ ?>
						<th class="width-60px">국가</th>
						<?php } ?>
                        <th class="width-120px">접속자 (IP)</th>
                        <th class="hidden-xs">위치</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
					for ($i=0; $i<count((array)$list); $i++) {

						$ipInfo = getIpInfo($list[$i]['lo_ip'], $token);
						$data_ipInfo = htmlspecialchars(json_encode($ipInfo, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), ENT_QUOTES, 'UTF-8');
					?>
                    <tr class="td-mlt">
                        <td class="td-num text-start"><?php echo $list[$i]['num']; ?></td>
						<?php if($is_flag || $is_admin == 'super'){ ?>
						<td class="text-center"><img src='<?php echo $ipInfo['flag_url'];?>' alt='<?php echo $ipInfo['country'];?> flag' style='vertical-align:middle;border:1px solid #ccc;width:25px;height:auto;'></td>
						<?php } ?>
                        <td class="text-center">
                            <?php
							if ($list[$i]['mb_id']) {

								if($is_ipInfo || $is_admin == 'super'){
									echo '<span class="connect-nameview"><a href="#" class="crt_ipInfo" data-info="'.$data_ipInfo.'" data-bs-toggle="modal" data-bs-target=".search-modal">'.eb_nameview($eyoom['nameview_skin'], $list[$i]['mb_nick'], $list[$i]['mb_id'], $list[$i]['mb_email'], $list[$i]['mb_homepage']).'</a></span>';
								}else{
									echo '<span class="connect-nameview">'.eb_nameview($eyoom['nameview_skin'], $list[$i]['mb_nick'], $list[$i]['mb_id'], $list[$i]['mb_email'], $list[$i]['mb_homepage']).'</span>';
								}

							} else {
								echo ($is_ipInfo || $is_admin == 'super') ? '<span><a href="#" class="crt_ipInfo" data-info="'.$data_ipInfo.'" data-bs-toggle="modal" data-bs-target=".search-modal">'.$list[$i]['name'].'</a></span>':'<span>'.$list[$i]['name'].'</span>';
							}
							?>
                        </td>
                        <td class="hidden-xs td-location">
                            <?php if ($list[$i]['lo_url'] && $is_admin == 'super') { ?>
                            <a href="<?php echo $list[$i]['lo_url']; ?>" class="connect-url"><?php echo $list[$i]['lo_location']; ?></a>
                            <?php } else { ?>
                            <?php echo $list[$i]['lo_location']; ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr class="td-mobile visible-xs">
                        <td colspan="<?php echo ($is_flag || $is_admin == 'super')?"3":"2";?>">
                            <?php if ($list[$i]['lo_url'] && $is_admin == 'super') { ?>
                            <a href="<?php echo $list[$i]['lo_url']; ?>"><?php echo $list[$i]['lo_location']; ?></a>
                            <?php } else { ?>
                            <?php echo $list[$i]['lo_location']; ?>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php } ?>
                    <?php if (count((array)$list) == 0) { ?>
                    <tr><td colspan="4" class="text-center"><span class="text-gray"><i class="fas fa-exclamation-circle"></i> 현재 접속자가 없습니다.</span></td></tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php /* IP정보 모달 시작 */ ?>
<div id="ip_info_wrap">
    <div class="ip_info">
        <h3>접속자 IP 정보</h3>
        <button type="button" class="ip_info_cls">
            <img src="<?php echo EYOOM_THEME_URL;?>/skin/connect/basic_ipinfo/img/icon_close.svg" alt="닫기">
        </button>
        <div id="ip_info">
            <div class="cont_info_wrap">
                <dl>
                    <dt>IP 주소</dt>
                    <dd id="ip_address"></dd>
                </dl>
            </div>
			<?php if($is_admin){ ?>
            <div class="cont_info_wrap">
                <dl>
                    <dt></dt>
                    <dd>
                        <a href="#" class="block-ip" data-ip-type="full">차단: 전체 IP</a>
                        <a href="#" class="block-ip" data-ip-type="range">차단: IP 대역 <span id="block-ip-range"></span></a>
                    </dd>
                </dl>
            </div>
			<?php } ?>
            <div class="cont_info_wrap">
                <dl>
                    <dt>국가</dt>
                    <dd>
                        <span id="country"></span>
                        <img id="flag" src="" alt="국기">
                    </dd>
                </dl>
            </div>
            <div class="cont_info_wrap">
                <dl>
                    <dt>지역</dt>
                    <dd id="city"></dd>
                </dl>
            </div>
            <div class="cont_info_wrap">
                <dl>
                    <dt>지역 코드</dt>
                    <dd id="region"></dd>
                </dl>
            </div>
            <div class="cont_info_wrap">
                <dl>
                    <dt>위치</dt>
                    <dd id="loc"></dd>
                </dl>
            </div>
            <div class="cont_info_wrap">
                <dl>
                    <dt>시간대</dt>
                    <dd id="timezone"></dd>
                </dl>
            </div>
            <div class="cont_info_wrap">
                <dl>
                    <dt>ISP</dt>
                    <dd id="org"></dd>
                </dl>
            </div>
            <div class="cont_info_wrap">
                <dl>
                    <dt>Ranges</dt>
                    <dd id="ip_ranges"></dd>
                </dl>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
	// IP 정보 표시
    $(".crt_ipInfo").on("click", function(e) {
        e.preventDefault();
        var info = $(this).attr("data-info"); // data-info 값 가져오기
        try {
            info = JSON.parse(info); // JSON 문자열을 JavaScript 객체로 변환
            // 모달 내 요소 업데이트
            $("#ip_address").text(info.ip || "N/A");
            $("#country").text(info.country || "N/A");
            $("#flag").attr("src", info.flag_url || "");
            $("#city").text(info.city || "N/A");
            $("#region").text(info.region || "N/A");
            $("#loc").text(info.loc || "N/A");
            $("#timezone").text(info.timezone || "N/A");
            $("#org").text(info.org || "N/A");
            $("#ip_ranges").text(info.ip_ranges || "N/A");

			// IP 대역 계산 및 링크 텍스트 설정
            var ipAddress = info.ip || "";
            var ipRangeText = "N/A";
            if (ipAddress && ipAddress.split(".").length === 4) {
                var ipParts = ipAddress.split(".");
                ipRangeText = ipParts[0] + "." + ipParts[1] + ".+";
            }
            $("#block-ip-range").text(ipRangeText);

            $("#ip_info_wrap").show();
        } catch (err) {
            console.error("JSON 파싱 오류:", err);
            alert("IP 정보를 불러오는 데 실패했습니다.");
        }
    });

	<?php if($is_admin){ ?>
	// 차단 IP 등록
	$(".block-ip").on("click", function(e) {
		e.preventDefault();

		var ipAddress = $("#ip_address").text(); // 표시된 IP 주소 가져오기
		if (!ipAddress || ipAddress === "N/A") {
            alert("IP 주소가 없습니다.");
            return;
        }

		var ipType = $(this).data("ip-type");
		var ipToBlock;

		if (ipType === "full") {
			ipToBlock = ipAddress; // 전체 IP: 123.123.123.123
		} else if (ipType === "range") {
			// IP 대역: 123.123.*
			var ipParts = ipAddress.split(".");
			if (ipParts.length === 4) {
				ipToBlock = ipParts[0] + "." + ipParts[1] + ".+";
			} else {
				alert("유효하지 않은 IP 형식입니다.");
				return;
			}
		}

		// AJAX 요청
		$.ajax({
			url: "<?php echo EYOOM_THEME_URL;?>/skin/connect/basic_ipinfo/block_ip_update.php",
			type: "POST",
			data: {
				ip_to_block: ipToBlock
			},
			success: function(response) {
				if (response.success) {
					alert("IP가 차단 목록에 추가되었습니다.");
					$("#ip_info_wrap").hide(); // 차단 후 창 닫기 (선택 사항)
				} else {
					alert("IP 차단에 실패했습니다: " + response.message);
					$("#ip_info_wrap").hide(); // 실패시 창 닫기 (선택 사항)
				}
			},
			error: function() {
				alert("서버와의 통신에 실패했습니다.");
			},
			dataType: "json"
		});
	});
	<?php } ?>

	$(".ip_info_bg, .ip_info_cls").click(function () {
		$("#ip_info_wrap").hide();
	});
});
</script>
<?php /* IP정보 모달 끝 */ ?>