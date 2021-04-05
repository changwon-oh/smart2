<?
/*#########################################
# 시스템명 : 스마트보고 시스템
# 작 성 일 : 2021.04.05
# 파 일 명 : wb_time_jo.php
# 기능설명 :  보고시간 변경
#########################################*/
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?
// 첨부파일 경로 및 홈페이지 정보
include "./include/env.inc.php";
// 공통헤더
include "./include/head_inc.php";
// 통합인증처리
include "./include/function.inc.php";
// DB 접속
include "./include/oci8.inc.php";
// 공통변수 처리 
include "./include/libutil.inc.php";
// 인증정보
include "./include/sso_auth.inc.php";

// 전달변수처리
$sRepid = getRequestString("repid"); // 조회 ID
if ($sRepid == "") {
	$sMsg = "필수 정보가 누락되었습니다.";
	$sUrl = $_HOMEPAGE."smart2/wb_list.php";
	alertMsg_move($sMsg, $sUrl);
	exit;
}

// 조회권한 체크

if ($isReport != 1){ // 관리자가 아닌 경우
}
$sTable_name01 = "KOSPOWB_REPORT";

// 조회내용
$qry_view = "SELECT repid,empno,title,username,dept_name,grade_name,bogo_empno,rep_empno,rep_date1,rep_time1,rep_date2,rep_time2,
rep_content,gubun,stats, reason, to_char(reg_date,'YYYY-MM-DD') regdate, to_char(sysdate,'HH24:MI') time FROM $sTable_name01 WHERE REPID=$sRepid";
$oci -> parseExec($qry_view);
$nIdx = 1;
if ($oci -> fetch()) {
	$sRepid = $oci -> result($nIdx++); 
	$aEmpno = $oci -> result($nIdx++); 
	$sTitle = $oci -> result($nIdx++); 
	$sUsername = $oci -> result($nIdx++); 
	$sDept_name = $oci -> result($nIdx++); 
	$sgrade_name = $oci -> result($nIdx++);
	$sBogo_empno = $oci -> result($nIdx++); 
	$sRep_empno = $oci -> result($nIdx++); 
	$sRep_date1 = $oci -> result($nIdx++); 
	$sRep_time1 = $oci -> result($nIdx++); 
	$sRep_date2 = $oci -> result($nIdx++); 
	$sRep_time2 = $oci -> result($nIdx++);
	$sRep_content = viewReplace($oci -> result($nIdx++)); 
	$sGubun = $oci -> result($nIdx++);
	$sStats = $oci -> result($nIdx++);
	$sReason = viewReplace($oci -> result($nIdx++)); 	
	$sRegdate = $oci -> result($nIdx++); // 등록일시
	$sTime = $oci -> result($nIdx++); // 등록일시
	
	$aTime = explode(":",$sTime);
	
	$totdate  =	(strtotime($sRep_date2) - strtotime($sRep_date1))/60/60/24 + 1 ;
	$totbun  =	(strtotime($sRep_time2) - strtotime($sRep_time1))/60 ;
	$time  = (strtotime($sRep_time2) - strtotime($sRep_time1))/60/60;

	$time = explode('.',$time);
	$time = $time[0];
	
	if($totbun >= 60){
	 $totbun =  ($totbun - (60 * $time));
	}

	if($time > 0){
    $Tottime  =  $totdate * $time;
	}else{
	$Tottime =0;
	}
	
	// $qry_user = "SELECT NAME, PSTN_NAME FROM SMART_AUTH_SYNC WHERE SABUN='$sRep_empno'";
	$qry_user = "SELECT a.USER_KNM as NAME";
	$qry_user .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD";
	$qry_user .= " 	   WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD";
	$qry_user .= " 	   ELSE b.org_nm || '장' END AS PSTN_NAME";
	$qry_user .= " FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd WHERE a.user_id = '$sRep_empno'";

	$oci -> parseExec($qry_user);
	$ret = $oci -> fetch();
	if ($ret == 1) {
    	$sPayName = $oci -> result(1);
    	$sPstnName = $oci -> result(2);
    }

    // $qry_user = "SELECT NAME, PSTN_NAME FROM SMART_AUTH_SYNC WHERE SABUN='$sBogo_empno'";
	$qry_user = "SELECT a.USER_KNM as NAME";
	$qry_user .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD";
	$qry_user .= " 	   WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD";
	$qry_user .= " 	   ELSE b.org_nm || '장' END AS PSTN_NAME";
	$qry_user .= " FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd WHERE a.user_id = '$sBogo_empno'";

	$oci -> parseExec($qry_user);
	$ret = $oci -> fetch();
	if ($ret == 1) {
    	$sBogoName = $oci -> result(1);
    	$sBogoPstnName = $oci -> result(2);
    }
	
}
$oci -> parseFree();
$oci -> disconnect();
$roci -> disconnect();
?>
</head>
<link href="CSS/imp.css" rel="stylesheet" type="text/css" />

<body topmargin="0" leftmargin="0">
    <!-- <? include "./include/top_inc.php"; ?> -->
    <table width="700" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td align="left"><img src="./images/logo.gif" border="0"></td>
        </tr>
    </table>
    <!-- Left Menu & Contents //-->
    <table width="700" border="0" cellpadding="0" cellspacing="0" align="left">
        <tr>
            <td valign="top">
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr height="50">
                        <td></td>
                    </tr>
                </table>
            </td>
            <td>&nbsp;</td>
            <td valign="top" align="left">
                <script language="javascript">
                function frmMod() {
                    var frm = document.forms[0];
                    frm.action = "wb_time_jo_ok.php";
                    frm.submit();
                }
                </script>
                <form name="frm" method="post" onSubmit="return false;">
                    <input type="hidden" name="repid" value="<?=$sRepid;?>">
                    <input type="hidden" name="empno" value="<?=$aEmpno;?>">
                    <input type="hidden" name="rep_empno" id="rep_empno" value="<?=$sRep_empno;?>" />
                    <input type="hidden" name="page" value="<?=$nPage;?>">
                    <input type="hidden" name="code" value="">
                    <table width="700" border="0" cellpadding="3" cellspacing="0"
                        style="border:1px solid #E2E2E2;border-collapse:collapse;font-size:13px;">
                        <col width="100">
                        <col />
                        <tr>
                            <th class="table_td" style="border-bottom:1px solid #E2E2E2;">보고제목</th>
                            <td class="table_td_right">&nbsp;&nbsp;<?=$sTitle;?></td>
                        </tr>
                        <tr>
                            <th class="table_td" style="border-bottom:1px solid #E2E2E2;">보고자</th>
                            <td class="table_td_right">&nbsp;&nbsp;<?=$sBogoPstnName;?>&nbsp;<?=$sBogoName;?></td>
                        </tr>
                        <tr>
                            <th class="table_td" style="border-bottom:1px solid #E2E2E2;">보고대상</th>
                            <td class="table_td_right">&nbsp;&nbsp;<?=$sPstnName;?>&nbsp;<?=$sPayName;?></td>
                        </tr>
                        <tr>
                            <th class="table_td">시작시간</th>
                            <td class="table_td_right">&nbsp;
                                <input type="text" name="hour1" class="input_time" maxlength="2" value="<?=$aTime[0];?>"
                                    onKeyUp="return isNumber(this);">시
                                <input type="text" name="min1" class="input_time" maxlength="2" value="<?=$aTime[1];?>"
                                    onKeyUp="return isNumber(this);">분
                            </td>
                        </tr>
                    </table>
                    <table width="700" border="0" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>&nbsp;</td>
                        </tr>
                        <tr>
                            <td align="center">
								<span class="bt_summit" onClick="return frmMod();">시작</span>&nbsp;&nbsp;&nbsp;
                                <!-- &nbsp;&nbsp;<img src="./images/bt08.gif" border="0" style="cursor:pointer;" onClick="self.close();"> -->
								<span class="bt_summit" onClick="self.close();">닫기</span>
                            </td>
                        </tr>
                    </table>
                </form>
                <table border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>