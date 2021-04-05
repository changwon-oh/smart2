<?
/*#########################################
# 시스템명 : 스마트보고 시스템 
# 작 성 일 : 2021.04.05        
# 파 일 명 : wb_list.php       
# 기능설명 : 스마트보고 목록   
#########################################*/
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/:qxhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<META http-equiv="Expires" content="-1">
<META http-equiv="Pragma" content="no-cache">
<META http-equiv="Cache-Control" content="No-Cache">
<META charset="utf-8">
<META http-equiv="X-UA-Compatible" content="IE=edge, chrome=1">

<?php
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

$dateInclease = '';
$sWrite_date = '';
$test = 0;

isset($_POST['dateInclease']) ? $dateInclease = trim($_POST['dateInclease']) : "";
isset($_POST['write_date']) ? $sWrite_date = trim($_POST['write_date']) : "0";
isset($_SESSION["rep_empno"]) ? $rep_empno = $_SESSION["rep_empno"] : "";
isset($_POST['rep_empno']) ? $rep_empno = trim($_POST['rep_empno']) : "";
$_SESSION["rep_empno"] = $rep_empno;

// 인증정보
include "./include/sso_auth.inc.php";

// 개인별 등록/신고내역 목록
$sTable_name01 = "KOSPOWB_REPORT";

if($dateInclease == 'plus'){
	$test = '+1';
} else if($dateInclease == 'minus'){
	$test = '-1';
} else {
	$test = '+0';
}

if(strlen($sWrite_date) == 0){
	$qry_date = "select to_char(sysdate,'YYYY-MM-DD HH24:MI') now, to_char(sysdate,'YYYY-MM-DD') as sys, decode(to_char(sysdate, 'D'),1,'일',2,'월',3,'화',4,'수',5,'목',6,'금','토') day from dual";
} else {
	$qry_date = "select to_char(sysdate,'YYYY-MM-DD HH24:MI') now, to_char(to_date(substr('$sWrite_date',1,4)||substr('$sWrite_date',6,2)||substr('$sWrite_date',9,2),'YYYYMMDD')$test, 'YYYY-MM-DD') as sys, decode(to_char(to_date(substr('$sWrite_date',1,4)||substr('$sWrite_date',6,2)||substr('$sWrite_date',9,2),'YYYYMMDD')$test, 'D'),1,'일',2,'월',3,'화',4,'수',5,'목',6,'금','토') day from dual";
}

$oci -> parseExec($qry_date);
$nIdx = 1;

if ($oci -> fetch()) {
  $sTo_date = $oci -> result($nIdx++);
  $sTo_date = $sTo_date;
  $sWrite_date = $oci -> result($nIdx++);
  $sWriteDay = $oci -> result($nIdx++);
  $sWrite_date = $sWrite_date;
}

$oci -> parseFree();

// 관리자 전체 조회/승인대기,반려 구분/게시물 카운트
// $qry_payuser = "SELECT PSTN_NAME FROM SMART_AUTH_SYNC WHERE SABUN='$rep_empno'";
$qry_payuser = "SELECT CASE WHEN a.USER_RPSWRK_CD IS NULL THEN b.org_nm || ' ' || a.USER_POSIT_CD";
$qry_payuser .= " 	   WHEN a.USER_POSIT_CD IN ('사장','부사장') THEN USER_POSIT_CD";
$qry_payuser .= " 	   ELSE b.org_nm || '장' END AS PSTN_NAME";
$qry_payuser .= " FROM CO_USER a inner JOIN co_org b ON a.org_cd = b.org_cd WHERE a.user_id = '$rep_empno'";

$oci -> parseExec($qry_payuser);
$ret3 = $oci -> fetch();
if ($ret3 == 1) {
	$aPstnName = $oci -> result(1);
}
$oci -> parseFree();

$oci -> disconnect();
$roci -> disconnect();
?>
<!-- <meta http-equiv="X-UA-Compatible" content="IE=EmulateIE9" /> -->
<script src="./JS/common.js"></script>
<script src="./JS/jquery-1.7.2.min.js"></script>
<title>스마트 보고관리(일반사용자)</title>
<link href="CSS/imp.css" rel="stylesheet" type="text/css" />
<style type="text/css">
p.menu_bt01 a {
    padding: 5px 13px;
    background-color: #002d5f;
    color: #ffffff;
    float: right;
    display: block;
    margin-top: -50px;
}

p.menu_bt02 a {
    padding: 3px 10px;
    background-color: #002d5f;
    color: #ffffff;
    float: right;
    margin-right: 20px;
    display: block;
    margin-top: -45px;
}
</style>
<script language="javascript">

function fnInit() {
    fnSetData();
    setInterval(fnSetData, 3500);
}

function fnGo(incleas) {
    var f = document.frm;
    f.dateInclease.value = incleas;
    f.action = "wb_list.php";
    f.submit();
}

function fnGoToday() {
    var f = document.frm;
    f.write_date.value = '';
    f.dateInclease.value = '';
    f.action = "wb_list.php";
    f.submit();
}

function fnChange(repEmpno) {
    var f = document.frm;
    // f.write_date.value = '';
    f.dateInclease.value = '';
    f.rep_empno.value = repEmpno;
    f.action = "wb_list.php";
    f.submit();
}

function fnSetData() {
    $.ajax({
        async: true,
        type: 'POST',
        url: 'ajaxList.php',
        //data: paramSub,
        data: {
            'sWrite_date': '<?=$sWrite_date;?>',
            'sRep_empno': '<?=$rep_empno;?>',
            'sEmpNo': '<?=$sEmpno;?>'
        },
        dataType: "json",
        success: function(json) {
            //   alert(json);  		    
            var onCnt = 0;
            for (var j = 0; j < json.length; j++) {
                if (json[j]['gubun'] == '0' && json[j]['stats'] == 'on') {
                    onCnt++;
                }
            }

            var retHtml = "";
            var retHtml2 = "";
            retHtml += "<table align=\"center\" width=\"100%\">";
            retHtml += "<colgroup>";
            retHtml += "    <col width=\"5%\"/>";
            retHtml += "    <col width=\"*\"/>";
            retHtml += "    <col width=\"7%\"/>";
            retHtml += "    <col width=\"7%\"/>";
            retHtml += "    <col width=\"8%\"/>";
            retHtml += "    <col width=\"7%\"/>";
            retHtml += "</colgroup>";
            retHtml += "<tbody>";
            retHtml += "  <tr class=\"table_header\">";
            retHtml += "      <td height=\"50px\" style=\"border-right:2px solid #E9F4F9\">시간</td>";
            retHtml += "      <td>보고내용</td>";
            retHtml += "      <td style=\"border-left:2px solid #E9F4F9\">시작시간</td>";
            retHtml += "      <td style=\"border-left:2px solid #E9F4F9\">종료시간</td>";
            retHtml += "      <td style=\"border-left:2px solid #E9F4F9\">보고상태</td>";
            retHtml += "      <td style=\"border-left:2px solid #E9F4F9\"></td>";
            retHtml += "  </tr>";
            for (var i = 8; i < 19; i++) {
                var hour = i;
                if (hour < 10)
                    hour = '0' + i;
                var back = "";
                if (hour == 12) {
                    back = " style='background-color:#f0f0f0;'"
                }
                retHtml += "<tr " + back + ">";
                retHtml += "    <td class=\"td_time\">" + hour + "</td>";
                retHtml += "    <td class=\"td_content\">";
                if (json != null) {
                    var tempHtml = "";
                    var tempCnt = 0;
                    if (json.length > 0) {
                        tempHtml += "<table width=\"100%\">";
                        for (var j = 0; j < json.length; j++) {
                            var timeTxt = "time_off";
                            var rep_time = json[j]['rep_time1'];
                            var p = json[j]['rep_time1'].indexOf(":");
                            if (i == rep_time.substring(0, p)) {
                                var colorTxt = "";
                                var long = 0;
                                var longTxt = "";
                                var attachTxt = "";
                                tempHtml += "<tr>";
                                var tempTime = json[j]['rep_time1'].substring(3, 5) + "분 ~ " + json[j][
                                    'rep_time2'
                                ].substring(3, 5) + "분";
                                if (json[j]['rep_time1'].substring(0, 2) != json[j]['rep_time2'].substring(
                                        0, 2)) {
                                    tempTime = json[j]['rep_time1'].substring(3, 5) + "분~" + json[j][
                                        'rep_time2'
                                    ].substring(0, 2) + "시" + json[j]['rep_time2'].substring(3, 5) + "분";
                                    long = 1;
                                }
                                if (long == 0) {
                                    longTxt = "letter-spacing:2.5px;";
                                }
                                if (json[j]['gubun'] == '0' && json[j]['stats'] == 'on') {
                                    timeTxt = "time_ov";
                                    colorTxt += "style='background:#CD1AD4;" + longTxt + "' ";
                                } else {
                                    if (long == 0)
                                        colorTxt += "style='" + longTxt + "' ";
                                }
                                if (json[j]['file_yn'] == 'Y' && json[j]['fileid'] != null) {
									var tt = json[j]['fileid'].split("#");
                                    for (var k = 0; k < tt.length; k++) {
                                        var kk = tt[k].split("@");
                                        attachTxt += "<a href='./wb_file.php?file=" + kk[0] + "&wbid=" +
                                            json[j]['repid'] + "&seq=" + kk[1] +
                                            "'><img src='images/icon_file_new.gif' border='0' /></a>";
                                    }
                                }
                                var titles = json[j]['title'];
                                // 1. 일정일 경우 2. 신청일 경우
                                // 1-1 본인 일정의 경우에만 내용이 보임
                                if (json[j]['sc_yn'] == 'Y') { // 일정은 파란색 글자로 표시
                                    if (json[j]['open_yn'] == 'N' &&
                                        '<?=$isReport;?>' != 1 ) {
                                        titles = '일정';
                                    }
                                    titles = "<span style='color:blue;'>" + titles + "</span>";
                                } else {
                                    if (json[j]['open_yn'] == 'N' && 
                                        '<?=$sEmpno;?>' != json[j]['empno'] && 
                                        '<?=$isReport;?>' != 1 &&
                                        '<?=$sEmpno;?>' != json[j]['bogo_empno']) { 
                                            titles = '업무보고'; // 제목 비공개의 경우
                                    }
                                }
                                if (json[j]['sc_yn'] != 'Y' && '<?=$sEmpno;?>' == json[j]['empno']) { // 본인것 수정
                                    if (json[j]['gubun'] == '0' && json[j]['stats'] == 'off') {
                                        // titles = "<a href='#' onclick=\"fnView('"+json[j]['repid']+"');\">"+titles+"</a>";
                                        titles = "<a href='#;' onclick=\"fnView('" + json[j]['repid'] + "');\">" + titles;
                                    } else {
                                        // titles = "<a href='#' onclick=\"fnView('"+json[j]['repid']+"');\">"+titles+"</a>";
                                        titles = "<a href='#;' onclick=\"fnView('" + json[j]['repid'] + "');\">" + titles;
                                    }
                                }
                                if (json[j]['open_yn'] == 'N') titles += '<img src=\"images/sec.gif\" border=\"0\" />'; // 비공개 자물쇠
								if (json[j]['open_yn'] == 'N' && '<?=$sEmpno;?>' != json[j]['empno'] && '<?=$sEmpno;?>' != json[j]['bogo_empno']) { // 제목 비공개의 경우
								} else {
									if (json[j]['sc_yn'] != 'Y') titles += "&nbsp;&nbsp;&nbsp;(" + json[j]['bogo_grade_name'] + " " + json[j]['bogo_name'] + ")" + "</a>";
								}
                                
                                tempHtml += "    <td style=\"padding-top:4px;width:200px;\"><div class=\"" +
                                    timeTxt + "\" " + colorTxt + " >" + tempTime + "</div></td><td>&nbsp;" +
                                    titles + "" + attachTxt + "</td>";
                                tempHtml += "</tr>";
                                tempHtml += "<tr><td colspan=\"2\"height=\"5\"></td></tr>";
                                tempCnt++;
                            }
                        }
                        tempHtml += "</table>";
                        if (tempCnt == 0) {
                            tempHtml += "&nbsp;";
                        }
                    }
                }
                retHtml += tempHtml;
                retHtml += "    </td>";
                retHtml += "    <td class=\"td_status\">";
                if (json != null) {
                    var tempHtml3 = "";
                    var tempCnt3 = 0;
                    if (json.length > 0) {
                        tempHtml3 += "<table width=\"100%\">";
                        for (var j = 0; j < json.length; j++) {
                            var rep_time = json[j]['rep_time1'];
                            var p = json[j]['rep_time1'].indexOf(":");
                            if (i == rep_time.substring(0, p)) {
                                tempHtml3 += "<tr>";
                                if (json[j]['stats_date'] != null) {
                                    tempHtml3 += "    <td style=\"padding:4px 12px 8px 4px;\">" + json[j][
                                        'stats_date'
                                    ].substring(0, 5) + "</td>";
                                } else {
                                    tempHtml3 += "    <td style=\"padding:4px 12px 8px 4px;\">&nbsp;</td>";
                                }
                                tempHtml3 += "</tr>";
                                tempHtml3 += "<tr><td height=\"5\"></td></tr>";
                                tempCnt3++;
                            }
                        }
                        tempHtml3 += "</table>";
                        if (tempCnt3 == 0) {
                            tempHtml3 += "&nbsp;";
                        }
                    }
                }
                retHtml += tempHtml3;
                retHtml += "    </td>";
                retHtml += "    <td class=\"td_status\">";
                if (json != null) {
                    var tempHtml4 = "";
                    var tempCnt4 = 0;
                    if (json.length > 0) {
                        tempHtml4 += "<table width=\"100%\">";
                        for (var j = 0; j < json.length; j++) {
                            var rep_time = json[j]['rep_time1'];
                            var p = json[j]['rep_time1'].indexOf(":");
                            if (i == rep_time.substring(0, p)) {
                                tempHtml4 += "<tr>";
                                if (json[j]['end_date'] != null) {
                                    tempHtml4 += "    <td style=\"padding:4px 12px 8px 4px;\">" + json[j][
                                        'end_date'
                                    ].substring(0, 5) + "</td>";
                                } else {
                                    tempHtml4 += "    <td style=\"padding:4px 12px 8px 4px;\">&nbsp;</td>";
                                }
                                tempHtml4 += "</tr>";
                                tempHtml4 += "<tr><td height=\"5\"></td></tr>";
                                tempCnt4++;
                            }
                        }
                        tempHtml4 += "</table>";
                        if (tempCnt4 == 0) {
                            tempHtml4 += "&nbsp;";
                        }
                    }
                }
                retHtml += tempHtml4;
                retHtml += "    </td>";
                retHtml += "    <td class=\"td_status\">";
                if (json != null) {
                    var tempHtml2 = "";
                    var tempCnt2 = 0;
                    if (json.length > 0) {
                        tempHtml2 += "<table width=\"100%\">";
                        for (var j = 0; j < json.length; j++) {
                            var rep_time = json[j]['rep_time1'];
                            var p = json[j]['rep_time1'].indexOf(":");
                            if (i == rep_time.substring(0, p)) {
                                var colorTxt = "";
                                var stateTxt = "";
                                tempHtml2 += "  <tr>";
                                if (json[j]['dq_yn'] == 'Y') {
                                    colorTxt +=
                                        "style='color:#8D8D8D;padding:4px 12px 8px 12px;cursor:pointer;' onmouseover=\"detailInfoScheView('" +
                                        json[j]['bogo_grade_name'] + " " + json[j]['bogo_name'] +
                                        "','','');\" onmouseout=\"divScheduleClose();\" ";
                                    stateTxt += "서면대체";
                                } else if (json[j]['sc_yn'] == 'Y') {
                                    colorTxt += "style='color:blue;padding:4px 12px 8px 12px;'";
                                    stateTxt += "일정";
                                } else {
                                    if (json[j]['gubun'] == '0' && json[j]['stats'] == '0') {
                                        colorTxt +=
                                            "style='color:#3c3c3c;padding:4px 12px 8px 12px;' onmouseout=\"divScheduleClose();\" ";
                                        stateTxt += "확정";
                                    } else if (json[j]['gubun'] == '0' && json[j]['stats'] == 'on') {
                                        colorTxt +=
                                            "style='color:#CD1AD4;padding:4px 12px 8px 12px;cursor:pointer;' onmouseover=\"detailInfoScheView('" +
                                            json[j]['bogo_grade_name'] + " " + json[j]['bogo_name'] +
                                            "','" + json[j]['stats_date'].substring(0, 5) +
                                            "','');\" onmouseout=\"divScheduleClose();\" ";
                                        stateTxt += "보고중";
                                    } else if (json[j]['gubun'] == '0' && json[j]['stats'] == 'off') {
                                        colorTxt +=
                                            "style='color:#8D8D8D;padding:4px 12px 8px 12px;cursor:pointer;' onmouseover=\"detailInfoScheView('" +
                                            json[j]['bogo_grade_name'] + " " + json[j]['bogo_name'] +
                                            "','" + json[j]['stats_date'].substring(0, 5) + "','" + json[j][
                                                'end_date'
                                            ].substring(0, 5) + "');\" onmouseout=\"divScheduleClose();\" ";
                                        stateTxt += "보고완료";
                                    } else if (json[j]['gubun'] == '2') {
                                        colorTxt +=
                                            "style='color:#F48814;padding:4px 12px 8px 12px;' onmouseout=\"divScheduleClose();\" ";
                                        stateTxt += "반려";
                                    } else {
                                        colorTxt +=
                                            "style='color:#3c3c3c;padding:4px 12px 8px 12px;' onmouseout=\"divScheduleClose();\" ";
                                        stateTxt += "승인대기";
                                    }
                                }
                                if (json[j]['sc_yn'] != 'Y' && '<?=$sEmpno;?>' == json[j]['empno'] && json[
                                        j]['gubun'] == '0') {
                                    if (json[j]['stats'] == 'off') {
                                        stateTxt = "<a href='#;' onclick=\"fnView('" + json[j]['repid'] +
                                            "');\">" + stateTxt + "</a>";
                                    } else if (json[j]['stats'] == '0' || json[j]['gubun'] == '2') {
                                        stateTxt = "<a href='#;' onclick=\"fnView('" + json[j]['repid'] +
                                            "');\">" + stateTxt + "</a>";
                                    }
                                }
                                tempHtml2 += "    <td " + colorTxt + " >" + stateTxt + "</td>";
                                tempHtml2 += "  </tr>";
                                tempHtml2 += "  <tr><td height=\"5\"></td></tr>";
                                tempCnt2++;
                            }
                        }
                        tempHtml2 += "</table>";
                        if (tempCnt2 == 0) {
                            tempHtml2 += "&nbsp;";
                        }
                    }
                }
                retHtml += tempHtml2;
                retHtml += "    </td>";
                if (Number('<?=$sWrite_date;?>'.replace(/-/gi, '')) < Number('<?=$sTo_date;?>'.replace(
                        /-/gi, '').substring(0, 8)) || Number('<?=$sWrite_date;?>'.replace(/-/gi, '')) ==
                    Number('<?=$sTo_date;?>'.replace(/-/gi, '').substring(0, 8)) && Number(hour) < Number(
                        '<?=$sTo_date;?>'.substring(11, 13))) {
                    retHtml += "    <td class=\"td_status\"></td>";
                } else {
                    // retHtml += "    <td class=\"td_status\"><img src=\"./images/bt11.gif\" onclick=\"popup('"+hour+"');\" style=\"cursor:pointer\" border=\"0\"></td>";
                    retHtml += "    <td class=\"td_status\"><span class=\"bt_confirm\" onClick=\"popup('" +
                        hour + "');\">신청</span>&nbsp;</td>";
                }
                retHtml += "  </tr>";
            }
            retHtml += "  </tbody>";
            retHtml += "</table>";
            $("#bogoCont").html(retHtml);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert('검색 대상자가 존재하지 않습니다.');
            alert(thrownError + ':message==' + xhr.responseText);
        }
    });
}

function popup(reqTime) {
    var f = document.frm;
    if (Number('<?=$sWrite_date;?>'.replace(/-/gi, '')) < Number('<?=$sTo_date;?>'.replace(/-/gi, '').substring(0,
        8)) || Number('<?=$sWrite_date;?>'.replace(/-/gi, '')) == Number('<?=$sTo_date;?>'.replace(/-/gi, '').substring(
            0, 8)) && Number(reqTime) < Number('<?=$sTo_date;?>'.substring(11, 13))) {
        alert('지난시간에는 보고할 수 없습니다.');
        return;
    }
    window.open("", "wb_add",
        "width=720,height=500,menubar=no,center=yes,scrollbars=no,help=no,status=no,resizable=no,top=100,left=500");
    f.reqTime.value = reqTime;
    f.action = "wb_add.php";
    f.target = "wb_add";
    f.submit();
    f.target = "";
}

function detailInfoScheView(bogoName, startTime, endTime) {
    var htmltxt = '';
    htmltxt +=
        '<div id="DetailInfoViewTemp" style="z-index:999;border:1px solid #000;background-color:#fff;padding:10px 0;">';
    htmltxt += '	<table width="430" border="0" cellspacing="0" cellpadding="0">';
    htmltxt += '		<tr>';
    htmltxt += ' 			<td align="center" valign="top" style="background-repeat: repeat-x;">';
    htmltxt += '				<table width="400" border="0" cellspacing="0" cellpadding="0">';
    htmltxt += '					<tr class="table_header">';
    htmltxt += '						<td width="180">보고자</td>';
    htmltxt += '						<td width="110" style=\"border-left:2px solid #E9F4F9\">시작시간</td>';
    htmltxt += '						<td style=\"border-left:2px solid #E9F4F9;border-right:2px solid #E9F4F9;\">종료시간</td>';
    htmltxt += '					</tr>';
    htmltxt += '					<tr>';
    htmltxt += '						<td class="td_status">' + bogoName + '</td>';
    htmltxt += '						<td class="td_status" style=\"border-left:2px solid #E9F4F9;\">' + startTime + '</td>';
    htmltxt += '						<td class="td_status" style=\"border-left:2px solid #E9F4F9;border-right:2px solid #E9F4F9;\">' +
        endTime + '</td>';
    htmltxt += '					</tr>';
    htmltxt += '				</table>';
    htmltxt += '			</td>';
    htmltxt += '        </tr>';
    htmltxt += '        <tr>';
    htmltxt += '			<td colspan="3" bgcolor="4e7bbd"></td>';
    htmltxt += '		</tr>';
    htmltxt += '	</table>';
    htmltxt += '</div>';
    var top = event.clientY + document.body.scrollTop;
    var left = event.clientX + document.body.scrollLeft;

    top = top + 10;
    left = left - 190

    $('#tipdiv').css("left", left + 'px');
    $('#tipdiv').css("top", top + 'px');

    $('#tipdiv').html(htmltxt);
    $('#tipdiv').show();
}

function divScheduleClose() {
    $('#tipdiv').html('');
    $('#tipdiv').hide();
}
</script>
<!-- <meta http-equiv="X-UA-Compatible" content="IE=Edge"> -->
<!-- <meta http-equiv="X-UA-Compatible" content="IE=10"> -->

<body onload="fnInit()">
    <div id="tipdiv" style="display:none;position:absolute;"></div>
    <table width="100%" height="90px" style="margin-bottom:5px;border:1px solid #e2e2e2;background-color:#0a2554;">
        <tr>
            <td>
                <table width="1300px" height="87px" align="center" style="background:url(images/title_bg.gif) no-repeat;">
					<tr>
						<td width="302px" align="left" style="color: white; ">
							<!-- <a href="http://scal.daewooenc.com/smart2/"><img src="images/daewooenc_symbol.png" width="40"/>&nbsp;&nbsp;<img src="images/title_left.png"/></a> -->
							<div class="rotation_parent">
								<a href="http://scal.daewooenc.com/smart2/">
									<div class="rotation">
										<img src="images/daewooenc_symbol.png" width="40" />
									</div>
									<img src="images/title_left.png">
								</a>
							</div>
							<!-- (일반사용자)  -->
							<!-- <?= $sEmpno."->".$rep_empno."/".$isReport.$isMainReport ?> -->
						</td>
						<td align="right">
							<div class="date" style="color:#ffffff;font-size:25px;"><?=substr($sWrite_date,0,4);?>년
								<?=substr($sWrite_date,5,2);?>월 <?=substr($sWrite_date,8,2);?>일&nbsp;(<?=$sWriteDay;?>)
							</div>
						</td>
						<td width="150px" align="right">
						</td>
					</tr>
				</table>
            </td>
        </tr>
    </table>
    </td>
    </tr>
    <tr>
        <td>

        </td>
    </tr>
    </table>

    <form name="frm" method="post">
        <input type="hidden" name="dateInclease" id="dateInclease" value="<?=$dateInclease;?>" />
        <input type="hidden" name="write_date" id="write_date" value="<?=$sWrite_date;?>" />
        <input type="hidden" name="repid" value="">
        <input type="hidden" name="reqTime" value="">
        <input type="hidden" name="rep_empno" value="<?=$rep_empno?>">
        <input type="hidden" name="sEmpNo" value="<?=$sEmpno?>">
        <table width="100%">
            <tr>
                <td>
                    <table width="1300px" height="40" align="center">
                        <tr>
                            <td width="350" align="left"><a href="#" onclick="manaSche('');"><span
                                        class="bt_main">일정보기</span></a></td>
                            <!-- <td class="date_navi"> -->
                            <td width="600">
                                <!-- <img src="images/prev.gif" height="29px" border="0" onclick="fnGo('minus');" style="cursor:pointer; vertical-align: middle; "/>
						<span style="border:0px solid #000;font-size:20px; font-weight:800;vertical-align: middle; ">&nbsp; <?=$sWrite_date;?> (<?=$sWriteDay;?>)&nbsp;</span>
						<img src="images/next.gif" height="29px" onclick="fnGo('plus');" style="cursor:pointer;vertical-align: middle; " />&nbsp;&nbsp;
						<span style="border:1px solid #9c9c9c;padding:4px 14px;font-size:16px; font-weight:550;vertical-align: middle;background-color:white;border-radius:6px;"><a href="#" onclick="fnGoToday();" >오늘</a></span> -->
                                <span style="font-size:20px; font-weight:800;vertical-align: middle; ">&nbsp;
                                    <?=$sWrite_date;?> (<?=$sWriteDay;?>)&nbsp;</span>
                                <img src="images/prev.gif" height="29px" border="0" onclick="fnGo('minus');"
                                    style="cursor:pointer; vertical-align:middle;" />
                                <span
                                    style="border:1px solid #9c9c9c; padding:3px 8px; font-size:16px; font-weight:550; vertical-align:middle;"><a
                                        href="#" onclick="fnGoToday();">오늘</a></span>
                                <img src="images/next.gif" height="29px" onclick="fnGo('plus');"
                                    style="cursor:pointer;vertical-align:middle;" />&nbsp;&nbsp;
                            </td>

                            <style>
                            .dropbtn {
                                width: 350px;
                                background-color: white;
                                padding: 6px 0px 6px 0px;
                                font-size: 18px;
                                font-weight: 800;
                                text-align: right;
                                border: none;

                                /* margin-top: 0em;
						margin-right: 0em;
						margin-bottom: 0em;
						margin-left: 0em;
						font-style: normal;
						font-family: system-ui;
						font-variant-caps: normal;
						color: initial;
						letter-spacing: normal;
						word-spacing: normal;
						line-height: normal;
						text-transform: none;
						text-indent: 0px;
						text-shadow: none;
						display: inline-block;
						text-align: start; */
                            }

                            .dropdown {
                                width: 350px;
                                position: relative;
                                display: inline-block;
                            }

                            /* 상단 메뉴 마우스 오버 시 스타일 */
                            .dropdown:hover .dropdown-content {
                                display: block;
                            }

                            /*.dropdown:hover .dropbtn {background-color: #3e8e41;}  마우스 올렸을 때 배경 색상*/

                            /* 하단 드롭 메뉴 스타일 */
                            .dropdown-content {
                                width: 350px;
                                font-size: 18px;
                                font-weight: 800;
                                display: none;
                                position: absolute;
                                background-color: #f1f1f1;
                                /* min-width: 160px; */
                                border: 1px solid #EDEDED;
                                box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
                                z-index: 1;
                            }

                            /* 하단 드롭 메뉴 링크 스타일 */
                            .dropdown-content a {
                                color: black;
                                padding: 12px 6px;
                                text-decoration: none;
                                display: block;
                            }

                            /* 하단 드롭메뉴 마우스 오버 시 스타일 */
                            .dropdown-content a:hover {
                                color: #105899;
                                background-color: #EDEDED;
                            }
                            </style>

                            <td width="350" align="right">
                                <div class="dropdown">
                                    <!-- <button class="dropbtn"><a href="#">님 보고일정</a></button>	 -->
                                    <?
						//  $rep_empnos = array('CEO','CFO',"인사관리지원본부장님", "IT실장님");
						 /*
						 $rep_empnos = [];
						 
						 $rep_array = array('sabun' => 11111111, 'name' => "오창원");
						 array_push($rep_empnos, $rep_array);
						 $rep_array = array('sabun' => 11111112, 'name' => "오창원2");
						 array_push($rep_empnos, $rep_array);
						 $rep_array = array('sabun' => 11111113, 'name' => "오창원3");
						 array_push($rep_empnos, $rep_array);
						 
						 $rep_empnos2 = array();
						 array_push($rep_empnos2, '오창원3');
						 */
						?>
                                    <a href="#"><button class="dropbtn"><?=$aPstnName?>님 보고일정</button></a>
                                    <!-- <img src="images/change_boss.gif" height="25px"/> -->
                                    <div class="dropdown-content">
                                        <? for($i=0; $i < count($rep_empnos); $i++){ ?>
                                        <a href="#"
                                            onclick="fnChange(' <?= $rep_empnos[$i]['user_id']; ?> ');"><?= $rep_empnos[$i]['user_knm']." ".$rep_empnos[$i]['posit_cd']; ?>
                                            보고일정</a>
                                            
                                        <? } ?>
                                        <!--  wb_sche_list.php ajaxList.php 참고
						 foreach($rep_empnos as $key => $value){ 
						for($i=0; $i<count($rep_empnos2); $i++) { 
						<a href="#" onclick="fnChange('11111112');">$rep_empnos2[$i];</a>
						} -->

                                        <!-- <a href="#" onclick="fnChange('11111113');">CEO 보고일정</a>
							// <a href="#" onclick="fnChange('11111112');">CFO 보고일정</a>
							// <a href="#" onclick="fnChange('11111111');">인사관리지원본부장님 보고일정</a>
							// <a href="#">IT실장님 보고일정</a> -->
                                    </div>
                                </div>




                            </td>
                        </tr>
                    </table>
                    <table width="1300px" align="center">
                        <tr>
                            <td>
                                <div id="bogoCont">

                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
<table><tr><td>



<!--     
<a class="btn" data-popup-open="popup-1" href="#">Open Popup #1</a>
<div class="popup" data-popup="popup-1">
	<div class="popup-inner">
		<h2>Wow! This is Awesome! (Popup #1)</h2>
		<p>Donec in volutpat nisi. In quam lectus, aliquet rhoncus cursus a, congue et arcu. Vestibulum tincidunt neque id nisi pulvinar aliquam. Nulla luctus luctus ipsum at ultricies. Nullam nec velit dui. Nullam sem eros, pulvinar sed pellentesque ac, feugiat et turpis. Donec gravida ipsum cursus massa malesuada tincidunt. Nullam finibus nunc mauris, quis semper neque ultrices in. Ut ac risus eget eros imperdiet posuere nec eu lectus.</p>

        <p><a data-popup-close="popup-1" href="#">Close</a></p>
		
		<a class="popup-close" data-popup-close="popup-1" href="#">x</a>
	</div>
</div>
-->         


<script language="javascript">
    $(function() {
        //----- OPEN
        $('[data-popup-open]').on('click', function(e) {
            var targeted_popup_class = jQuery(this).attr('data-popup-open');
            $('[data-popup="' + targeted_popup_class + '"]').fadeIn(350);

            e.preventDefault();
        });

        //----- CLOSE
        $('[data-popup-close]').on('click', function(e) {
            var targeted_popup_class = jQuery(this).attr('data-popup-close');
            $('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);

            e.preventDefault();
        });
    });
</script>

<!--
<a href="#" id="popup-open-1" class="btn" data-popup-open="popup-1">Popup</a>

<div class="popup" data-popup="popup-1">
    <div class="popup-inner">
        <iframe src="wb_add_div.php" frameborder="0" width="740" height="550" marginwidth="0" marginheight="0"></iframe>
        <a href="#" id="popup-colse-1" class="popup-close" data-popup-close="popup-1">x</a>
    </div>
</div>

<a href="#" id="popup-open-2" class="btn" data-popup-open="popup-2">Popup2</a>
<div class="popup" data-popup="popup-2">
    <div class="popup-inner">
        <iframe src="wb_admin_manage.php" frameborder="0" width="740" height="550" marginwidth="0" marginheight="0"></iframe>
        <a href="#" id="popup-colse-2" class="popup-close" data-popup-close="popup-2">x</a>
    </div>
</div>
-->
</td></tr></table>

    </form>
</body>

</html>