<?
/*#########################################
# 시스템명 : 스마트보고 시스템
# 작 성 일 : 2021.04.05
# 파 일 명 : wb_sche_add_ok.php
# 기능설명 : 일정등록 처리
#########################################*/

set_time_limit(0);

// 첨부파일 경로 및 홈페이지 정보
include "./include/env.inc.php";

// 통합인증처리
include "./include/function.inc.php";

// 전달변수 처리
$sEmpno = getRequestString("empno");
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
$sReDate = 1;
if(strlen($sRep_date2) > 0){
  $sReDate = intval((strtotime(str_replace('－','-',$sRep_date2)) - strtotime(str_replace('－','-',$sRep_date1))) / 86400)+1;
}

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
$sScYn = getRequestString("scYn");
$sGubun = getRequestString("gubun"); //1. 요청 2. 반려 3. 확정

// DB 접속
include ("./include/oci8.inc.php");
// 공통변수 처리 
include "./include/libutil.inc.php";

// 인증정보
include "./include/sso_auth.inc.php";
if ($sTitle != "" && $sEmpno != "") {
	$sTable_name01 = "KOSPOWB_REPORT";
	$sTable_name02 = "KOSPOWB_FILE";

	$sRep_time1 = $sRep_hour1.":".$sRep_min1.":00";
	$sRep_time2 = $sRep_hour2.":".$sRep_min2.":00";
    
    $sQuery		= "select max(to_number(repid)) from " . $sTable_name01;
	$oci->parseExec($sQuery);
	$sRow = $oci -> fetch();

	if(is_null($sRow)) {
		$newIdx		= 1;
	}else{
		$newIdx		= $oci -> result(1)+1;
	}
    	
    for($i=0; $i < $sReDate; $i++){        
    	$qry_add = "INSERT INTO " . $sTable_name01 . " (repid,empno,title,username,dept_name,grade_name,rep_empno,bogo_empno,rep_date1,rep_time1,rep_date2,rep_time2,rep_content,open_yn,gubun,stats,sc_yn) 
    	VALUES ($newIdx+$i,'$sEmpno','$sTitle','$sUsername','$sDept_name','$sGrade_name','$sRep_empno','$sBogo_empno','".check_day($sRep_date1,$i)."','$sRep_time1',null,'$sRep_time2'
    	,'$sRep_content','$sOpenYn','$sPgubun','0','$sScYn')";
    	
    	$oci -> parseExec($qry_add);
    	
    	$oci -> parseFree();
		
    	// 파일첨부 처리
    	$sFileDir = $_UPLOAD_PATH;
    	$nMax_size = 150 * 1048576; //(1024*1024)
    	$nPrevFileSize = $_POST["prevfile"];
    	$nCrntFileSize = 0;
    
    	if (is_uploaded_file($_FILES['upfile']['tmp_name'])) {
    		$sUploadedFile = str_replace("'","`",$_FILES['upfile']['name']);
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
    	
    		$nCrntFileSize = $nPrevFileSize + $_FILES['upfile']['size'];
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
    			$sUpfile = upload_file_svr($sFileDir, $_FILES['upfile'], $sUploadedFile, $_FILES['upfile']['tmp_name'],$nSeqFile,$sEmpno,$sRgstDate);
    		}
    	} else {
    		$sUpfile = "";
    	}
    
    	if ($sUpfile != "") {
    		$nFileSeq = 1;
    		$sSavedFile	= $sUpfile;
    		$qry_file = "INSERT INTO " . $sTable_name02 . " (fileid,seq,wbid,upload_name,saved_name,gubun) VALUES ($nWbFileid, $nFileSeq, $newIdx+$i, '$sUploadedFile', '$sSavedFile', 'B')";
    		$oci -> parseExec($qry_file);
    		$oci -> parseFree();
    	}
    }
    if($sScYn == 'N'){
	    $sMsg = substr($sRep_date1,6,2)."월 ".substr($sRep_date1,10,2)."일 ".substr($sRep_time1,0,5)."~".substr($sRep_time2,0,5)." 스마트보고가 등록되었습니다.";
		
	    $mobileNo = getMobileNo('18830019');
	    sms_send($mobileNo,$sMsg);	
	}
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
