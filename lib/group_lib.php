<?

// TRNS_KEY 팀코드 기준으로 팀이릉과 상위팀 코드리스트를 가져옴
// TRNS_KEY 팀코드는 유일한 값으로 여러개가 있을 경우 마지막에 있는 값만 리턴함

function jojik_list($deptno)
{
	global $ora,$com_code;
	static $deptlist, $arrayIndex = 0;
/*
    if(isset($com_code) && ($com_code != "" || $com_code != NULL)){
        $query = "SELECT ORG_NAME1||'/'||ORG_NAME2||'/'||ORG_NAME3||'/'||ORG_NAME4||'/'||ORG_NAME5 TOT_ORG_NM, ORG_CODE1||'.'||ORG_CODE2||'.'||ORG_CODE3||'.'||ORG_CODE4||'.'||ORG_CODE5 TOT_ORG_CD FROM TP_JIKJAE_RECEIVE@EBIZ WHERE FIRM_NO='$com_code' AND (ORG_CODE1 = '$deptno' OR ORG_CODE2 = '$deptno' OR ORG_CODE3 = '$deptno' OR ORG_CODE4 = '$deptno' OR ORG_CODE5 = '$deptno') ORDER BY J_GUBUN, J_SY1, J_SY2, ORG_CODE1, ORG_CODE2, ORG_CODE3, ORG_CODE4, ORG_CODE5";
    } else {
*/
        //$query = "select offi_nm, ORG_TOP1_CD||'.'||ORG_TOP2_CD||'.'||ORG_TOP3_CD||'.'||ORG_TOP4_CD||'.'||ORG_TOP5_CD TOT_ORG_CD  from sy0140c@insa WHERE END_YMD IS NULL AND ORG_CD='$deptno' AND ORG_CD <> '0000'";
		$query = "SELECT FORMAL_NAME OFFI_NM, ORG_TOP1_CD||'.'||ORG_TOP2_CD||'.'||ORG_TOP3_CD||'.'||ORG_TOP4_CD||'.'||ORG_TOP5_CD TOT_ORG_CD FROM SMART_AUTH_DEPT WHERE TRNS_KEY='$deptno'";
//    }
	$row = $ora->FetchRow($query);
    
    // if($row[1] != ""){
    //     $deptlist = explode(".",$row[1]);
    // }
    // if($row[0] != ""){
    //     $deptname = str_replace("T/F","TF",$row[0]);
    //     $deptname = explode("/",$deptname);
    // }

    foreach ($row as $key => $value) {
        if($key == 0){
            $deptname = str_replace("T/F","TF",$value);
            $deptname = explode("/",$deptname);    
        } elseif($key == 1) {
            $deptlist = explode(".",$value);
        } else {
            $deptname = ""; 
        }
    }   

    // IT실 | 0000.0010.0020
	return $deptname;
}

?>
