<?
function isViewable_rep($empno,$wbid) {
	global $oci;
	$ret = -1;
	if ($empno != "") {
		$qry = "SELECT COUNT(*) FROM KOSPOWB_REPORT WHERE EMPNO='$empno' AND REPID=$wbid";
		$oci -> parseExec($qry);
		if ($oci -> fetch()) {
		    $ret = $oci -> result(1);
		}
		$oci -> parseFree();		
	}
	return $ret;
}

// 스마트 보고관리자(부속실 관리자)
function getSmartIsAdmin($empno,$repno){
    global $oci;
	$ret = 0;
	if ($empno != "") {
		// SELECT DECODE(COUNT(*),0,0,1) FROM KOSPOWB_REPORT_ADMIN WHERE BOGO_EMPNO = '1001177' AND (MANAGER_EMPNO1='1202539' OR MANAGER_EMPNO2 = '1202539')
	    $qry = "SELECT DECODE(COUNT(*),0,0,1) FROM KOSPOWB_REPORT_ADMIN WHERE BOGO_EMPNO = '$repno' AND (MANAGER_EMPNO1='$empno' OR MANAGER_EMPNO2 = '$empno')  ";
	    $oci -> parseExec($qry);
		if ($oci -> fetch()) {
		    $ret = $oci -> result(1);
		}
		$oci -> parseFree();
	}
	return $ret;
}

// 스마트 메인(wb_main.php) 접근권한
function getSmartIsManage($empno){
    global $oci;
	$ret = 0;
	if ($empno != "") {
		// SELECT DECODE(COUNT(*),0,0,1) FROM KOSPOWB_REPORT_ADMIN WHERE (BOGO_EMPNO='1202539' OR MAIN_EMPNO = '1202539')
	    $qry = "SELECT DECODE(COUNT(*),0,0,1) FROM KOSPOWB_REPORT_ADMIN WHERE (BOGO_EMPNO='$empno' OR MAIN_EMPNO = '$empno') ";
	    $oci -> parseExec($qry);
		if ($oci -> fetch()) {
		    $ret = $oci -> result(1);
		}
		$oci -> parseFree();
	}
	return $ret;
}

// 접속자 인사정보
function getEmpno($empno){
    global $oci;
	$ret = array();
	if ($empno != "") {

		// SELECT a.user_id, a.USER_KNM as NAME 
		// , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD
		// 		WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD
		// 		ELSE b.org_nm || '장' END||'님' AS PSTN_NAME,
		//  a.ORG_CD as TRNS_DPRT_KEY
		// FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd WHERE USER_TP_CD = '01' AND user_id = '1202539';
		$qry = " SELECT a.user_id, a.USER_KNM as NAME ";
		$qry .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD";
		$qry .= " WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD";
		$qry .= " ELSE b.org_nm || '장' END||'님' AS PSTN_NAME";
		$qry .= " FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd WHERE USER_TP_CD = '01' AND user_id = '$empno'";
	    $oci -> parseExec($qry);
		while($col = $oci -> fetchInto()) {
			$arrayMiddle = array(
				"user_id" => $col['USER_ID'],
				"user_knm" => $col['NAME'],
				"posit_cd" => $col['PSTN_NAME'],
			);
			array_push($ret, $arrayMiddle);
		}
		$oci -> parseFree();
	}
	return $ret;
}

// 직속상관 리스트
function getRepEmpnos($empno){
    global $oci;
	$ret = array();
	if ($empno != "") {
		// SELECT A.ORG_NM, A.ORG_CD, B.USER_ID, B.USER_KNM, ORG_LVL 
		// , CASE WHEN B.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD ELSE B.USER_RPSWRK_CD END||'님' AS POSIT_CD 
		// , CASE WHEN B.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD ELSE A.ORG_NM||'장' END||'님' AS POSIT_CD 
		// FROM ( SELECT ORG_NM, ORG_CD, HGR_ORG_CD, ORG_LVL FROM CO_ORG 
		// START WITH ORG_CD = ( SELECT ORG_CD FROM CO_USER WHERE USER_ID = '1202539' ) 
		// CONNECT BY PRIOR HGR_ORG_CD = ORG_CD ) A 
		// INNER JOIN CO_USER B ON A.ORG_CD = B.ORG_CD 
		// AND USER_RPSWRK_CD IN ( '팀장','실장','본부장','사장','대표이사사장','현장소장','CFO' ) 
		// AND B.user_id <> '1202539' ORDER BY ROWNUM DESC;
		$qry = " SELECT A.ORG_NM, A.ORG_CD, B.USER_ID, B.USER_KNM, ORG_LVL";
		$qry .= " , CASE WHEN B.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD ELSE B.USER_RPSWRK_CD END||'님' AS POSIT_CD";
		$qry .= " , CASE WHEN B.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD ELSE A.ORG_NM||'장' END||'님' AS POSIT_CD";
		$qry .= " FROM (";
		$qry .= " SELECT ORG_NM, ORG_CD, HGR_ORG_CD, ORG_LVL FROM CO_ORG";
		$qry .= " START WITH ORG_CD = ( SELECT ORG_CD FROM CO_USER WHERE USER_ID = '$empno' )";
		$qry .= " CONNECT BY PRIOR HGR_ORG_CD = ORG_CD";
		$qry .= " ) A";
		$qry .= " INNER JOIN CO_USER B ON A.ORG_CD = B.ORG_CD";
		$qry .= " AND USER_RPSWRK_CD IN ( '팀장','실장','본부장','사장','대표이사사장','현장소장','CFO' ) AND B.user_id <> '$empno' ORDER BY ROWNUM DESC";
	    $oci -> parseExec($qry);
			while($col = $oci -> fetchInto()) {
				$arrayMiddle = array(
					"org_nm" => $col['ORG_NM'],
					"org_cd" => $col['ORG_CD'],
					"user_id" => $col['USER_ID'],
					"user_knm" => $col['USER_KNM'],
					"posit_cd" => $col['POSIT_CD'],
					// "posit_cd_1" => $col['POSIT_CD_1'],
					
				);
				array_push($ret, $arrayMiddle);
			}
		$oci -> parseFree();
	}
	return $ret;
}

//보고관리대상자 리스트(부속실 관리자)
function getRepManagers($empno){
    global $oci;
	$ret = array();
	if ($empno != "") {
		// SELECT b.ORG_NM, A.ORG_CD, a.user_id, a.USER_KNM
		// , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD
		// 		WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD
		// 		ELSE b.org_nm || '장' END||'님' AS PSTN_NAME";
		// FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd 
		// 			   inner JOIN KOSPOWB_REPORT_ADMIN c ON a.user_id = c.BOGO_EMPNO
		// WHERE MANAGER_EMPNO1 = '1202539' OR MANAGER_EMPNO2 = '1202539';
		$qry = " SELECT b.ORG_NM, A.ORG_CD, a.user_id, a.USER_KNM";
 		$qry .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD";
		$qry .= " WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD";
	    $qry .= " ELSE b.org_nm || '장' END||'님' AS PSTN_NAME";
	    $qry .= "  FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd ";
		$qry .= "                 inner JOIN KOSPOWB_REPORT_ADMIN c ON a.user_id = c.BOGO_EMPNO";
		$qry .= "  WHERE MANAGER_EMPNO1 = '$empno' OR MANAGER_EMPNO2 = '$empno' ORDER BY a.GRD_ARRAY_NO ";
	    $oci -> parseExec($qry);
			while($col = $oci -> fetchInto()) {
				$arrayMiddle = array(
					"org_nm" => $col['ORG_NM'],
					"org_cd" => $col['ORG_CD'],
					"user_id" => $col['USER_ID'],
					"user_knm" => $col['USER_KNM'],
					"posit_cd" => $col['PSTN_NAME'],
					// "posit_cd_1" => $col['POSIT_CD_1'],
					
				);
				array_push($ret, $arrayMiddle);
			}
		$oci -> parseFree();
	}
	return $ret;
}


//보고대상자리스트(집행임원+관리자)
function getMainManagers($empno){
    global $oci;
	$ret = array();
	if ($empno != "") {
		$qry = " SELECT b.ORG_NM, A.ORG_CD, a.user_id, a.USER_KNM";
 		$qry .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD";
		$qry .= " WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD";
	    $qry .= " ELSE b.org_nm || '장' END||'님' AS PSTN_NAME";
	    $qry .= "  FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd ";
		$qry .= "                 inner JOIN KOSPOWB_REPORT_ADMIN c ON a.user_id = c.BOGO_EMPNO";
		$qry .= "  WHERE BOGO_EMPNO = '$empno' OR MAIN_EMPNO = '$empno' ORDER BY A.GRD_ARRAY_NO  ";
	    $oci -> parseExec($qry);
			while($col = $oci -> fetchInto()) {
				$arrayMiddle = array(
					"org_nm" => $col['ORG_NM'],
					"org_cd" => $col['ORG_CD'],
					"user_id" => $col['USER_ID'],
					"user_knm" => $col['USER_KNM'],
					"posit_cd" => $col['PSTN_NAME'],
					// "posit_cd_1" => $col['POSIT_CD_1'],
					
				);
				array_push($ret, $arrayMiddle);
			}
		$oci -> parseFree();
	}
	return $ret;
}

/*
function getSmartAdmin($empno){
    global $oci;
	$ret = "";
	if ($empno != "") {
	    $qry = "SELECT MANAGER_EMPNO1||'.'||MANAGER_EMPNO2 FROM KOSPOWB_REPORT_ADMIN WHERE BOGO_EMPNO='$empno' ";
	    $oci -> parseExec($qry);
		if ($oci -> fetch()) {
		    $ret = $oci -> result(1);
		}
		$oci -> parseFree();
	}

	return $ret;    
}

function getSmartManage($empno){
    global $oci;
	$ret = "";
	if ($empno != "") {
	    $qry = "SELECT BOGO_EMPNO FROM KOSPOWB_REPORT_ADMIN WHERE (BOGO_EMPNO='$empno' OR MAIN_EMPNO = '$empno' OR MANAGER_EMPNO1='$empno' OR MANAGER_EMPNO2 = '$empno') ";
	    $oci -> parseExec($qry);
		if ($oci -> fetch()) {
		    $ret = $oci -> result(1);
		}
		$oci -> parseFree();
	}

	return $ret;
}

function getMobileNo($empno) {
	global $oci;
	$ret = "";
	if ($empno != "") {
	    $qry = "SELECT replace(mbl_phn,'-','') FROM SMART_AUTH_SYNC WHERE SABUN='$empno' ";
	    $oci -> parseExec($qry);
		if ($oci -> fetch()) {
		    $ret = $oci -> result(1);
		}
		$oci -> parseFree();
	}

	return $ret;
}
*/

?>