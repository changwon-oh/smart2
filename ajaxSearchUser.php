<?

include "./include/env.inc.php";

include "./include/function.inc.php";

include "./include/oci8.inc.php";

include "./include/libutil.inc.php";

$sTable_name01 = "CO_USER";
$Name = $_POST['search'];

// SELECT * FROM ( 
// 	SELECT a.user_id, a.USER_KNM as NAME 
// 	, CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD
// 			WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD
// 			ELSE b.org_nm || '장' END AS PSTN_NAME,
// 	 a.ORG_CD as TRNS_DPRT_KEY
// 	FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd WHERE USER_TP_CD = '01' AND USER_KNM LIKE '%창원%' AND RETIR_YMD IS NULL ORDER BY a.GRD_ARRAY_NO
// 	) WHERE ROWNUM <= 10;

$qry_list =  " SELECT * FROM ( ";
$qry_list .= " SELECT a.user_id, a.USER_KNM as NAME ";
$qry_list .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD";
$qry_list .= " 		WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD";
$qry_list .= " 		ELSE b.org_nm || '장' END AS PSTN_NAME,";
$qry_list .= "  a.ORG_CD as TRNS_DPRT_KEY";
$qry_list .= " FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd WHERE USER_TP_CD = '01' AND USER_KNM LIKE '%$Name%' AND RETIR_YMD IS NULL ORDER BY a.GRD_ARRAY_NO ";
$qry_list .= " ) WHERE ROWNUM <= 10";
$oci -> parseExec($qry_list);
$resultArray = array();
while($col = $oci -> fetchInto()) {
  $arrayMiddle = array(
		"user_id" => $col['USER_ID'], 
		"user_knm" => $col['NAME'],
		"pstn_name" => $col['PSTN_NAME'],
		"trns_dprt_key" => $col['TRNS_DPRT_KEY']
  );

  array_push($resultArray, $arrayMiddle);
}

echo json_encode($resultArray);
$oci -> parseFree();

$oci -> disconnect();
$roci -> disconnect();

exit;
?>
