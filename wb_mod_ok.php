<?
/*#########################################
# 시스템명 : 스마트보고 			      
# 작 성 일 : 2021.04.05          
# 파 일 명 : wb_mod_ok.php    	 
# 기능설명 : 스마트보고 등록 수정처리
#########################################*/
// 첨부파일 경로 및 홈페이지 정보
include "./include/env.inc.php";
// 통합인증처리
include "./include/function.inc.php";

// 전달변수 처리
$sRepid = getRequestString("repid");
$sEmpNo = getRequestString("empno");
$sTitle = getRequestString("title");
$sUsername = getRequestString("username");
$sDept_name = getRequestString("dept_name");
$sGrade_name = getRequestString("grade_name");
$sBogo_empno = getRequestString("BogoEmpno");
$sRep_empno = getRequestString("approvalEmpno");
$sRep_date1 = getRequestString("sdate");
$sRep_hour1 = getRequestString("hour1");
$sPgubun = getRequestString("pgubun");

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
if(strlen($sPgubun) == 0){
  $sPgubun = 1;
}
$sRep_content = getRequestString("rep_content");
$sOpenYn = getRequestString("openYn");
$sFileYn = getRequestString("fileYn");
$sScYn = getRequestString("scYn");
$sGubun = getRequestString("gubun"); //1. 요청 2. 반려 3. 확정

// DB 접속
include ("./include/oci8.inc.php");
// 공통변수 처리 
include "./include/libutil.inc.php";
// 인증정보
include "./include/sso_auth.inc.php";
	
if ($sEmpNo != "" && $sRepid !="") {
	$sTable_name01 = "KOSPOWB_REPORT";
	$sTable_name02 = "KOSPOWB_FILE";	

	$qry_payuser = "SELECT a.USER_KNM as NAME";
	$qry_payuser .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD";
	$qry_payuser .= " 	   WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD";
	$qry_payuser .= " 	   ELSE b.org_nm || '장' END AS GRADE_NAME";
	$qry_payuser .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN a.USER_POSIT_CD ELSE a.USER_RPSWRK_CD END AS DPRT_NAME";
	$qry_payuser .= " FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd WHERE a.user_id = '$sBogo_empno'";


	$oci -> parseExec($qry_payuser);
	$ret3 = $oci -> fetch();
	if ($ret3 == 1) {
		$rPayName 			= $oci -> result(1);
		$rPstnName 			= $oci -> result(2);
		$rDprtName 			= $oci -> result(3);	
	}
	
	// 파일첨부 처리
	$sFileDir = $_UPLOAD_PATH;
	$nMax_size = 150 * 1048576; //(1024*1024)
	$nPrevFileSize = $_POST["prevfile"];
	$nCrntFileSize = 0;
	
	// 신고등록수정
	if($sRep_empno == ""){
		$sApprovalqry = "";		
	}else{
		$sApprovalqry = ", rep_empno='$sRep_empno'";
	}
	
	// 신고등록수정
	$sRep_time1 = $sRep_hour1.":".$sRep_min1.":00";
	$sRep_time2 = $sRep_hour2.":".$sRep_min2.":00";
	
	for($i=1; $i < 4; $i++){
    	if (is_uploaded_file($_FILES['upfile'.$i]['tmp_name'])) {
    		$sUploadedFile = str_replace("'","`",$_FILES['upfile'.$i]['name']);
    		// 파일첨부등록
    		$qry_fileid = "SELECT SEQ_KOSPOWB_FILE.NEXTVAL FROM DUAL";
    		$oci -> parseExec($qry_fileid);
    		if ($oci -> fetch()) {
    			$nWbFileid = $oci -> result(1);
    		} else $nWbFileid = 0;
    		$oci -> parseFree();
    		
    		$sRgstDate = date("Ymd");
    		$nFileSeq = 1;
    		$nSeqFile = $nWbid."_".$nWbFileid."_".$nFileSeq; // 신고번호_파일번호_순서
    		
    		$nCrntFileSize = $nPrevFileSize + $_FILES['upfile'.$i]['size'];
    		if ($nCrntFileSize > $nMax_size) {
    			alertMsg("파일 업로드 용량을 초과하였습니다.");
    			// 업로드된 파일 삭제처리
    			for ($d = 1; $d <= count($sUpFile); $d++) {
    				$delete_file = $sUpFile[$d];
    				if($delete_file != "") @unlink("$sFileDir$delete_file");
    			}
    			page_redirect ("javascript:history.back()");
    		} else {
    			$delete_file = "";
    			$sUpfile = upload_file_svr($sFileDir, $_FILES['upfile'.$i], $sUploadedFile, $_FILES['upfile'.$i]['tmp_name'],$nSeqFile,$sEmpNo,$sRgstDate);
    		}
    	} else {
    		$sUpfile = "";
    	}
    	$sDelFileId = getRequestNumber("delfile".$i);
    	if ($sUpfile != "") { // 이전 파일 삭제
    	    $qry_preFile = "SELECT nvl(MAX(seq),0)+1 FROM $sTable_name02 WHERE WBID=$sRepid AND GUBUN='B'";
    		$oci -> parseExec($qry_preFile);
    		if ($oci -> fetch()){
    			$nFileSeq = $oci -> result(1);
    		}
    		$sSavedFile	= $sUpfile;
    		$qry_file = "INSERT INTO " . $sTable_name02 . " (fileid,seq,wbid,upload_name,saved_name,gubun) VALUES ($nWbFileid, $nFileSeq, $sRepid, '$sUploadedFile', '$sSavedFile','B')";
    		$oci -> parseExec($qry_file);
    		$oci -> parseFree();
    	} else if ($sDelFileId != "") { // 이전 파일 삭제
    		$qry_preFile = "SELECT SAVED_NAME FROM $sTable_name02 WHERE FILEID=$sDelFileId AND GUBUN='B'";
    		$oci -> parseExec($qry_preFile);
    		if ($oci -> fetch()){
    			$delete_file = $oci -> result(1);
    			if($delete_file != "") @unlink("$sFileDir$delete_file");
    		}
    		$oci -> parseFree();
    		// 삭제
    		$qry_delFile = "DELETE FROM $sTable_name02 WHERE FILEID=$sDelFileId AND GUBUN='B'";
    		$oci -> parseExec($qry_delFile);
    		$oci -> parseFree();
    	}
	}
	
	$qry_mod = "UPDATE " . $sTable_name01 . " SET TITLE='$sTitle', USERNAME='$sUsername', DEPT_NAME='$sDept_name', BOGO_EMPNO = '$sBogo_empno', GRADE_NAME='$sGrade_name',
	REP_DATE1=substr('$sRep_date1',1,4)||'-'||substr('$sRep_date1',7,2)||'-'||substr('$sRep_date1',11,2), REP_TIME1='$sRep_time1', REP_DATE2='$sRep_date2', REP_TIME2='$sRep_time2', REP_CONTENT='$sRep_content', OPEN_YN ='$sOpenYn', FILE_YN ='$sFileYn', STATS='0', reg_date=sysdate $sApprovalqry, gubun = '$sPgubun', bogo_name = '$rPayName', bogo_grade_name = '$rPstnName', bogo_dprt_name = '$rDprtName' WHERE repid= $sRepid ";
	$oci -> parseExec($qry_mod);
    $oci -> parseFree();
	
	$oci -> disconnect();
    $roci -> disconnect();
?>
<script>
	window.opener.location.href = window.opener.location.href;
	self.close();
</script>
<?
} else {
	alertMsg("필수정보가 누락되었습니다.");
	$oci -> disconnect();
    $roci -> disconnect();
	exit;
}	
?>