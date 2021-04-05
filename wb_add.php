<?
/*#########################################
# 시스템명 : 스마트보고 시스템            			 #
# 작 성 일 : 2021.04.05                    #
# 파 일 명 : wb_add.php                 	 #
# 기능설명 : 스마트 보고 등록  	     			 #
#########################################*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
$sToday 	= getRequestString("write_date");
$sReqTime   = getRequestString("reqTime");
$sRepEmpno   = getRequestString("rep_empno");

// $qry_user = "SELECT to_char(sysdate,'YYYY-MM-DD HH24:MI') NOW, NAME, DPRT_NAME, PSTN_NAME, GRADE_NAME, TRNS_DPRT_KEY FROM SMART_AUTH_SYNC WHERE SABUN='$sEmpno'";
$qry_user = " SELECT to_char(sysdate,'YYYY-MM-DD HH24:MI') NOW, a.USER_KNM as NAME";
$qry_user .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN a.USER_POSIT_CD ELSE a.USER_RPSWRK_CD END AS DPRT_NAME";
$qry_user .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD";
$qry_user .= "        WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD";
$qry_user .= "        ELSE b.org_nm || '장' END AS PSTN_NAME,";
$qry_user .= " a.USER_POSIT_CD as GRADE_NAME, a.ORG_CD as TRNS_DPRT_KEY";
$qry_user .= " FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd WHERE a.user_id = '$sEmpno'";

$oci -> parseExec($qry_user);
$ret = $oci -> fetch();

$e = 1;
if ($ret == 1) {
	$sToDate            = $oci -> result($e++);
	$sToDate 			= charReplace($sToDate);
	$sUserName 			= $oci -> result($e++);
	$sDeptName 			= $oci -> result($e++);
	$sPstnName 			= $oci -> result($e++);
	$sGradeName 		= $oci -> result($e++);
	$sTrns_dprt_key 	= $oci -> result($e++);
}

// echo $sToday; // 2021-03-20
// echo $sRep_time1; // 14:00:00

// $qry_payuser = "SELECT NAME, PSTN_NAME, DPRT_NAME FROM SMART_AUTH_SYNC WHERE SABUN='$sRepEmpno'";
$qry_payuser = "SELECT a.USER_KNM as NAME";
$qry_payuser .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD";
$qry_payuser .= " 	   WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD";
$qry_payuser .= " 	   ELSE b.org_nm || '장' END AS GRADE_NAME";
$qry_payuser .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN a.USER_POSIT_CD ELSE a.USER_RPSWRK_CD END AS DPRT_NAME";
$qry_payuser .= " FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd WHERE a.user_id = '$sRepEmpno'";

$oci -> parseExec($qry_payuser);
$ret3 = $oci -> fetch();
if ($ret3 == 1) {
	$aPayName 			    = $oci -> result(1);
	$aPstnName 			    = $oci -> result(2);
	$aDprtName 			    = $oci -> result(3);	
}

$oci -> parseFree();

$oci -> disconnect();
$roci -> disconnect();

?>
<link href="CSS/imp.css" rel="stylesheet" type="text/css" />
</head>
<script language="javascript">
	/* 폼 체크 */
	function frmCheck() {
		var f = document.frm;
		var BogoEmpno = document.getElementsByName("BogoEmpno")[0].value;

		var title = f.title.value;
		if (title.length == 0) {
			alert('제목을 입력하지 않았습니다.');
			return false;
		}

		if (f.hour1.value == "" || f.min1.value == "") {
			alert('보고 시작 시간을 입력해 주세요.');
			return;
		}
		if (f.hour2.value == "" || f.min2.value == "") {
			alert('보고 종료 시간을 입력해 주세요.');
			return;
		}

		if (Number(f.hour1.value) > Number(f.hour2.value)) {
			f.hour1.focus();
			alert('보고 종료 시간보다 보고 시작 시간이 큽니다.');
			return;
		}

		if (Number(f.hour1.value) < 8 || Number(f.hour1.value) > 18) {
			f.hour1.focus();
			alert('보고시간은 08시부터 18시끼지 입니다.');
			return;
		}

		if (Number(f.hour2.value) < 8 || Number(f.hour2.value) > 18) {
			f.hour2.focus();
			alert('보고시간은 08시부터 18시끼지 입니다.');
			return;
		}

		if (Number(f.min1.value) > 59) {
			f.min1.focus();
			alert('59분 이하로 입력해주세요.');
			return;
		}

		if (Number(f.min2.value) > 59) {
			f.min2.focus();
			alert('59분 이하로 입력해주세요.');
			return;
		}

		if (Number(f.hour1.value) == Number(f.hour2.value) && Number(f.min1.value) > Number(f.min2.value)) {
			f.min1.focus();
			alert('보고 종료 분보다 보고 시작 분이 큽니다.');
			return;
		}

        /*
		if (Number('??= $sToday; ?>'.replace(/－/gi, '')) ? Number('??= $sToDate; ?>'.replace(/－/gi, '').substring(0, 8)) || Number('??= $sToday; ?>'.replace(/－/gi, '')) == Number('??= $sToDate; ?>'.replace(/－/gi, '').substring(0, 8)) && Number(f.hour1.value) < Number('<?= $sToDate; ?>'.substring(11, 13))) {
			alert('지난시간에는 보고 시작 할 수 없습니다.');
			return;
		}
        */
       
		if (BogoEmpno.length == 0) {
			alert('보고자를 선택하지 않았습니다.');
			return false;
		}

		if ((Number(f.hour2.value) - Number(f.hour1.value)) * 60 + Number(f.min2.value) - Number(f.min1.value) > 30) {
			if (!confirm('기본 보고시간은 30분입니다. 진행하시겠습니까?')) {
				return;
			}
		}
		frm.action = "./wb_add_ok.php"
		frm.submit();
	}

	function Paygo() {
		var frm = document.forms[0];
		window.open('./info_inwon_nsso.php', 'insa', 'width=650,height=500,menubar=no,center=yes,scrollbars=yes,help=no,status=no,resizable=yes,top=100,left=500');
	}

	function frmCancel() {
		var frm = document.forms[0];
		frm.reset();
		history.back();
	}
	// 개별 요소 처리
	/*
	function objEnabled(obj, val) {
		var frm = document.forms[0];
		var arrName = obj.split(":");

		for (var i = 0; i < arrName.length; i++) {
			for (var j = 0; j < frm.elements.length; j++) {
				var cmpName = String(frm.elements[j].name);

				if (arrName[i] == cmpName) {
					frm.elements[j].disabled = val;
					break;
				}
			}
		}
	}
	*/
	
	/* textarea 입력길이 체크 */
	function sizeCheck(obj, size) {
		if (obj.value.length > size) {
			alert("내용이 " + size + "자를 초과하였습니다.");
			obj.value = obj.value.substring(0, size - 2);
			obj.focus();
		}
	}

	function isNumber(obj) {
		var objRule = /[^0-9]/g;
		if (objRule.test(obj.value) == true) {
			alert("숫자만 입력하세요.");
			obj.value = obj.value.substring(0, obj.value.length - 1);
		}
	}

	function change_name() {
		var input_name = document.getElementsByName("user_name")[0].value;
		var div_name = document.getElementById("div_lct_name");
		div_name.innerHTML = input_name;
	}

	var fileCnt = 1;

	function appendFile(group_id, file_id) {
		var group_ctl = document.getElementById(group_id);
		var length = group_ctl.rows.length;
		var row = group_ctl.insertRow(length);
		var cell = row.insertCell();
		fileCnt = fileCnt + 1;
		if (fileCnt < 4) {
			cell.innerHTML = '&nbsp;&nbsp;&nbsp;<input type="file" id="upfile' + fileCnt + '" name="upfile' + fileCnt + '" style="width:400px; margin:3px;" onKeyDown="blur();">';
		} else {
			alert('첨부파일 추가는 3개까지만 가능합니다.');
		}
	}

	//파일 라인
	function deleteFile(group_id, file_id) {
		var group_ctl = document.getElementById(group_id);
		var length = group_ctl.rows.length;
		if (fileCnt > 1) fileCnt = fileCnt - 1;
		if (length > 1) {
			var row = group_ctl.deleteRow(length - 1);
		}
	}

</script>
<style type="text/css">
	/* 
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
} */

</style>

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
			<td valign="top">
				<form name="frm" method="post" enctype="multipart/form-data" onSubmit="return false;">
					<input type="hidden" name="sdate" value="<?= $sToday; ?>">
					<input type="hidden" name="username" value="<?= $sUserName; ?>">
					<input type="hidden" name="dept_name" value="<?= $sDeptName; ?>">
					<input type="hidden" name="grade_name" value="<?= $sGradeName; ?>">
					<input type="hidden" name="empno" value="<?= $sEmpno; ?>">
					<input type="hidden" name="BogoEmpno" id="BogoEmpno" value="<?= $sEmpno; ?>">
					<input type="hidden" name="approvalEmpno" value="<?= $sRepEmpno ?>">
					<input type="hidden" name="scYn" value="N" />
					<input type="hidden" name="sysType" value="Y" />
					<input type="hidden" name="retYear" id="retYear" value="" />
					<input type="hidden" name="testUserId" id="testUserId" value="<?= $sEmpno; ?>" />
					<input type="hidden" name="dprt_key" id="dprt_key" value="<?= $sTrns_dprt_key; ?>" />
					<table width="700" border="0" cellpadding="3" cellspacing="0" style="border:1px solid #E2E2E2;border-collapse:collapse;font-size:12px;">
						<col width="130">
						<col />
						<tr>
							<td class="table_td" style="border-bottom:1px solid #E2E2E2;">보고대상</td>
							<td id="payname" align="left" style="border-bottom:1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;<b><span id="approvalName" name="approvalName" style="font-size: 15px; font-weight:800;"><?= $aPstnName; ?> <?= $aPayName; ?></span></b>
								<!--&nbsp;<img src="./images/bt16.gif" border="0" style="cursor:pointer;" onClick="Paygo();">-->
							</td>
						</tr>
						<tr>
							<td class="table_td" style="border-bottom:1px solid #E2E2E2;">보고제목</td>
							<td align="left" style="border-bottom:1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;<input type="text" name="title" class="input_title" size="50" maxlength="200" /></td>
						</tr>
						<tr>
							<td class="table_td" style="border-bottom:1px solid #E2E2E2;">신청자</td>
							<td align="left" style="border-bottom:1px solid #E2E2E2;"><span style="font-size: 15px; font-weight:800;">&nbsp;&nbsp;&nbsp;<?= $sPstnName; ?>&nbsp;<?= $sUserName; ?></span></td>
						</tr>
						<tr>
							<td class="table_td" style="border-bottom:1px solid #E2E2E2;">보고자</td>
							<td id="payname" align="left" style="border-bottom:1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;
								<span id="bogoName" name="bogoName" style="font-size: 15px; font-weight:800;"><?= $sPstnName; ?>&nbsp;<?= $sUserName; ?>&nbsp;&nbsp;
								<!-- &nbsp;<img src="./images/bt16.gif" border="0" style="cursor:pointer;" onClick="Paygo();"></span> -->
								<span class="bt_confirm" onclick="Paygo();">선택</span>
							</td>
						</tr>
						<tr>
							<td class="table_td" style="border-bottom:1px solid #E2E2E2;">일시</td>
							<td style="border-bottom:1px solid #E2E2E2;">
								<table border="0" cellpadding="0" cellspacing="0">
									<tr>
										<td align="left">&nbsp;&nbsp;&nbsp;<input type="text" name="hour1" class="input_time" maxlength="2" value="<?= $sReqTime; ?>" onKeyUp="return isNumber(this);"><span style="font-size: 15px; font-weight:800;">시</span>
											<input type="text" name="min1" class="input_time" maxlength="2" value="00" onKeyUp="return isNumber(this);"><span style="font-size: 15px; font-weight:800;">분 ~ </span>
											<input type="text" name="hour2" class="input_time" maxlength="2" value="<?= $sReqTime ?>" onKeyUp="return isNumber(this);"><span style="font-size: 15px; font-weight:800;">시</span>
											<input type="text" name="min2" class="input_time" maxlength="2" value="30" onKeyUp="return isNumber(this);"><span style="font-size: 15px; font-weight:800;">분</span>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="table_td" style="border-bottom:1px solid #E2E2E2;">제목공개</td>
							<td align="left" style="border-bottom:1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;<input type="radio" name="openYn" value="Y" /><span style="font-size: 15px; font-weight:800;">공개&nbsp;<input type="radio" name="openYn" value="N" checked />비공개</span></td>
						</tr>
						<tr>
							<td class="table_td" style="border-bottom:1px solid #E2E2E2;">첨부파일공개</td>
							<td align="left" style="border-bottom:1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;<input type="radio" name="fileYn" value="Y" /><span style="font-size: 15px; font-weight:800;">공개&nbsp;<input type="radio" name="fileYn" value="N" checked />비공개</span></td>
							</td>
						<tr>
							<td class="table_td">첨부파일</td>
							<td align="left" style="border-bottom:1px solid #E2E2E2">
								<table width="90%" border=0 id="file_group1" class="inner_table">
									<tr>
										<td>&nbsp;&nbsp;&nbsp;<input type="file" id="upfile1" name="upfile1" style="width:400px; margin:3px; vertical-align:middle;" onKeyDown="blur();" />
											<a href="javascript:appendFile('file_group1', 'file1');"><img src="./images/btn_plus.gif" align="middle" /></a>&nbsp;
											<a href="javascript:deleteFile('file_group1', 'file1');"><img src="./images/btn_del.gif" align="middle" /></a>
											<!-- <a href="javascript:appendFile('file_group1', 'file1');"><span class="bt_confirm">+ 추가</span></a>
											<a href="javascript:deleteFile('file_group1', 'file1');"><span class="bt_confirm">- 삭제</span></a> -->
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</tr>
				</table>
				<table width="700" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<!-- <td align="center" height="50"><img src="./images/bt11.gif" border="0" style="cursor:pointer;" onClick="return frmCheck();">&nbsp;&nbsp;&nbsp;<img src="./images/bt02.gif" border="0" style="cursor:pointer;" onClick="self.close();"></td> -->
						<td align="center" height="80">
							<!-- <img src="./images/bt11.gif" border="0" style="cursor:pointer;" onClick="return frmCheck();">&nbsp;&nbsp;&nbsp;
							<img src="./images/bt02.gif" border="0" style="cursor:pointer;" onClick="self.close();"> -->
							<span class="bt_summit" onClick="return frmCheck();">신청</span>&nbsp;&nbsp;&nbsp;
							<span class="bt_summit" onClick="self.close();">취소</span>
						</td>
					</tr>
				</table>
			</form>
		</td>
	</tr>
	</table>
</body>

</html>