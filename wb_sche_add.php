<?
/*#########################################
# 시스템명 : 스마트보고 시스템
# 작 성 일 : 2021.04.05
# 파 일 명 : wb_sche_add.php
# 기능설명 : 일정등록
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

$sTable_name01 = "KOSPOWB_REPORT";
// 접속자 정보
$act = "./wb_add_ok.php";
$sToday 	= getRequestString("write_date");
$sRepEmpno   = getRequestString("rep_empno");
$sRepid   = getRequestString("repid");

// $qry_user = "SELECT to_char(sysdate,'YYYY-MM-DD HH24:MI') NOW, NAME, DPRT_NAME, GRADE_NAME,to_char(sysdate,'HH24') NOWTIME  FROM SMART_AUTH_SYNC WHERE SABUN='$sEmpno'";
$qry_user = " SELECT to_char(sysdate,'YYYY-MM-DD HH24:MI') NOW,  a.USER_KNM as NAME";
$qry_user .= "  , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN a.USER_POSIT_CD ELSE a.USER_RPSWRK_CD END AS DPRT_NAME";
$qry_user .= "  , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD";
$qry_user .= " WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD";
$qry_user .= "         ELSE b.org_nm || '장' END AS GRADE_NAME";
$qry_user .= "  ,to_char(sysdate,'HH24') NOWTIME ";
$qry_user .= "  FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd WHERE a.user_id = '$sEmpno'";

$oci -> parseExec($qry_user);
$ret = $oci -> fetch();

$sTitle = "";
$sBogoEmpno = $sEmpno;

if($sRepid == ""){
    // $sBogoEmpno = $sEmpno;
    $e = 1;
    if ($ret == 1) {
    	$sToDate            = $oci -> result($e++);
    	$sToDate 			= charReplace($sToDate);
    	$sUserName 			= $oci -> result($e++);
    	$sDeptName 			= $oci -> result($e++);
    	$sGradeName 		= $oci -> result($e++);
        
        $sRep_time1 = $oci -> result($e++).":00";
        $sRep_time2 = ($sRep_time1+1).":00";    
        $aRep_time1 = explode(":",$sRep_time1);
    	$aRep_time2 = explode(":",$sRep_time2);	
    }
} else {
    $act = "./wb_mod_ok.php";
    // 조회내용
    // $qry_view = "SELECT repid,empno,title,username,dept_name,grade_name,bogo_empno,rep_empno,rep_date1,rep_time1,rep_time2,open_yn FROM $sTable_name01 WHERE EMPNO='$sEmpno' AND REPID=$sRepid ";
    $qry_view = "SELECT repid,empno,title,username,dept_name,grade_name,bogo_empno,rep_empno,rep_date1,rep_time1,rep_time2,open_yn FROM $sTable_name01 WHERE REPID=$sRepid "; // 다른비서가 등록한 내용도 수정 가능
	$oci -> parseExec($qry_view);
    $nIdx = 1;
    if ($oci -> fetch()) {
        
    	$sRepid = $oci -> result($nIdx++); 
    	// $sEmpno = $oci -> result($nIdx++); 
    	$TEMP_sEmpno = $oci -> result($nIdx++); 
    	$sTitle = $oci -> result($nIdx++); 
    	$sUserName = $oci -> result($nIdx++);
    	$sDeptName = $oci -> result($nIdx++);
    	$sGradeName = $oci -> result($nIdx++);
    	// $sBogoEmpno = $oci -> result($nIdx++);
		$TEMP_sBogoEmpno = $oci -> result($nIdx++);
    	$sRepEmpno = $oci -> result($nIdx++);
    	$sToday = $oci -> result($nIdx++);
    	$sRep_time1 = $oci -> result($nIdx++); // 시간1
    	$sRep_time2 = $oci -> result($nIdx++); // 시간2
    	$aRep_time1 = explode(":",$sRep_time1);
    	$aRep_time2 = explode(":",$sRep_time2);
		$sOpenYn = $oci -> result($nIdx++);
    }

    $oci -> parseFree();
}

$oci -> disconnect();
$roci -> disconnect();
?>
<link href="CSS/imp.css?after1" rel="stylesheet" type="text/css" />
<script src="./JS/jquery-1.7.2.min.js"></script>
</head>
	<script language="javascript">
	function addRow(id){
	    var idx = 0;
	    var tbl = document.getElementById("tbl");
	    var row = tbl.rows;
        var cnt = row.length;            
        var r = tbl.insertRow(cnt);
        var html = new Array();
        html[idx++] = "<input type=\"text\" name=\"sdate\" id=\"sdate\" size=\"10\" maxlength=\"10\" value=\"2018-10-29\" onKeyUp=\"return isNumber(this);\" readonly > <img src=\"images/btn_data.gif\" style=\"cursor:pointer\" alt=\"날짜\" align=\"absmiddle\" onclick=\"calendar(this, document.getElementById('sdate'), 'yyyy-mm-dd')\" style=\"cursor:pointer;\" />";
        html[idx++] = "<input type=\"text\" name=\"hour1\" size=\"3\" maxlength=\"2\" value=\"<?=$aRep_time1[0];?>\" onKeyUp=\"return isNumber(this);\">시 <input type=\"text\" name=\"min1\" size=\"3\" maxlength=\"2\" value=\"<?=$aRep_time1[1];?>\" onKeyUp=\"return isNumber(this);\">분 ~ <input type=\"text\" name=\"hour2\" size=\"3\" maxlength=\"2\" value=\"<?=$aRep_time2[0];?>\" onKeyUp=\"return isNumber(this);\">시 <input type=\"text\" name=\"min2\" size=\"3\" maxlength=\"2\" value=\"<?=$aRep_time2[1];?>\" onKeyUp=\"return isNumber(this);\">분";
        html[idx++] = "<input type=\"text\" name=\"title\" size=\"75\" maxlength=\"200\"/>";
        for(var i=0; i < 3; i++){
            var c = tbl.rows(cnt).insertCell(i);
            c.innerHTML = html[i];
        }                        
	}

    function removeRow(){
      
    }
	
	/* 폼 체크 */
	function frmCheck() {
	  var f = document.frm;
	  var BogoEmpno = document.getElementsByName("BogoEmpno")[0].value;
	  
		var title = f.title.value;
		if(title.length == 0){
			alert('제목을 입력하지 않았습니다.');
			return false;
		}
		
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
				
		frm.submit();
	}
	
	function fnDel(){
    	if (confirm("삭제 하시겠습니까?")){
			var f = document.frm;
			frm.code.value = '3';
			frm.action = "wb_del_ok.php";
			frm.submit();
		}
    }
	
	/* textarea 입력길이 체크 */
	function sizeCheck(obj,size) {
		if ( obj.value.length > size ) {
			alert ("내용이 "+size+"자를 초과하였습니다.");
			obj.value = obj.value.substring(0,size-2);
			obj.focus();
		}
	}

	function isNumber(obj) {
		var objRule = /[^0-9]/g;
		if ( objRule.test(obj.value) == true ){
			alert ("숫자만 입력하세요.");
			obj.value = obj.value.substring( 0, obj.value.length-1 );
		}
	}	

	function change_name(){
		var input_name = document.getElementsByName("user_name")[0].value;
		var div_name = document.getElementById("div_lct_name");
		div_name.innerHTML = input_name;
	}
	</script>
<!--
<style type="text/css">
p.menu_bt01 a{
	padding:5px 13px;
	background-color:#002d5f;
	color:#ffffff;
	float:right;
	display:block;
	margin-top:-50px;
}
p.menu_bt02 a{
	padding:3px 10px;
	background-color:#002d5f;
	color:#ffffff;
	float:right;
	margin-right:150px;
	display:block;
	margin-top:-45px;
}
</style>
-->
<body topmargin="0" leftmargin="0">
<!-- <? include "./include/top_inc.php"; ?> -->
<table width="700" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td align="left"><img src="./images/logo.gif" border="0"></td>
	</tr>
</table>
<!-- Left Menu & Contents //-->
<table width="700" border="0" cellpadding="0" cellspacing="0">
<tr>
<td valign="top">
<table border="0" cellpadding="0" cellspacing="0">
	<tr height="50">
		<td></td>
		</tr>
	</table>
</td>
<td>&nbsp;</td>
<td valign="top">
	<form name="frm" method="post" action="<?=$act;?>" onSubmit="return false;">
	<input type="hidden" name="sdate" value="<?=$sToday;?>">
	<input type="hidden" name="repid" value="<?=$sRepid;?>">
	<input type="hidden" name="username" value="<?=$sUserName;?>">
	<input type="hidden" name="dept_name" value="<?=$sDeptName;?>">
	<input type="hidden" name="grade_name" value="<?=$sGradeName;?>">
	<input type="hidden" name="empno" value="<?=$sEmpno;?>">
	<input type="hidden" name="BogoEmpno" id="BogoEmpno" value="<?=$sBogoEmpno;?>">
	<input type="hidden" name="approvalEmpno" value="<?=$sRepEmpno?>">
	<input type="hidden" name="pgubun" value="0" />
	<input type="hidden" name="openYn" value="Y" />
	<input type="hidden" name="scYn" value="Y" />
	<input type="hidden" name="code" value="" />
	<!--a href="#" onclick="addRow('tbl')">추가</a-->
	<table width="700" border="0" cellpadding="3" cellspacing="0" style="border:1px solid #E2E2E2;border-collapse:collapse;font-size:12px;" id="tbl">
	<col width="100">
	<col />
	<tr>
	    <td class="table_td" style="border-bottom:1px solid #E2E2E2;">일정</td>
	    <td align="left" style="border-bottom:1px solid #E2E2E2;">&nbsp;<input type="text" class="input_title" name="title" value="<?=$sTitle;?>" maxlength="200"/></td>
	</tr>
	<tr>
		<td class="table_td" style="border-bottom:1px solid #E2E2E2;">일정공개</td>
		<td align="left" style="border-bottom:1px solid #E2E2E2;font-size: 15px; font-weight:800;">&nbsp;
			<input type="radio" name="openYn" value="Y" <? if(isset($sOpenYn)){if($sOpenYn=='Y' ){echo "checked" ;}}else{} ?>/>공개&nbsp;
		    <input type="radio" name="openYn" value="N" <? if(isset($sOpenYn)){if($sOpenYn=='N' ){echo "checked" ;}}else{ echo "checked" ; } ?> />비공개</td>
	</tr>
	<tr>
	    <td class="table_td" style="border-bottom:1px solid #E2E2E2;">일시</td>
	    <td style="border-bottom:1px solid #E2E2E2;">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td align="left">&nbsp;<input type="text" name="hour1" class="input_time" maxlength="2" value="<?=$aRep_time1[0];?>" onKeyUp="return isNumber(this);"><span style="font-size: 15px; font-weight:800;">시</span>
					<input type="text" name="min1" class="input_time" maxlength="2" value="<?=$aRep_time1[1];?>" onKeyUp="return isNumber(this);"><span style="font-size: 15px; font-weight:800;">분 ~ </span>
					<input type="text" name="hour2" class="input_time" maxlength="2" value="<?=$aRep_time2[0];?>" onKeyUp="return isNumber(this);"> <span style="font-size: 15px; font-weight:800;">시</span>
					<input type="text" name="min2" class="input_time" maxlength="2" value="<?=$aRep_time2[1];?>" onKeyUp="return isNumber(this);"><span style="font-size: 15px; font-weight:800;">분</span></td>
				</tr>
			</table>
		</td>
	</tr>
	</table>
	<br><br>
	<table width="700" cellpadding="0" cellspacing="0">
		<tr>
			<td><span class="bt_summit" onClick="return frmCheck();"><? if($sRepid != ""){ ?>수정<? } else { ?>등록<?}?></span>&nbsp;&nbsp;&nbsp;
			<? if($sRepid != ""){  ?>
				<span class="bt_summit" onClick="return fnDel();">삭제</span>&nbsp;&nbsp;&nbsp;
			<? } ?>
			<span class="bt_summit" onClick="self.close();">취소</span>
			</td>
		</tr>
	    <tr>
	        <td align="center" height="50">
				<!-- <img src="./images/<? if($sRepid != ""){ ?>bt05<? } else { ?>bt10<?}?>.gif" border="0" style="cursor:pointer;" onClick="return frmCheck();">&nbsp; <? if($sRepid != ""){ ?><img src="./images/bt06.gif" border="0" style="cursor:pointer;" onClick="return fnDel();"><? } ?>&nbsp;<img src="./images/bt02.gif" border="0" style="cursor:pointer;" onClick="self.close();"></td> -->
	    </tr>
	</table>
	</form>
</td>
</tr>
</table>
</body>
</html>
