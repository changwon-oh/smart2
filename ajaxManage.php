<?php

include "./include/env.inc.php";

include "./include/function.inc.php";

include "./include/oci8.inc.php";

include "./include/libutil.inc.php";

$sTable_name01 = "KOSPOWB_REPORT_ADMIN";
// $qry_list = "SELECT * FROM (SELECT repid, admin_empno, bogo_empno, manager_empno1, manager_empno2, main_empno, B.pstn_name title, B.name bogo_name, B.grade_name bogo_grade_name, B.trns_dprt_key bogo_deptno, B.bonsa_yn, B.dprt_name, B.trns_grade_key, B.dept_sort, C.name manager_name1, C.grade_name manager_grade_name1, D.name manager_name2, D.grade_name manager_grade_name2, E.name main_name, E.grade_name main_grade_name, sb_yn, to_char(reg_date,'YYYY-MM-DD') regdate FROM $sTable_name01 A, SMART_AUTH_SYNC B, SMART_AUTH_SYNC C, SMART_AUTH_SYNC D, SMART_AUTH_SYNC E WHERE A.BOGO_EMPNO = B.SABUN(+) AND A.MANAGER_EMPNO1 = C.SABUN(+) AND A.MANAGER_EMPNO2 = D.SABUN(+) AND A.MAIN_EMPNO = E.SABUN(+)) ORDER BY DECODE(BONSA_YN,'Y',1,2), TO_NUMBER(DECODE(SUBSTR(TITLE,LENGTH(TITLE)-1,2),'사장',1,'부장]',2)), DEPT_SORT ";
// SELECT * FROM (SELECT repid, admin_empno, bogo_empno, manager_empno1, manager_empno2, main_empno, B.pstn_name title, B.name bogo_name, B.grade_name bogo_grade_name, B.trns_dprt_key bogo_deptno, B.bonsa_yn, B.dprt_name, B.trns_grade_key, B.dept_sort,
//                C.name manager_name1, C.grade_name manager_grade_name1, D.name manager_name2, D.grade_name manager_grade_name2, E.name main_name, E.grade_name main_grade_name, sb_yn, to_char(reg_date,'YYYY-MM-DD') regdate   
//                 FROM KOSPOWB_REPORT_ADMIN A, SMART_AUTH_SYNC B, SMART_AUTH_SYNC C, SMART_AUTH_SYNC D, SMART_AUTH_SYNC E  
//  WHERE A.BOGO_EMPNO = B.SABUN(+) AND A.MANAGER_EMPNO1 = C.SABUN(+) AND A.MANAGER_EMPNO2 = D.SABUN(+) AND A.MAIN_EMPNO = E.SABUN(+)) ORDER BY DECODE(BONSA_YN,'Y',1,2), TO_NUMBER(DECODE(SUBSTR(TITLE,LENGTH(TITLE)-1,2),'사장',1,'부장]',2)), DEPT_SORT;
 
// SELECT * FROM (SELECT B.name bogo_name, C.name manager_name1, C.name manager_name2, main_empno, B.pstn_name title, sb_yn
//                 FROM KOSPOWB_REPORT_ADMIN A, SMART_AUTH_SYNC B, SMART_AUTH_SYNC C, SMART_AUTH_SYNC D, SMART_AUTH_SYNC E  
//  WHERE A.BOGO_EMPNO = B.SABUN(+) AND A.MANAGER_EMPNO1 = C.SABUN(+) AND A.MANAGER_EMPNO2 = D.SABUN(+) AND A.MAIN_EMPNO = E.SABUN(+));

// SELECT * FROM (SELECT B.USER_KNM bogo_name, C.USER_KNM manager_name1, C.USER_KNM manager_name2, main_empno, sb_yn
//                 FROM KOSPOWB_REPORT_ADMIN A, co_user B, co_user C, co_user D, co_user E  
// WHERE A.BOGO_EMPNO = B.user_id(+) AND A.MANAGER_EMPNO1 = C.user_id(+) AND A.MANAGER_EMPNO2 = D.user_id(+) AND A.MAIN_EMPNO = E.user_id(+));

$qry_list = " SELECT repid, admin_empno, bogo_empno, manager_empno1, manager_empno2,  B.USER_KNM || ' ' || B.USER_POSIT_CD bogo_name , C.USER_KNM || ' ' || C.INCHRG_DUTY manager_name1 , D.USER_KNM  || ' ' || D.INCHRG_DUTY manager_name2, main_empno, sb_yn";
$qry_list .= " FROM KOSPOWB_REPORT_ADMIN A, co_user B, co_user C, co_user D, co_user E  ";
$qry_list .= " WHERE A.BOGO_EMPNO = B.user_id(+) AND A.MANAGER_EMPNO1 = C.user_id(+) AND A.MANAGER_EMPNO2 = D.user_id(+) AND A.MAIN_EMPNO = E.user_id(+) ORDER BY B.GRD_ARRAY_NO";

$oci -> parseExec($qry_list);
$resultArray = array();
while($col = $oci -> fetchInto()) {
  $arrayMiddle = array(
		// "repid" => $col['REPID'], 
		// "admin_empno" => $col['ADMIN_EMPNO'], 
		// "bogo_empno" => viewReplace($col['BOGO_EMPNO']), 
		// "bogo_deptno" => viewReplace($col['BOGO_DEPTNO']),
		// "bonsa_yn" => viewReplace($col['BONSA_YN']),
		// "bogo_name" => viewReplace($col['BOGO_NAME']), // 보고대상 title + bogo_name 보임
		// "bogo_grade_name" => viewReplace($col['BOGO_GRADE_NAME']),
		// "manager_empno1" => viewReplace($col['MANAGER_EMPNO1']), 
		// "manager_name1" => viewReplace($col['MANAGER_NAME1']), // 관리자1
		// "manager_grade_name1" => viewReplace($col['MANAGER_GRADE_NAME1']),
		// "manager_empno2" => viewReplace($col['MANAGER_EMPNO2']), 
		// "manager_name2" => viewReplace($col['MANAGER_NAME2']),  // 관리자2
		// "manager_grade_name2" => viewReplace($col['MANAGER_GRADE_NAME2']),
		// "main_empno" => viewReplace($col['MAIN_EMPNO']),  //메인화면
		// "main_name" => viewReplace($col['MAIN_NAME']),
		// "main_grade_name" => viewReplace($col['MAIN_GRADE_NAME']),
		// "title" => viewReplace($col['TITLE']), // 보고대상 title + bogo_name 보임
		// "regdate" => $col['REGDATE'], 
		// "use_yn" =>  $col['SB_YN'], //사용여부

		"repid" => $col['REPID'], 
		"admin_empno" => $col['ADMIN_EMPNO'], 
		"bogo_empno" => viewReplace($col['BOGO_EMPNO']), 
		"manager_empno1" => viewReplace($col['MANAGER_EMPNO1']), 
		"manager_empno2" => viewReplace($col['MANAGER_EMPNO2']), 
		"bogo_name" => viewReplace($col['BOGO_NAME']), // 보고대상 title + bogo_name 보임
		"manager_name1" => viewReplace($col['MANAGER_NAME1']), // 관리자1
		"manager_name2" => viewReplace($col['MANAGER_NAME2']),  // 관리자2
		"main_empno" => viewReplace($col['MAIN_EMPNO']),  //메인화면
		// "title" => viewReplace($col['TITLE']), // 보고대상 title + bogo_name 보임 임시로 사번
		"use_yn" =>  $col['SB_YN'] //사용여부

	);
  
  array_push($resultArray, $arrayMiddle);
}

$oci -> parseFree();
	
$oci -> disconnect();
$roci -> disconnect();

echo json_encode($resultArray);
exit;
?>
