<?
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

$sTable_name01 = "KOSPOWB_REPORT";
$sTable_name02 = "KOSPOWB_REPORT_ADMIN";
$sRepid = getRequestString("sRepid");
$rep_empno = getRequestString("rep_empno");
$procGubun = getRequestString("procGubun");

if($procGubun == "startChk"){
    $qry_add = "UPDATE $sTable_name01 SET END_DATE = TO_CHAR(SYSDATE,'HH24:MI:SS'), STATS = 'off' WHERE substr(rep_date1,1,4)||substr(rep_date1,6,2)||substr(rep_date1,9,2) < to_char(sysdate,'YYYYMMDD') AND STATS = 'on' AND REP_EMPNO = '$rep_empno' ";
} else if($procGubun == "listProc"){
    $sType = $_POST['sType'];
    $typeTxt = "STATS_DATE";
    $sStats = "on";
    if($sType == 'E'){
	$typeTxt = "END_DATE";
        $sStats = "off";
    }

    $qry_add = "UPDATE $sTable_name01 SET $typeTxt = TO_CHAR(SYSDATE,'HH24:MI:SS'), STATS = '$sStats' WHERE REPID = '$sRepid' ";
} else if($procGubun == "dpChk"){
    $sDqChk = getRequestString("dqChk");  
    $qry_add = "UPDATE $sTable_name01 SET DQ_YN = '$sDqChk' WHERE REPID = '$sRepid' ";
} else if($procGubun == "proc"){
    $sGubun = getRequestString("sGubun");
    $sReason = getRequestString("sReason");
    if($sGubun == '0'){
    	$typeTxt = "GUBUN = 0, REP_DATE2= TO_CHAR(SYSDATE,'YYYY-MM-DD HH24:MI')";
    } else if($sGubun == '2'){
    	$typeTxt = "GUBUN = 2 ,REASON = '$sReason'";
    }
    $qry_add = "UPDATE $sTable_name01 SET $typeTxt WHERE REPID = '$sRepid' ";
} else if($procGubun == "manage"){
    $sRepNo= $_POST['rep_no'];
    $sMan1No = $_POST['man1_no'];
    $sMan2No = $_POST['man2_no'];
    $sMainNo = $_POST['main_no'];
    $sScYn = $_POST['useYn'];
    $qry_add =  " DELETE $sTable_name02 ";    
}

//echo $qry_add; 
$oci -> parseExec($qry_add);
	
$oci -> parseFree();

// KOSPOWB_REPORT_ADMIN 관리자 등록
if($procGubun == "manage"){
    for($i=0; $i < count($sRepNo); $i++){
        $sQuery		= "select max(to_number(repid)) from " . $sTable_name02;
        $oci->parseExec($sQuery);
        $sRow = $oci -> fetch();
        
        if(is_null($sRow)) {
        	$newIdx		= 1;
        }else{
        	$newIdx		= $oci -> result(1)+1;
        }
        $oci -> parseFree();
        // INSERT INTO KOSPOWB_REPORT_ADMIN VALUES (2, '1001177', '11111111','11111112','11111113','','Y',SYSDATE);
        $qry_add =  " INSERT INTO $sTable_name02 VALUES ($newIdx, '$sEmpno', '".($sRepNo[$i]=='null'?'':$sRepNo[$i])."','".($sMan1No[$i]=='null'?'':$sMan1No[$i])."','".($sMan2No[$i]=='null'?'':$sMan2No[$i])."','".($sMainNo[$i]=='null'?'':$sMainNo[$i])."','$sScYn[$i]',SYSDATE) ";
        $oci -> parseExec($qry_add);
    	
    	$oci -> parseFree();
    }
} else if($procGubun == "proc"){
    $qry_add = "SELECT EMPNO, BOGO_EMPNO, REP_EMPNO, REP_DATE1, REP_TIME1, REP_TIME2 FROM $sTable_name01 WHERE REPID = '$sRepid' ";
    $oci -> parseExec($qry_add);
    $ret = $oci -> fetch();
    if ($ret == 1) {
		$rEmpno 			= $oci -> result(1);
		$rBogoEmpno 		= $oci -> result(2);
    	$rRepEmpno 			= $oci -> result(3);
    	$rRepDate1 			= $oci -> result(4);
    	$rRepTime1 			= $oci -> result(5);
    	$rRepTime2 			= $oci -> result(6);
    }
    $oci -> parseFree();
    
    if($sGubun == '0'){
        $sMsg = substr($rRepDate1,6,2)."월 ".substr($rRepDate1,10,2)."일 ".substr($rRepTime1,0,5)."~".substr($rRepTime2,0,5)." 스마트보고가 확정되었습니다.";
    } else {
        $sMsg = substr($rRepDate1,6,2)."월 ".substr($rRepDate1,10,2)."일 ".substr($rRepTime1,0,5)."~".substr($rRepTime2,0,5)." 스마트보고가 반려되었습니다. 반려사유:".$sReason;
    }	
}


$oci -> disconnect();
$roci -> disconnect();

exit;
?>
