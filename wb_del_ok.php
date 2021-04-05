<?
/*#########################################
# 시스템명 : 스마트보고 시스템  
# 작 성 일 : 2021.04.05       
# 파 일 명 : wb_del_ok.php    
# 기능설명 : 스마트 보고 삭제처리
#########################################*/

// 첨부파일 경로 및 홈페이지 정보
include "./include/env.inc.php";
// 공통헤더
include "./include/head_inc.php";
// 통합인증처리
include "./include/function.inc.php";
// DB 접속
include "./include/oci8.inc.php";
// 공통변수 처리 
include "./include/libutil.inc.php";
// 인증정보
include ("./include/sso_auth.inc.php");

$sRepid = getRequestString("repid");

$sTable_name01 = "KOSPOWB_REPORT";
$sTable_name02 = "KOSPOWB_FILE";
$sTable_EAI    = "T_ALARM_SEND";

$sFileDir = $_UPLOAD_PATH;

	$Back_qry = "SELECT EMPNO FROM KOSPOWB_REPORT WHERE REPID='$sRepid' AND GUBUN= '0' AND SC_YN = 'N' ";
	$oci -> parseExec($Back_qry);
	$ret = $oci -> fetch();
    if ($ret == 1) {
		$sMsg = "승인자가 승인 하였습니다.";
		$sUrl = "http://pws.kospo.co.kr/pwshome/smart2/wb_list.php";
		alertMsg_move($sMsg, $sUrl);
		exit;
	}
	$qry_preFile = "SELECT SAVED_NAME FROM $sTable_name02 WHERE WBID=$sRepid AND GUBUN='B'";
	$oci -> parseExec($qry_preFile);
	if ($oci -> fetch()){
		$delete_file = $oci -> result(1);
		if($delete_file != "") @unlink("$sFileDir$delete_file");
	}
	$oci -> parseFree();
	// 삭제
	$qry_delFile = "DELETE FROM $sTable_name02 WHERE WBID=$sRepid AND GUBUN='B'";
	$oci -> parseExec($qry_delFile);
		
	$qry_update = "DELETE FROM " . $sTable_name01 . " WHERE REPID=$sRepid";																			  
	$oci -> parseExec($qry_update);
	
	$oci -> parseFree();	
    $oci -> disconnect();
    $roci -> disconnect();
?>
<script>
	// opener.location.reload();
	window.opener.location.href = window.opener.location.href;
	self.close();
</script>

