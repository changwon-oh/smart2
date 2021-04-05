<?
/*#########################################
# 시스템명 : 스마트보고 시스템
# 작 성 일 : 2021.04.05
# 파 일 명 : wb_time_mod_ok.php
# 기능설명 : 보고시간 변경처리
#########################################*/
// 첨부파일 경로 및 홈페이지 정보
include "./include/env.inc.php";
// 통합인증처리
include "./include/function.inc.php";

// 전달변수 처리
$sRepid = getRequestString("repid");
$sRep_date1 = getRequestString("sdate");
$sRep_hour1 = getRequestString("hour1");
if(strlen($sRep_hour1) == 1){
  $sRep_hour1 = '0'.$sRep_hour1;
}
$sRep_min1 = getRequestString("min1");
if(strlen($sRep_min1) == 1){
  $sRep_min1 = '0'.$sRep_min1;
}
$sRep_date2 = getRequestString("edate");
$sRep_hour2 = getRequestString("hour2");
if(strlen($sRep_hour2) == 1){
  $sRep_hour2 = '0'.$sRep_hour2;
}
$sRep_min2 = getRequestString("min2");
if(strlen($sRep_min2) == 1){
  $sRep_min2 = '0'.$sRep_min2;
}
$sRep_content = getRequestString("rep_content");
$sGubun = getRequestString("gubun"); //1. 요청 2. 반려 3. 확정

// DB 접속
include ("./include/oci8.inc.php");
// 공통변수 처리 
include "./include/libutil.inc.php";
// 인증정보
include "./include/sso_auth.inc.php";   
if ($sEmpno != "" && $sRepid !="") {
	$sTable_name01 = "KOSPOWB_REPORT";
	
	// 신고등록수정
	if($sRep_empno ==""){
		$sApprovalqry = "";		
	}else{
		$sApprovalqry = ", rep_empno='$sRep_empno'";
	}
	
	// 신고등록수정
	$sRep_time1 = $sRep_hour1.":".$sRep_min1.":00";
	$sRep_time2 = $sRep_hour2.":".$sRep_min2.":00";
	
	$qry_mod = "UPDATE " . $sTable_name01 . " SET REP_TIME1='$sRep_time1', REP_TIME2='$sRep_time2', REP_DATE1 = substr('$sRep_date1',1,4)||'-'||substr('$sRep_date1',7,2)||'-'||substr('$sRep_date1',11,2) WHERE repid= $sRepid ";

	$oci -> parseExec($qry_mod);
	$oci -> parseFree();
	
	$qry_mobile = "SELECT EMPNO, BOGO_EMPNO, REP_EMPNO, REP_DATE1, REP_TIME1, REP_TIME2, GUBUN FROM $sTable_name01 WHERE REPID = '$sRepid' ";
    $oci -> parseExec($qry_mobile);
    $ret = $oci -> fetch();
    if ($ret == 1) {
		$rEmpno 			= $oci -> result(1);
    	$rBogoEmpno 		= $oci -> result(2);
		$rRepEmpno	 		= $oci -> result(3);
    	$rRepDate1 			= $oci -> result(4);
    	$rRepTime1 			= $oci -> result(5);
    	$rRepTime2 			= $oci -> result(6);
		$rGubun 			= $oci -> result(7);
    }
	
    $oci -> parseFree();
	
	
	$oci ->disconnect();
	$roci -> disconnect();
	?>
	<script>
		window.opener.location.href = window.opener.location.href;
		self.close();
	</script>
	<?
} else {
	alertMsg("필수정보가 누락되었습니다.");
	$oci -> parseFree();
	$oci ->disconnect();
	exit;
}	
?>
