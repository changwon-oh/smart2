<?
/*#########################################
# 시스템명 : 스마트보고 시스템
# 작 성 일 : 2021.04.05
# 파 일 명 : wb_time_mod_ok.php
# 기능설명 : 보고시간 변경처리
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
	$sUrl = $_HOMEPAGE."smart2/wb_list.php";
	alertMsg_move($sMsg, $sUrl);
	exit;
}
$nPage = getRequestNumber("page"); // 페이지번호
if ($nPage < 0) $nPage = 1;


if ($isReport != 1){ // 관리자가 아닌 경우
	//$nRet = isViewable_rep($sEmpno,$sRepid);
	//if ($nRet <= 0) {
	//	echo "조회권한이 없습니다.";
	//	$oci -> disconnect();
	//	$roci -> disconnect();
	//	exit;
	//}
}
$sTable_name01 = "KOSPOWB_REPORT";

// 조회내용
$qry_view = "SELECT repid,empno,title,username,dept_name,grade_name,bogo_empno,rep_empno,rep_date1,rep_time1,rep_date2,rep_time2,
rep_content,gubun,stats, reason, to_char(reg_date,'YYYY-MM-DD') regdate FROM $sTable_name01 WHERE REPID=$sRepid";
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
	
	$aRep_time1 = explode(":",$sRep_time1);
	$aRep_time2 = explode(":",$sRep_time2);

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
	
	// $qry_user = "SELECT to_char(sysdate,'YYYY-MM-DD HH24:MI') NOW, NAME, PSTN_NAME FROM SMART_AUTH_SYNC WHERE SABUN='$sRep_empno'";
	$qry_user = "SELECT to_char(sysdate,'YYYY-MM-DD HH24:MI') NOW, a.USER_KNM as NAME";
	$qry_user .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD";
	$qry_user .= " 	   WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD";
	$qry_user .= " 	   ELSE b.org_nm || '장' END AS PSTN_NAME";
	$qry_user .= " FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd WHERE a.user_id = '$sRep_empno'";
	$oci -> parseExec($qry_user);
	$ret = $oci -> fetch();
	if ($ret == 1) {
	$sToDate            = $oci -> result(1);
	$sToDate 			= charReplace($sToDate);
	$sPayName = $oci -> result(2);
	$sPstnName = $oci -> result(3);
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
<link href="CSS/imp.css" rel="stylesheet" type="text/css" />
<link href="CSS/jquery-ui.css" rel="stylesheet" type="text/css" />
<script src="./JS/jquery-1.7.2.min.js"></script>
<script src="./JS/jquery-ui.js"></script>
</head>
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
<td valign="top" >
<table border="0" cellpadding="0" cellspacing="0">
	<tr height="50">
	<td></td>
	</tr>
	</table>
</td>
<td>&nbsp;</td>

<style>
	.ui-datepicker-trigger {
		vertical-align:top;
		height: 24px; /* the same of textbox */
	}
</style>

<td valign="top">
	<script language="javascript">
	// 달력
	$(function(){
		$('#sdate').datepicker({
			showOn: "both",
			buttonImage:"./images/ico_day.gif",
			buttonImageOnly:true
		});
				
		$('img').mouseover(function(){
		    if($(this).attr("src") == "./images/ico_day.gif")
	            $(this).css("cursor","pointer");
		});
	});
	
	window.onload = function (){ 
		$.datepicker.regional['ko'] = {
			inline: true,
	 		dateFormat: "yy-mm-dd",
	 		changeMonth: true,
	 		changeYear: true,
	 		showButtonPanel: false, //아래 버튼
			monthNames : ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
			monthNamesShort : ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
			dayNames: ['일','월','화','수','목','금','토'],
			dayNamesShort: ['일','월','화','수','목','금','토'],
			dayNamesMin: ['일','월','화','수','목','금','토'],
			firstDay: 0,
			isRTL: false,
			showMonthAfterYear: true,
			showAnim: 'slideDown'
		};
		$.datepicker.setDefaults($.datepicker.regional['ko']);
	}
	
	function frmList(){
		location.href = "./wb_list.php?page=<?=$nPage;?>";
	}
	function frmMod(){
		var f = document.forms[0];
		if(f.hour1.value == "" || f.min1.value == ""){
		  alert('보고 시작 시간을 입력해 주세요.');
		  return;
		}
		if(f.hour2.value == "" || f.min2.value == ""){
		  alert('보고 종료 시간을 입력해 주세요.');
		  return;
		}
		
		if(Number(f.hour1.value) > Number(f.hour2.value)){
		   f.hour1.focus();
		   alert('보고 종료 시간보다 보고 시작 시간이 큽니다.');
		   return;
		}
		
		if(Number(f.hour1.value) < 8 || Number(f.hour1.value) > 18){
			f.hour1.focus();
		    alert('보고시간은 08시부터 18시끼지 입니다.');
		    return;
		}
		
		if(Number(f.hour2.value) < 8 || Number(f.hour2.value) > 18){
			f.hour2.focus();
		    alert('보고시간은 08시부터 18시끼지 입니다.');
		    return;
		}
		
		if(Number(f.min1.value) > 59){
			f.min1.focus();
		    alert('59분 이하로 입력해주세요.');
		    return;
		}
		
		if(Number(f.min2.value) > 59){
			f.min2.focus();
		    alert('59분 이하로 입력해주세요.');
		    return;
		}
		
		if(Number(f.hour1.value) == Number(f.hour2.value) && Number(f.min1.value) > Number(f.min2.value)){
			f.min1.focus();
		   alert('보고 종료 분보다 보고 시작 분이 큽니다.');
		   return;
		}
		
		if(Number(f.sdate.value.replace(/-/gi,'')) < Number('<?=$sToDate;?>'.replace(/－/gi,'').substring(0,8)) || Number(f.sdate.value.replace(/-/gi,'')) == Number('<?=$sToDate;?>'.replace(/－/gi,'').substring(0,8)) && Number(f.hour1.value) < Number('<?=$sToDate;?>'.substring(11,13))){
			alert('지난날로 날짜 변경 할 수 없습니다.');
			return;
		}
		
		if((Number(f.hour2.value)-Number(f.hour1.value))*60 + Number(f.min2.value) - Number(f.min1.value) > 30){
		   if(!confirm('보고 시간이 30분 이상입니다. 진행하시겠습니까?')){
		       return;
		   }
		}
		
		frm.action = "wb_time_mod_ok.php";
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
	</script>

	<form name="frm" method="post" onSubmit="return false;">
	<input type="hidden" name="repid" value="<?=$sRepid;?>">
	<input type="hidden" name="empno" value="<?=$aEmpno;?>">
	<input type="hidden" name="page" value="<?=$nPage;?>">
	<input type="hidden" name="code" value="">
	<table width="700" border="0" cellpadding="3" cellspacing="0" style="border:1px solid #E2E2E2;border-collapse:collapse;font-size:13px;">
	<col width="100">
	<col />
	<tr>
	<td class="table_td" style="border-bottom:1px solid #E2E2E2;">보고제목</td>
	<td class="table_td_right">&nbsp;&nbsp;<?=$sTitle;?></td>
	</tr>
	<tr>
	<td class="table_td" style="border-bottom:1px solid #E2E2E2;">보고자</td>
	<td class="table_td_right">&nbsp;&nbsp;<?=$sBogoPstnName;?>&nbsp;<?=$sBogoName;?></td>
  </tr>
  <tr>
	<td class="table_td" style="border-bottom:1px solid #E2E2E2;">보고대상</td>
	<td class="table_td_right">&nbsp;&nbsp;<?=$sPstnName;?>&nbsp;<?=$sPayName;?></td>
	</tr>
	<tr>
	<td class="table_td">일시</td>
	<td class="table_td_right">&nbsp;
		<input type="text" name="sdate" id="sdate" class="input_date" maxlength="10" value="<?=$sRep_date1;?>" onKeyUp="return isNumber(this);" readonly >
		<input type="text" name="hour1" class="input_time" maxlength="2" value="<?=$aRep_time1[0];?>" onKeyUp="return isNumber(this);">시
		<input type="text" name="min1" class="input_time" maxlength="2" value="<?=$aRep_time1[1];?>" onKeyUp="return isNumber(this);">분 ~
		<input type="text" name="hour2" class="input_time" maxlength="2" value="<?=$aRep_time2[0];?>" onKeyUp="return isNumber(this);">시
		<input type="text" name="min2" class="input_time" maxlength="2" value="<?=$aRep_time2[1];?>" onKeyUp="return isNumber(this);">분
		</td>
	</tr>
	</table>
	<table width="700" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td>&nbsp;</td>
	</tr>
	<tr>
	<td align="center">
	<? //if ($isReport == 1){
		?>
	<!--요청중일 경우에만 수정-->
	<!-- &nbsp;&nbsp;<img src="./images/bt05.gif" border="0" style="cursor:pointer;" onClick="return frmMod();"> -->
	<span class="bt_summit" onClick="return frmMod();">수정</span>&nbsp;&nbsp;&nbsp;
	<? //}
	 ?>
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
