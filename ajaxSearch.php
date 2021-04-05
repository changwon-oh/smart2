<?php
// 공통정보
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

$sSdate = charReplace($_POST['sdate']);
$sEdate = charReplace($_POST['edate']);
$sSerachGbn = charReplace($_POST['searchGbn']);
$sSerachTxt = charReplace($_POST['searchTxt']);
$sRep_empno = charReplace($_POST['repEmpno']);

$qry_list = " SELECT repid, rep_date1,rep_time1,rep_time2,title,rep_content, username, bogo_empno, (SELECT dprt_name FROM SMART_AUTH_SYNC WHERE sabun = bogo_empno) bogo_dprt_name,(SELECT name FROM SMART_AUTH_SYNC WHERE sabun = bogo_empno) bogo_name,(SELECT grade_name FROM SMART_AUTH_SYNC WHERE sabun = bogo_empno) bogo_grade_name, gubun,stats, grade_name, dept_name, stats_date, end_date, open_yn, file_yn, dq_yn, sc_yn, to_char(round((sysdate-to_date(to_char(sysdate,'YYYYMMDD')||replace(rep_time1,':',''),'YYYYMMDDHH24MISS'))*12*60*60,0)) ing_time, to_char(reg_date,'YYYY-MM-DD') regdate, empno, (SELECT SUBSTR(XMLAGG(XMLELEMENT(X,'#',FILEID||'@'||SEQ) ORDER BY SEQ).EXTRACT('//text()'),2) FROM KOSPOWB_FILE WHERE WBID = REPID AND GUBUN = 'B') fileid ";
$qry_list .= "  FROM $sTable_name01 A ";
$qry_list .= " WHERE rep_empno = '$sRep_empno' AND gubun = '0' AND (stats = 'off' OR DQ_YN = 'Y') ";
$qry_list .= "   AND substr(rep_date1,1,4)||substr(rep_date1,6,2)||substr(rep_date1,9,2) BETWEEN substr('$sSdate',1,4)||substr('$sSdate',7,2)||substr('$sSdate',11,2) AND substr('$sEdate',1,4)||substr('$sEdate',7,2)||substr('$sEdate',11,2) ";

if($sSerachGbn == ''){
$qry_list .= "   AND (title LIKE '%".$sSerachTxt."%' ";
    $qry_list .= "   OR ((SELECT name FROM SMART_AUTH_SYNC WHERE sabun = bogo_empno) LIKE '%".$sSerachTxt."%' ";
    $qry_list .= "    OR (SELECT name FROM SMART_AUTH_SYNC WHERE sabun = empno) LIKE '%".$sSerachTxt."%') ";
    $qry_list .= "   OR (SELECT COUNT(*) FROM KOSPOWB_FILE WHERE wbid = repid AND gubun = 'B' AND upload_name LIKE '%".$sSerachTxt."%') > 0) ";
} else if($sSerachGbn == '1'){
    $qry_list .= "   AND title LIKE '%".$sSerachTxt."%' ";
} else if($sSerachGbn == '2'){
    $qry_list .= "   AND ((SELECT name FROM SMART_AUTH_SYNC WHERE sabun = bogo_empno) LIKE '%".$sSerachTxt."%' ";
    $qry_list .= "    OR (SELECT name FROM SMART_AUTH_SYNC WHERE sabun = empno) LIKE '%".$sSerachTxt."%') ";
} else if($sSerachGbn == '3'){
    $qry_list .= "   AND (SELECT COUNT(*) FROM KOSPOWB_FILE WHERE wbid = repid AND gubun = 'B' AND upload_name LIKE '%".$sSerachTxt."%') > 0 ";
}
$qry_list .= " ORDER BY replace(rep_date1,'-','') desc, to_number(replace(stats_date,':','')) desc, reg_date desc ";
// echo($qry_list);
$oci -> parseExec($qry_list);
$resultArray = array();
while($col = $oci -> fetchInto()) {
  $arrayMiddle = array(
		"repid" => $col['REPID'], // 신고번호
		"rep_date1" => iconv("EUC-KR","UTF-8",$col['REP_DATE1']), // 보고날짜
		"rep_time1" => $col['REP_TIME1'], // 보고날짜
		"rep_time2" => $col['REP_TIME2'], // 보고시간
		"title" => viewReplace($col['TITLE']), // 보고제목
		"rep_content" => viewReplace($col['REP_CONTENT']), // 보고내용		
		"username" => $col['USERNAME'], // 신청자
		"bogo_empno" => $col['BOGO_EMPNO'], // 보고자
		"bogo_name" => $col['BOGO_NAME'], // 보고자명
		"bogo_grade_name" => $col['BOGO_GRADE_NAME'], // 보고자 직급
		"bogo_dprt_name" => $col['BOGO_DPRT_NAME'],
		"gubun" => $col['GUBUN'], // 구분 
		"stats" => $col['STATS'], // 상태
		"grade_name" => $col['GRADE_NAME'], 
		"dept_name" => $col['DEPT_NAME'], 		
		"stats_date" => $col['STATS_DATE'],
		"end_date" => $col['END_DATE'],
		"open_yn" => $col['OPEN_YN'],
		"file_yn" => $col['FILE_YN'],		
		"dq_yn" => $col['DQ_YN'],
		"sc_yn" => $col['SC_YN'],
		"regdate" => $col['REGDATE'], // 등록일시
		"empno" => $col['EMPNO'], // 사번
		"fileid" => $col['FILEID'],
		"ing_time" => $col['ING_TIME']		
  );
  
  array_push($resultArray, $arrayMiddle);
}

echo json_encode($resultArray);

$oci -> parseFree();
	
$oci -> disconnect();
$roci -> disconnect();

exit;
?>