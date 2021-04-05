<?
/*#########################################
# 시스템명 : 스마트보고
# 작 성 일 : 2021.04.05
# 파 일 명 : wb_mod.php
# 기능설명 : 스마트보고 등록 수정
#########################################*/
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<?
// 공통헤더
include "./include/env.inc.php";
// 공통헤더
include "./include/head_inc.php";
include "./include/function.inc.php";
// DB 접속
include "./include/oci8.inc.php";
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
	$nRet = isViewable_rep($sEmpno,$sRepid);
	if ($nRet <= 0) {
		echo "조회권한이 없습니다.";
		$oci -> disconnect();
		$roci -> disconnect();
		exit;
	}
}
$sTable_name01 = "KOSPOWB_REPORT";
$sTable_name02 = "KOSPOWB_FILE";
// 조회내용
$qry_view = "SELECT repid,empno,title,username,dept_name,grade_name,bogo_empno,rep_empno,rep_date1,rep_time1,rep_date2,rep_time2,rep_content,open_yn,file_yn,gubun,stats, to_char(reg_date,'YYYY-MM-DD') regdate FROM $sTable_name01 WHERE EMPNO='$sEmpno' AND REPID=$sRepid";
$oci -> parseExec($qry_view);
$nIdx = 1;
if ($oci -> fetch()) {
	$sRepid = $oci -> result($nIdx++); 
	$sEmpno = $oci -> result($nIdx++); 
	$sTitle = $oci -> result($nIdx++); 
	$sUsername = $oci -> result($nIdx++); 
	$sDept_name = $oci -> result($nIdx++); 
	$sGrade_name = $oci -> result($nIdx++);
	$sBogo_empno = $oci -> result($nIdx++); 
	$sRep_empno = $oci -> result($nIdx++); 
	$sRep_date1 = viewReplace($oci -> result($nIdx++)); // 날짜1
	$sRep_time1 = $oci -> result($nIdx++); // 시간1
	$sRep_date2 = viewReplace($oci -> result($nIdx++)); // 날짜2
	$sRep_time2 = $oci -> result($nIdx++); // 시간2
	$sRep_content = viewReplace($oci -> result($nIdx++)); 
	$sOpenYn = $oci -> result($nIdx++);
	$sFileYn = $oci -> result($nIdx++);	
	$sGubun = $oci -> result($nIdx++);
	$sStats = $oci -> result($nIdx++);	
	$sRegdate = $oci -> result($nIdx++); // 등록일시
	$aRep_time1 = explode(":",$sRep_time1);
	$aRep_time2 = explode(":",$sRep_time2);
}

// YYYY-MM-DD HH24:MI
// echo $sToday; 
// echo sToDate; 
// echo $sRep_date1; // 2021-03-20
// echo $sRep_time1; // 14:00:00

// 첨부파일
$sAttcdFile = null;
$qry_file = "SELECT * FROM $sTable_name02 WHERE WBID=$sRepid AND GUBUN='B' ORDER BY FILEID, SEQ";
$oci -> parseExec($qry_file);
$nFileIdx = 0;
// $sAttcdFile = array();
while($col = $oci -> fetchInto()) {
	$sAttcdFile[$nFileIdx] = $col["UPLOAD_NAME"];
	$sAttcdFileId[$nFileIdx] = $col["FILEID"];
	$sAttcdFileSeq[$nFileIdx++] = $col["SEQ"];
}
$oci -> parseFree();

// 접속자 정보
// $qry_user = "SELECT to_char(sysdate,'YYYY-MM-DD HH24:MI') NOW, TRNS_DPRT_KEY FROM SMART_AUTH_SYNC WHERE SABUN='$sEmpno'";
$qry_user = " SELECT to_char(sysdate,'YYYY-MM-DD HH24:MI') NOW, a.USER_KNM as NAME";
$qry_user .= " ,a.ORG_CD as TRNS_DPRT_KEY";
$qry_user .= " FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd WHERE a.user_id = '$sEmpno'";

$oci -> parseExec($qry_user);
$ret = $oci -> fetch();

if ($ret == 1) {
	$sToDate            = $oci -> result(1);
	$sToDate 			= charReplace($sToDate);
	$sTrns_dprt_key 	= $oci -> result(2);
}

// $qry_user = "SELECT NAME, PSTN_NAME FROM SMART_AUTH_SYNC WHERE SABUN='$sRep_empno'";
$qry_user = "SELECT a.USER_KNM as NAME";
$qry_user .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD";
$qry_user .= "        WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD";
$qry_user .= "        ELSE b.org_nm || '장' END AS PSTN_NAME";
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
$qry_user .= " 	   ELSE b.org_nm || '장' END AS GRADE_NAME";
$qry_user .= " , CASE WHEN a.USER_RPSWRK_CD IS NULL THEN a.USER_POSIT_CD ELSE a.USER_RPSWRK_CD END AS DPRT_NAME";
$qry_user .= " FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd WHERE a.user_id = '$sBogo_empno'";
$oci -> parseExec($qry_user);
$ret = $oci -> fetch();
if ($ret == 1) {
	$sBogoName = $oci -> result(1);
	$sBogoPstnName = $oci -> result(2);
}

$oci -> parseFree();
$oci -> disconnect();
$roci -> disconnect();
?>
<link href="CSS/imp.css" rel="stylesheet" type="text/css" />
</head>

<script language="javascript">
// var aFormName = new Array("title", "username", "dept_name", "grade_name", "sdate", "hour1", "min1",
    //     "edate", "hour2", "min2", "rep_content");
    // var aFormTitle = new Array("보고제목", "성명", "소속", "직급", "시작일자", "종료일자", "시작시간(시)", "시작시간(분)", "종료시간(시)",
    //     "종료시간(분)", "보고내용");

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

        // YYYY-MM-DD HH24:MI
        // $sRep_date1; // 2021-03-20
        // $sRep_time1; // 14:00:00
        // $sToday = $sRep_date1 + " " + $sRep_time1;
        // $sToDate = $sRep_date1 + " " + $sRep_time1;
        // console.log($sToday);
        /*
        if (Number('??= $sToday; ?>'.replace(/－/gi, '')) ? Number('??= $sTo_date; ?>'.replace(/－/gi, '').substring(0, 8)) || 
            Number('??= $sToday; ?>'.replace(/－/gi, '')) == Number('??= $sTo_date; ?>'.replace(/－/gi, '').substring(0, 8)) && 
            Number(f.hour1.value) < Number('<?= $sTo_date; ?>'.substring(11, 13))) {
            alert('지난시간에는 보고 시작 할 수 없습니다.');
            return;
        }
        */

        if (BogoEmpno.length == 0) {
            alert('보고자를 선택하지 않았습니다.');
            return false;
        }

        if ((Number(f.hour2.value) - Number(f.hour1.value)) * 60 + Number(f.min2.value) - Number(f.min1
                .value) > 30) {
            if (!confirm('보고 시간이 30분 이상입니다. 진행하시겠습니까?')) {
                return;
            }
        }

        frm.action = "./wb_mod_ok.php";
        frm.submit();
    }

    function Paygo() {
        var frm = document.forms[0];
        window.open('./info_inwon_nsso.php', 'insa', 'width=650,height=500,menubar=no,center=yes,scrollbars=yes,help=no,status=no,resizable=yes,top=200,left=500');
    }

    function frmCancel() {
        var frm = document.forms[0];
        frm.reset();
        history.back();
    }

    /*
    // 개별 요소 처리
    function objEnabled(obj, val) {
        var frm = document.forms[0];
        var arrName = obj.explode(":");

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

    var fileCnt = <?= count($sAttcdFile) + 1; ?>;
    if (fileCnt < 4){
            cell.innerHTML = '&nbsp;<input type="file" id="upfile' + fileCnt + '" name="upfile' + fileCnt +
                '" style="width:400px;" onKeyDown="blur();">';
        }else{
            alert('첨부파일 추가는 3개까지만 가능합니다.');
        }


    function appendFile(group_id, file_id) {
        var group_ctl = document.getElementById(group_id);
        var length = group_ctl.rows.length;
        var row = group_ctl.insertRow(length);
        var cell = row.insertCell();
        fileCnt = fileCnt + 1;
        
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
                    <input type="hidden" name="sysType" value="Y" />
                    <input type="hidden" name="retYear" id="retYear" value="" />
                    <input type="hidden" name="testUserId" id="testUserId" value="<?= $sBogo_empno; ?>" />
                    <input type="hidden" name="dprt_key" id="dprt_key" value="<?= $sTrns_dprt_key; ?>" />
                    <table width="700" border="0" cellpadding="3" cellspacing="0" style="border:1px solid #E2E2E2;border-collapse:collapse;font-size:12px;">
                        <col width="130">
                        <col />
                        <tr>
                            <td class="table_td" style="border-bottom:1px solid #E2E2E2;">보고제목</td>
                            <td align="left" style="border-bottom:1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;<input type="text" name="title" value="<?= $sTitle; ?>" class="input_title" size="50" maxlength="200" /></td>
                        </tr>
                        <tr>
                            <td class="table_td" style="border-bottom:1px solid #E2E2E2;">신청자</td>
                            <td align="left" style="border-bottom:1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;<span style="font-size: 15px; font-weight:800;"><?= $sGrade_name; ?>&nbsp;<?= $sUsername; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="table_td" style="border-bottom:1px solid #E2E2E2;">보고자</td>
                            <td id="payname" align="left" style="border-bottom:1px solid #E2E2E2;">
                                &nbsp;&nbsp;&nbsp;<span id="bogoName" name="bogoName" style="font-size: 15px; font-weight:800;"><?= $sBogoPstnName; ?>&nbsp;<?= $sBogoName; ?></span>&nbsp;&nbsp;
                                <!-- &nbsp;<img src="./images/bt16.gif" border="0" style="cursor:pointer;" onClick="Paygo();"> -->
                                <span class="bt_confirm" onclick="Paygo();">선택</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="table_td" style="border-bottom:1px solid #E2E2E2;">보고대상</td>
                            <td id="payname" align="left" style="border-bottom:1px solid #E2E2E2;">
                                &nbsp;&nbsp;&nbsp;<span id="approvalName" name="approvalName" style="font-size: 15px; font-weight:800;"><?= $sPstnName; ?>
                                    <?= $sPayName; ?></span>
                                <!--<img src="./images/bt16.gif" border="0" style="cursor:hand;" onClick="Paygo();">-->
                            </td>
                        </tr>
                        <tr>
                            <td class="table_td" style="border-bottom:1px solid #E2E2E2;">일시</td>
                            <td align="left" style="border-bottom:1px solid #E2E2E2;">
                                &nbsp;&nbsp;&nbsp;<input type="text" name="hour1" class="input_time" maxlength="2" value="<?= $aRep_time1[0]; ?>" onKeyUp="return isNumber(this);"><span style="font-size: 15px; font-weight:800;">시</span>
                                <input type="text" name="min1" class="input_time" maxlength="2" value="<?= $aRep_time1[1]; ?>" onKeyUp="return isNumber(this);"><span style="font-size: 15px; font-weight:800;">분 ~</span>
                                <input type="text" name="hour2" class="input_time" maxlength="2" value="<?= $aRep_time2[0]; ?>" onKeyUp="return isNumber(this);"><span style="font-size: 15px; font-weight:800;">시</span>
                                <input type="text" name="min2" class="input_time" maxlength="2" value="<?= $aRep_time2[1]; ?>" onKeyUp="return isNumber(this);"><span style="font-size: 15px; font-weight:800;">분</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="table_td" style="border-bottom:1px solid #E2E2E2;">제목공개</td>
                            <td align="left" style="border-bottom:1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;<input type="radio" name="openYn" value="Y" <? if($sOpenYn=='Y' ){echo "checked" ;} ?>/><span style="font-size: 15px; font-weight:800;">공개&nbsp;<input type="radio" name="openYn" value="N" <? if($sOpenYn=='N' ){echo "checked" ;} ?>/>비공개</span>
                            </td>
            </td>
        </tr>
        <tr>
            <td class="table_td" style="border-bottom:1px solid #E2E2E2;">첨부파일공개</td>
            <td align="left" style="border-bottom:1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;<input type="radio" name="fileYn" value="Y" <? if($sFileYn=='Y' ){echo "checked" ;} ?>/><span style="font-size: 15px; font-weight:800;">공개&nbsp;<input type="radio" name="fileYn" value="N" <? if($sFileYn=='N' ){echo "checked" ;} ?>/>비공개</span></td>
            </td>
        </tr>
        <tr>
            <td class="table_td">첨부파일</td>
            <td align="left" style="border-bottom:1px solid #E2E2E2">
                <table width="90%" border=0 id="file_group1" class="inner_table">
                    <tr>
                        <td>&nbsp;&nbsp;&nbsp;<input type="file" id="upfile1" name="upfile1" style="width:400px; margin:3px; vertical-align:middle;" onKeyDown="blur();" />
                            <a href="javascript:appendFile('file_group1', 'file1');"><img src="./images/btn_plus.gif" align="middle" /></a>&nbsp;
                            <a href="javascript:deleteFile('file_group1', 'file1');"><img src="./images/btn_del.gif" align="middle" /></a>
                        </td>
                    </tr>
                </table>
                <? for($i=0; $i < count($sAttcdFile); $i++){ ?>
                <? if ($sAttcdFile[$i] != "") { ?>&nbsp;<?= $sAttcdFile[$i]; ?> <input type="checkbox" name="delfile<?= ($i + 1); ?>" value="<?= $sAttcdFileId[$i]; ?>"> 삭제
                <? } ?><br />
                <? } ?>
            </td>
        </tr>
    </table>
    <table width="700" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <!-- <td align="center" height="50"><img src="./images/bt11.gif" border="0" style="cursor:pointer;" onClick="return frmCheck();">&nbsp;&nbsp;&nbsp;<img src="./images/bt02.gif" border="0" style="cursor:pointer;" onClick="self.close();"></td> -->
            <td align="center" height="80">
                <!-- <img src="./images/bt11.gif" border="0" style="cursor:pointer;" onClick="return frmCheck();">&nbsp;&nbsp;&nbsp;
				<img src="./images/bt02.gif" border="0" style="cursor:pointer;" onClick="self.close();"> -->
                <span class="bt_summit" onClick="return frmCheck();">저장</span>&nbsp;&nbsp;&nbsp;
                <span class="bt_summit" onClick="self.close();">취소</span>
            </td>
        </tr>
    </table>
    <input type="hidden" name="empno" value="<?= $sEmpno; ?>">
    <input type="hidden" name="username" value="<?= $sUsername; ?>">
    <input type="hidden" name="dept_name" value="<?= $sDept_name; ?>">
    <input type="hidden" name="grade_name" value="<?= $sGrade_name; ?>">
    <input type="hidden" name="BogoEmpno" id="BogoEmpno" value="<?= $sBogo_empno; ?>">
    <input type="hidden" name="repid" value="<?= $sRepid; ?>">
    <input type="hidden" name="sdate" value="<?= $sRep_date1; ?>">
    <input type="hidden" name="approvalEmpno" id="approvalEmpno" value="<?= $sRep_empno; ?>">
    <input type="hidden" name="sEmpNo" value="<?= $sEmpno; ?>">
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