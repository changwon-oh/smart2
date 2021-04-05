<?
/*#########################################
# 시스템명 : 스마트보고
# 작 성 일 : 2021.04.05
# 파 일 명 : wb_view.php
# 기능설명 : 스마트보고 조회
#########################################*/
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<META http-equiv="Expires" content="-1">
<META http-equiv="Pragma" content="no-cache">
<META http-equiv="Cache-Control" content="No-Cache">
<META charset="utf-8">
<META http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">
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
	$sUrl = $_HOMEPAGE."smart/wb_list2.php";
	alertMsg_move($sMsg, $sUrl);
	exit;
}
$nPage = getRequestNumber("page"); // 페이지번호
if ($nPage < 0) $nPage = 1;

$isAwmsAdmin = 1;

// 조회권한 체크
// if($isAwmsAdmin != 1){
	if ($isReport != 1){ // 관리자가 아닌 경우
		$nRet = isViewable_rep($sEmpno,$sRepid);
		if ($nRet <= 0) {
			echo "조회권한이 없습니다.";
			$oci -> disconnect();
			$roci -> disconnect();
			exit;
		}
	}
// }	
$sTable_name01 = "KOSPOWB_REPORT";
$sTable_name02 = "KOSPOWB_FILE";

// 조회내용
$qry_view = "SELECT repid,empno,title,username,dept_name,grade_name,bogo_empno,rep_empno,rep_date1,rep_time1,rep_date2,rep_time2,
rep_content,gubun, decode(open_yn,'N','비공개','공개') open_yn_txt, decode(file_yn,'N','비공개','공개') file_yn_txt, stats, reason, to_char(reg_date,'YYYY-MM-DD') regdate FROM $sTable_name01 WHERE REPID=$sRepid";
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
	$sOpenYnTxt = $oci -> result($nIdx++);
	$sFileYnTxt = $oci -> result($nIdx++);	
	$sStats = $oci -> result($nIdx++);
	$sReason = viewReplace($oci -> result($nIdx++)); 
	$sRegdate = $oci -> result($nIdx++); // 등록일시
	
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
	
	// 첨부파일
	$sAttcdFile = null;
    $qry_file = "SELECT * FROM $sTable_name02 WHERE WBID=$sRepid AND GUBUN='B' ORDER BY FILEID, SEQ";
    $oci -> parseExec($qry_file);
    $nFileIdx = 0;
    while($col = $oci -> fetchInto()) {
    	$sAttcdFile[$nFileIdx] = $col["UPLOAD_NAME"];
    	$sAttcdFileId[$nFileIdx] = $col["FILEID"];
    	$sAttcdFileSeq[$nFileIdx++] = $col["SEQ"];
    }
    $oci -> parseFree();
	
	// $qry_user = "SELECT NAME, PSTN_NAME, TRNS_DPRT_KEY FROM SMART_AUTH_SYNC WHERE SABUN='$sRep_empno'";
	$qry_user = "SELECT a.USER_KNM as NAME";
	$qry_user .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD";
	$qry_user .= " 	   WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD";
	$qry_user .= " 	   ELSE b.org_nm || '장' END AS PSTN_NAME,";
	$qry_user .= " a.ORG_CD as TRNS_DPRT_KEY";
	$qry_user .= " FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd WHERE a.user_id = '$sRep_empno'";
	$oci -> parseExec($qry_user);
	$ret = $oci -> fetch();
	if ($ret == 1) {
	    $sPayName = $oci -> result(1);
	    $sPstnName = $oci -> result(2);
	    $sTrns_dprt_key = $oci -> result(3);
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
<td valign="top" background="../images/menu_bg.jpg">
<table border="0" cellpadding="0" cellspacing="0">
	<tr height="50">
	<td></td>
	</tr>
	</table>
</td>
<td>&nbsp;</td>
<td valign="top" align="left">
	<script language="javascript">
	function frmList(){
		location.href = "./wb_list.php?page=<?=$nPage;?>";
	}
	function frmMod(){
		var frm = document.forms[0];
		frm.action = "wb_mod.php";
		frm.submit();
	}
	function frmTimeMod(){
		var frm = document.forms[0];
		frm.action = "wb_time_mod.php";
		frm.submit();
	}
	
	function frmBak() {
      document.getElementById('oDiv').style.display="";
      document.getElementById('oDiv').style.posTop = "500";
      document.getElementById('oDiv').style.posLeft = "300";
      document.getElementById('TextA').focus();
	}
		
	function frmDel(){
	    if (confirm("삭제 하시겠습니까?")){
			var frm = document.forms[0];
			frm.action = "wb_del_ok.php";
			frm.submit();
		} else {
			alert("취소되었습니다.");
			return;
		}
    }
    
	</script>
	<form name="frm" method="post" onSubmit="return false;">
	<input type="hidden" name="repid" value="<?=$sRepid;?>">
	<input type="hidden" name="empno" value="<?=$aEmpno;?>">
	<input type="hidden" name="sEmpNo" value="<?=$sEmpno;?>">	
	<input type="hidden" name="page" value="<?=$nPage;?>">
	<input type="hidden" name="retYear" id="retYear" value="" />	
	<input type="hidden" name="testUserId" id="testUserId" value="<?=$sEmpno;?>" />	
	<input type="hidden" name="dprt_key" id="dprt_key" value="<?=$sTrns_dprt_key;?>" />
	<table width="690" border="0" cellpadding="3" cellspacing="0" style="border:1px solid #E2E2E2;border-collapse:collapse;font-size:12px;">
	<col width="130">
	<col />
	<tr>
	<td class="table_td" style="border-bottom:1px solid #E2E2E2;">보고제목</td>
	<!-- font-size:16px;
	background-color:#AAC7F2;
	font-weight:800;	
	text-align:center; -->
	<td align="left" style="border-bottom:1px solid #E2E2E2"><span style=" font-size: 15px; font-weight:800;">&nbsp;&nbsp;&nbsp;<?=$sTitle;?></span></td>
	</tr>
	<tr>
	<td class="table_td" style="border-bottom:1px solid #E2E2E2;">신청자</td>
	<td align="left" style="border-bottom:1px solid #E2E2E2;"><span style=" font-size: 15px; font-weight:800;">&nbsp;&nbsp;&nbsp;<?=$sgrade_name;?> <?=$sUsername;?></span></td>
	</tr>	
	<tr>
	<td class="table_td" style="border-bottom:1px solid #E2E2E2;">보고자</td>
	<td align="left" style="border-bottom:1px solid #E2E2E2;"><span style=" font-size: 15px; font-weight:800;">&nbsp;&nbsp;&nbsp;<?=$sBogoPstnName;?> <?=$sBogoName;?></span></td>
	</tr>
	<tr>
	<td class="table_td" style="border-bottom:1px solid #E2E2E2;">보고대상</td>
	<td align="left" style="border-bottom:1px solid #E2E2E2;"><span style=" font-size: 15px; font-weight:800;">&nbsp;&nbsp;&nbsp;<?=$sPstnName;?>&nbsp;<?=$sPayName;?></span></td>
	</tr>
	<tr>
	<td class="table_td" style="border-bottom:1px solid #E2E2E2;">일시</td>
	<td align="left" style="border-bottom:1px solid #E2E2E2;"><span style=" font-size: 15px; font-weight:800;">&nbsp;&nbsp;&nbsp;<?=$sRep_date1;?> <?=$sRep_time1;?> ~ <?=$sRep_time2;?>  / 총 <?=$Tottime;?>시간 <?=$totbun?> 분</span></td>
	</tr>
	<tr>
	<td class="table_td" style="border-bottom:1px solid #E2E2E2;">제목공개</td>
	<td align="left" style="border-bottom:1px solid #E2E2E2;"><span style=" font-size: 15px; font-weight:800;">&nbsp;&nbsp;&nbsp;<?=$sOpenYnTxt;?></span></td>
	</tr>
	<tr>
	<td class="table_td" style="border-bottom:1px solid #E2E2E2;">첨부파일공개</td>
	<td align="left" style="border-bottom:1px solid #E2E2E2;"><span style=" font-size: 15px; font-weight:800;">&nbsp;&nbsp;&nbsp;<?=$sFileYnTxt;?></span></td>
	</tr>	
	<tr>
	<td class="table_td" style="border-bottom:1px solid #E2E2E2;">첨부파일</td>
	<td align="left" style="border-bottom:1px solid #E2E2E2;"><span style=" font-size: 15px; font-weight:800;">
	<? for($i=0; $i < count($sAttcdFile); $i++){ ?>
	    &nbsp;&nbsp;&nbsp;<a href="./wb_file.php?file=<?=$sAttcdFileId[$i];?>&wbid=<?=$sRepid;?>&seq=<?=$sAttcdFileSeq[$i];?>"><?=$sAttcdFile[$i];?></a> <br />
	<? } ?>
	</span></td>
	</tr>		
	<? if($sGubun == "2"){ ?>	
	<tr rowspan='2'>
	<td class="table_td">반려사유</td>
	<td>
		<table border="0" cellpadding="0" cellspacing="0">
		<tr>
		<td><span style=" font-size: 15px; font-weight:800;">&nbsp;&nbsp;&nbsp;<?=$sReason?></span></td>
		</tr>
		</table>
	</td>
	</tr>
	<?}?>
	</table>
	<table width="700" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td align="center" height="80">
	<? if (($sGubun == 1 || $sGubun == 2) && $sEmpno == $aEmpno){ ?>
			<!--요청중일 경우에만 수정-->
			<!-- &nbsp;&nbsp;<img src="./images/bt05.gif" border="0" style="cursor:hand;" onClick="return frmMod();"> -->
			<!-- &nbsp;&nbsp;<img src="./images/bt06.gif" border="0" style="cursor:hand;" onClick="return frmDel();"> -->
			<span class="bt_summit" onClick="return frmMod();">수정</span>&nbsp;&nbsp;&nbsp;
			<span class="bt_summit" onClick="return frmDel();">삭제</span>&nbsp;&nbsp;&nbsp;
		<? } ?>
		<!-- &nbsp;&nbsp;<img src="./images/bt08.gif" border="0" style="cursor:hand;" onClick="self.close();"> -->
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