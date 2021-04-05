<?php
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
// echo($sTable_name01);
$sWrite_date = charReplace($_POST['sWrite_date']);
$sRep_empno = charReplace($_POST['sRep_empno']);

// $qry_list = "SELECT repid, rep_date1,rep_time1,rep_time2,title,rep_content, username,gubun,stats, grade_name, dept_name, bogo_empno, open_yn, dq_yn, sc_yn, bogo_dprt_name, bogo_name, bogo_grade_name, (SELECT SUBSTR(XMLAGG(XMLELEMENT(X,'#',FILEID||'@'||SEQ) ORDER BY SEQ).EXTRACT('//text()'),2) FROM KOSPOWB_FILE WHERE WBID = REPID AND GUBUN = 'B') fileid, stats_date, end_date, to_char(round((sysdate-to_date(to_char(sysdate,'YYYYMMDD')||replace(stats_date,':',''),'YYYYMMDDHH24MISS'))*(24*60*60),0)) ing_time, to_char(round((sysdate-to_date(to_char(sysdate,'YYYYMMDD')||replace(rep_time2,':',''),'YYYYMMDDHH24MISS'))*(24*60*60),0)) over_time, to_char(reg_date,'YYYY-MM-DD') regdate, empno FROM $sTable_name01 A WHERE rep_empno = '$sRep_empno' and rep_date1 = substr('$sWrite_date',1,4)||'-'||substr('$sWrite_date',7,2)||'-'||substr('$sWrite_date',11,2) AND GUBUN=0 ORDER BY to_number(replace(stats_date,':','')), reg_date asc ";
$qry_list = "SELECT repid, rep_date1,rep_time1,rep_time2,title,rep_content, username,gubun,stats, grade_name, dept_name, bogo_empno, open_yn, dq_yn, sc_yn, bogo_dprt_name, bogo_name, bogo_grade_name, (SELECT SUBSTR(XMLAGG(XMLELEMENT(X,'#',FILEID||'@'||SEQ) ORDER BY SEQ).EXTRACT('//text()'),2) FROM KOSPOWB_FILE WHERE WBID = REPID AND GUBUN = 'B') fileid, stats_date, end_date, to_char(round((sysdate-to_date(to_char(sysdate,'YYYYMMDD')||replace(stats_date,':',''),'YYYYMMDDHH24MISS'))*(24*60*60),0)) ing_time, to_char(round((sysdate-(to_date(to_char(sysdate,'YYYYMMDD')||replace(stats_date,':',''),'YYYYMMDDHH24MISS')+round((to_date(to_char(sysdate,'YYYYMMDD')||replace(rep_time2,':',''),'YYYYMMDDHH24MISS')-to_date(to_char(sysdate,'YYYYMMDD')||replace(rep_time1,':',''),'YYYYMMDDHH24MISS')),6)))*(24*60*60),2)) over_time, to_char(reg_date,'YYYY-MM-DD') regdate, empno FROM $sTable_name01 A WHERE rep_empno = '$sRep_empno' and rep_date1 = substr('$sWrite_date',1,4)||'-'||substr('$sWrite_date',7,2)||'-'||substr('$sWrite_date',11,2) AND GUBUN=0 ORDER BY to_number(replace(stats_date,':','')), reg_date asc";

$oci -> parseExec($qry_list);
$resultArray = array();
while($col = $oci -> fetchInto()) {
  $arrayMiddle = array(
		"repid" => $col['REPID'], // 신고번호
		"rep_date1" => $col['REP_DATE1'], // 보고날짜
		"rep_time1" => $col['REP_TIME1'], // 보고날짜
		"rep_time2" => $col['REP_TIME2'], // 보고시간
		"title" => viewReplace($col['TITLE']), // 보고제목
		"rep_content" => viewReplace($col['REP_CONTENT']), // 보고내용	
		// "title" => $col['TITLE'], // 보고제목
		// "rep_content" => $col['REP_CONTENT'], // 보고내용
		"username" => $col['USERNAME'], // 신청자
		"bogo_empno" => $col['BOGO_EMPNO'], // 보고자
		"bogo_name" => $col['BOGO_NAME'], // 보고자명
		"bogo_grade_name" => $col['BOGO_GRADE_NAME'], // 보고자 직급
		"bogo_dprt_name" => $col['BOGO_DPRT_NAME'],
		"gubun" => $col['GUBUN'], // 
		"stats" => $col['STATS'], 
		"grade_name" => $col['GRADE_NAME'],
		"dept_name" => $col['DEPT_NAME'],  		
		"stats_date" => $col['STATS_DATE'],
		"end_date" => $col['END_DATE'],
		"regdate" => $col['REGDATE'], // 등록일시
		"empno" => $col['EMPNO'], // 사번
		"ing_time" => $col['ING_TIME'], // 사번		
		"over_time" => $col['OVER_TIME'],
		"open_yn" => $col['OPEN_YN'],
		"dq_yn" => $col['DQ_YN'],
		"fileid" => $col['FILEID'],
		"sc_yn" =>  $col['SC_YN']
  );
  
  array_push($resultArray, $arrayMiddle);
}

$oci -> parseFree();
	
$oci -> disconnect();
$roci -> disconnect();

echo json_encode($resultArray);
exit;
?>
