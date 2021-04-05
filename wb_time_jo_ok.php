<?
/*#########################################
# 시스템명 : 스마트보고 시스템
# 작 성 일 : 2021.04.05
# 파 일 명 : wb_time_jo_ok.php
# 기능설명 : 보고시간 변경처리
#########################################*/
// 첨부파일 경로 및 홈페이지 정보
include "./include/env.inc.php";
// 통합인증처리
include "./include/function.inc.php";
// DB 접속
include "./include/oci8.inc.php";
// 공통변수 처리 
include "./include/libutil.inc.php";
// 인증정보
include "./include/sso_auth.inc.php";
// 전달변수 처리

$sRepid = getRequestString("repid");
$sHour1 = getRequestString("hour1");
if(strlen($sHour1) == 1){
  $sHour1 = '0'.$sHour1;
}
$sMin1 = getRequestString("min1");
$Rep_empno = getRequestString("rep_empno");
if(strlen($sMin1) == 1){
  $sMin1 = '0'.$sMin1;
}

if ($sRepid !="") {
	$sTable_name01 = "KOSPOWB_REPORT";
	
	$qry_str = "SELECT COUNT(*) FROM " . $sTable_name01 . " WHERE STATS = 'on' AND REP_EMPNO = '$Rep_empno' ";

	$oci -> parseExec($qry_str);
	$sIng = 0;
	if ($oci -> fetch()) {
		$sIng = $oci -> result(1);
	}
	
	$sMsg = "보고가 시작되었습니다.";
	if($sIng > 0){
		$sMsg = "진행중인 보고가 있습니다.";
	}
	
	if($sIng == 0){
		// 신고등록수정
		$sStats_date = $sHour1.":".$sMin1.":";
			
		$qry_mod = "UPDATE " . $sTable_name01 . " SET STATS = 'on' , STATS_DATE='$sStats_date'||TO_CHAR(SYSDATE,'SS') WHERE repid= $sRepid ";
		$oci -> parseExec($qry_mod);
	}
	
	$oci -> parseFree();
	$oci ->disconnect();
	$roci -> disconnect();
	//$sUrl = "http://pws.kospo.co.kr/kospowb/report/wb_time_jo.php?repid=$sRepid";
	//alertMsg_move($sMsg, $sUrl);
?>
<script>
	alert('<?=$sMsg;?>');
	self.close();
</script>
<?
} else {
	alertMsg("필수정보가 누락되었습니다.");
	exit;
}	
?>
