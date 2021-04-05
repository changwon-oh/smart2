<?
/*#########################################
# 시스템명 : 스마트보고 시스템
# 작 성 일 : 2021.04.05
# 파 일 명 : wb_search.php
# 기능설명 : 보고 검색
#########################################*/
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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

// 개인별 등록/신고내역 목록
$sTable_name01 = "KOSPOWB_REPORT";

isset($_POST['rep_empno']) ? $rep_empno	= trim($_POST['rep_empno']) : "";

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

$sDate = date("Y-m-d",strtotime(date("Y-m-d",time())." -7days"));
$eDate = date("Y-m-d",time());

?>
<link href="CSS/imp.css" rel="stylesheet" type="text/css" />
<link href="CSS/jquery-ui.css" rel="stylesheet" type="text/css" />
<script src="./JS/common.js"></script>
<script src="./JS/jquery-1.7.2.min.js"></script>
<script src="./JS/jquery-ui.js"></script>
<title>스마트 보고검색</title>
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

.trFont16 {
    font-size: 16px;
}
</style>
<script language="javascript">
$(function() {
    $('#sdate').datepicker({
        showOn: "both",
        buttonImage: "./images/ico_day.gif",
        buttonImageOnly: true,
        onClose: function(selectedDate) {
            $('#edate').datepicker("option", "minDate", selectedDate);
        }
    });

    $('#edate').datepicker({
        showOn: "both",
        buttonImage: "./images/ico_day.gif",
        buttonImageOnly: true,
        onClose: function(selectedDate) {
            $('#sdate').datepicker("option", "maxDate", selectedDate);
        }
    });

    $('img').mouseover(function() {
        if ($(this).attr("src") == "./images/ico_day.gif")
            $(this).css("cursor", "pointer");
    });
});

window.onload = function() {
    $.datepicker.regional['ko'] = {
        inline: true,
        dateFormat: "yy-mm-dd",
        changeMonth: true,
        changeYear: true,
        showButtonPanel: false, //아래 버튼
        monthNames: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
        monthNamesShort: ['1월', '2월', '3월', '4월', '5월', '6월', '7월', '8월', '9월', '10월', '11월', '12월'],
        dayNames: ['일', '월', '화', '수', '목', '금', '토'],
        dayNamesShort: ['일', '월', '화', '수', '목', '금', '토'],
        dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
        firstDay: 0,
        isRTL: false,
        showMonthAfterYear: true,
        showAnim: 'slideDown'
    };
    $.datepicker.setDefaults($.datepicker.regional['ko']);
}

function fnSearchData() {
    var f = document.frm;
    $.ajax({
        async: true,
        type: 'POST',
        url: 'ajaxSearch.php',
        //data: paramSub,
        data: {
            'sdate': f.sdate.value,
            'edate': f.edate.value,
            'searchGbn': f.searchGbn.value,
            'searchTxt': f.searchTxt.value,
            'repEmpno': '<?=$rep_empno;?>'
        },
        dataType: "json",
        success: function(json) {
            // 		  alert(json);  		        			
            var retHtml = "";
            if (json != null) {
                var tempHtml = "";

                if (json.length > 0) {
                    for (var j = 0; j < json.length; j++) {
                        retHtml += "<tr class='trFont16'>";
                        var rep_time = json[j]['rep_time1'];
                        var p = json[j]['rep_time1'].indexOf(":");
                        var colorTxt = "";
                        var long = 0;
                        var attachTxt = "";
                        var stateTxt = "";
                        var tempTime = json[j]['rep_time1'].substring(0, 2) + "시" + json[j]['rep_time1']
                            .substring(3, 5) + "분 ~ " + json[j]['rep_time2'].substring(0, 2) + "시" + json[j]
                            ['rep_time2'].substring(3, 5) + "분";
                        if (json[j]['rep_time1'].substring(0, 2) != json[j]['rep_time2'].substring(0, 2)) {
                            //tempTime = json[j]['rep_time1'].substring(0,2)+"시"+json[j]['rep_time1'].substring(3,5)+"분~"+json[j]['rep_time2'].substring(0,2)+"시"+json[j]['rep_time2'].substring(3,5)+"분";
                            long = 1;
                        }

                        if (json[j]['fileid'] != null) {
                            var tt = json[j]['fileid'].split("#");
                            for (var k = 0; k < tt.length; k++) {
                                var kk = tt[k].split("@");
                                attachTxt += "<a href='./wb_file.php?file=" + kk[0] + "&wbid=" + json[j][
                                        'repid'
                                    ] + "&seq=" + kk[1] +
                                    "'><img src='images/icon_file_new.gif' border='0' /></a>";
                            }

                        }

                        if (json[j]['dq_yn'] == 'Y') {
                            stateTxt = "서면대체";
                        } else {
                            if (json[j]['gubun'] == '0' && json[j]['stats'] == 'off') {
                                stateTxt = "보고완료";
                            }
                        }

                        var titles = json[j]['title'];
                        titles = "<a href='#' onclick=\"fnView('" + json[j]['repid'] + "');\">" + titles +
                            "</a>";
                        retHtml += "    <td height='40'>" + json[j]['rep_date1'].replace(/－/gi, '-') +
                            "</td>";
                        //retHtml += "    <td>"+tempTime+"</td>";
                        retHtml += "    <td style='text-align:left;'>" + titles + "" + attachTxt + "</td>";
                        retHtml += "    <td style='text-align:left;'>&nbsp;" + json[j]['bogo_grade_name'] +
                            " " + json[j]['bogo_name'] + "</td>";
                        retHtml += "    <td>" + (json[j]['stats_date'] != null ? json[j]['stats_date']
                            .substring(0, 5) + "~" : "") + (json[j]['end_date'] != null ? json[j][
                            'end_date'
                        ].substring(0, 5) : "") + "</td>";
                        retHtml += "    <td>" + stateTxt + "</td>";
                        retHtml += "</tr>";
                        retHtml +=
                            "<tr><td colspan='5' style='border-bottom: 2px solid #E2E2E2;'></td><td></td></tr>";
                    }
                } else {
                    retHtml += "<tr><td colspan='5' height='40'>검색 내용이 없습니다.</td></tr>";
                }
            }
            retHtml += "  </tbody>";
            retHtml += "</table>";
            $("#bogoCont").html(retHtml);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            //alert('검색 대상자가 존재하지 않습니다.');
            //alert(thrownError+':message=='+xhr.responseText);
        }
    });
}
</script>

<body>
    <div id="tipdiv" style="display:none;position:absolute;"></div>
    <table width="100%" height="90px" style="margin-bottom:5px;border:1px solid #e2e2e2;background-color:#0a2554;">
        <tr>
            <td>
                <!--table width="1300px" height="87px" align="center" style="background:url(images/title_bg.gif) no-repeat;">
                <tr>
                    <td width="242px" align="left">
                        <img src="images/title_left.png"/>
                    </td>
                    <td align="right"><div class="date" style="color:#ffffff;font-size:25px;"></div></td>
                    <td width="150px" align="right">
                      
                    </td>
                </tr>
            </table-->
                <div class="date" style="color:#ffffff;font-size:30px;"><?=$aPstnName;?>님 보고이력 조회</div>
            </td>
        </tr>
        <tr>
            <td>

            </td>
        </tr>
    </table>
    <form name="frm" method="post">
        <input type="hidden" name="repid" id="repid" value="" />
        <table width="100%">
            <tr>
                <td>
                    <table width="1000" align="center" height="50">
                        <tr>
                            <td width="35%" align="left">
								기간 :&nbsp;
								<!-- 기간 : &nbsp;<input type="text" name="sdate" id="sdate" class="input_date" maxLength="10" value="<?=$sDate;?>" style="height:18px; width:75px; border:1px #e5e5e5 solid;" /> ~ 
								<input type="text" name="edate" id="edate" maxLength="10" value="<?=$eDate;?>" style="height:18px; width:75px; border:1px #e5e5e5 solid;" /> </td> -->
								<input type="text" name="sdate" id="sdate" class="input_title" style="width: 100px;" maxLength="10" value="<?=$sDate;?>"/> ~ 
								<input type="text" name="edate" id="edate" class="input_title" style="width: 100px;" maxLength="10" value="<?=$eDate;?>"/> </td>
                            <td align="right">
                                <select name="searchGbn" id="searchGbn" class="input_title" style="width: 100px";>
                                    <option value="">전체</option>
                                    <option value="1">제목</option>
                                    <option value="2">보고자</option>
                                    <option value="3">첨부파일명</option>
                                </select>
                                <input type="text" name="searchTxt" value="" size="40" class="input_title" style="width: 450px;"
                                    onkeypress="if(event.keyCode==13){fnSearchData();}" />&nbsp;&nbsp;
                                <div
                                    style="float:right;padding:3px 11px;border:1px solid #000;background-color:lavender;">
                                    <a href="#" onclick="fnSearchData();">검색</a></div>
                            </td>
                        </tr>
                    </table>

                    <table width="1000" align="center">
                        <tr>
                            <td>
                                <div id="bogoCont2">
                                    <table width="100%">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <table width="100%" align="center" id="tbl_tb">
                                                        <colgroup>
                                                            <col width="125">
                                                            <!--col width="170"-->
                                                            <col width="*">
                                                            <col width="140">
                                                            <col width="130">
                                                            <col width="80">
                                                        </colgroup>
                                                        <thead>
                                                            <tr class="table_header">
                                                                <td height="40">일자</td>
                                                                <!--td>시간</td-->
                                                                <td>보고내용</td>
                                                                <td>보고자</td>
                                                                <td>보고시간</td>
                                                                <td>상태</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="bogoCont">
                                                            <tr>
                                                                <td colspan="5" height="40">검색 내용이 없습니다.</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </form>
</body>

</html>