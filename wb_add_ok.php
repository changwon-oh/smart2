<?
/*#########################################
# 시스템명 : 스마트보고 시스템      	        			#
# 작 성 일 : 2021.04.05                    #
# 파 일 명 : wb_add_ok.php                	    #
# 기능설명 : 스마트 보고 등록처리        			                #
#########################################*/
set_time_limit(0);
// 공통정보
include ("./include/env.inc.php");
// Function
include ("./include/function.inc.php");

// 전달변수 처리처
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
$sGubun = getRequestString("gubun"); //1.요청  2. 반려 3. 확정


// DB 접속
include ("./include/oci8.inc.php");
include ("./include/libutil.inc.php");
// SSO 인증정보
include ("./include/sso_auth.inc.php");
if ($sTitle != "" && $sEmpno != "") {
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
	
	$qry_add = "INSERT INTO " . $sTable_name01 . " (repid,empno,title,username,dept_name,grade_name,rep_empno,bogo_empno,rep_date1,rep_time1,rep_date2,rep_time2,rep_content,open_yn,file_yn,gubun,stats,sc_yn,bogo_name,bogo_grade_name,bogo_dprt_name) 
	VALUES ($newIdx,'$sEmpNo','$sTitle','$sUsername','$sDept_name','$sGrade_name','$sRep_empno','$sBogo_empno',substr('$sRep_date1',1,4)||'-'||substr('$sRep_date1',7,2)||'-'||substr('$sRep_date1',11,2),'$sRep_time1','$sRep_date2','$sRep_time2'
	,'$sRep_content','$sOpenYn','$sFileYn','$sPgubun','0','$sScYn','$rPayName','$rPstnName','$rDprtName')";
	$oci -> parseExec($qry_add);
	$oci -> parseFree();
	
	// 파일첨부 처리
	$sFileDir = $_UPLOAD_PATH;
	$nMax_size = 150 * 1048576; //(1024*1024)
	$nPrevFileSize = $_POST["prevfile"];
	$nCrntFileSize = 0;
    
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
    		$nSeqFile = $nWbid."_".$nWbFileid."_".$nFileSeq; // 신고번호_파일번호_순서­
    	
    		$nCrntFileSize = $nPrevFileSize + $_FILES['upfile'.$i]['size'];
    		if ($nCrntFileSize > $nMax_size) {
    			alertMsg("파일 업로드 용량을 초과하였습니다.");
    			// 업로드된 파일 삭제처리삭제처
    			for ($d = 1; $d <= count($sUpFile); $d++) {
    				$delete_file = $sUpFile[$d];
    				if($delete_file != "") @unlink("$sFileDir$delete_file");
    			}
    			page_redirect ("javascript:history.back()");
    		} else {
    			$delete_file = "";
    			$sUpfile = upload_file_svr($sFileDir, $_FILES['upfile'.$i], $sUploadedFile, $_FILES['upfile'.$i]['tmp_name'],$nSeqFile,$sEmpno,$sRgstDate);
    		}
    	} else {
    		$sUpfile = "";
    	}
    
    	if ($sUpfile != "") {
    		$nFileSeq = $i;
    		$sSavedFile	= $sUpfile;
    		$qry_file = "INSERT INTO " . $sTable_name02 . " (fileid,seq,wbid,upload_name,saved_name,gubun) VALUES ($nWbFileid, $nFileSeq, $newIdx, '$sUploadedFile', '$sSavedFile', 'B')";
    		$oci -> parseExec($qry_file);
    		$oci -> parseFree();
    	}
	}
    
	$oci -> disconnect();
   	$roci -> disconnect();
?>
<script>
	window.opener.location.href = window.opener.location.href;
	// opener.location.replace("http://http://scal.daewooenc.com/smart2/");
	// window.opener.parent.location.href='http://scal.daewooenc.com/smart2/wb_list.php';
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