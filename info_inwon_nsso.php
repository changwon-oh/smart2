<?
/*#########################################
# 시스템명 : 스마트보고 시스템
# 작 성 일 : 2021.04.05
# 파 일 명 : wb_admin_manage.php
# 기능설명 : 스마트보고 관리자 목록
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
?>

<link href="CSS/imp.css" rel="stylesheet" type="text/css" />
<script src="./JS/common.js"></script>
<script src="./JS/jquery-1.7.2.min.js"></script>
<title>스마트 보고관리(직원검색용)</title>
<style type="text/css">
    a:hover {
        cursor: pointer;
        background-color: yellow;
    }

    html {
        overflow-y: scroll;
    }
</style>
<script language="javascript">
    //Getting value from "ajax.php".
    function fill(Value) {
        //Assigning value to "search" div in "search.php" file.
        $('#search').val(Value);
        //Hiding "display" div in "search.php" file.
        $('#display').hide();
    }

    $(document).ready(function() {
        $("#search").keyup(function() {
            var name = $('#search').val();
            if (name == "") {
                $("#display").html("");
            } else {
                $.ajax({
                    type: "POST",
                    url: "ajaxSearchUser.php",
                    data: {
                        search: name
                    },
                    dataType: "json",
                    success: function(json) {
                        // [{"user_id":"9724920","user_knm":"ucd5c","PSTN_NAME":"ucef4","TRNS_DPRT_KEY":"AR"},
                        // {"user_id":"9724920","user_knm":"ucd5c","PSTN_NAME":"ucef4","TRNS_DPRT_KEY":"AR"},]
                        var retHtml = "";
                        retHtml += "<table align=\"center\" width=\"100%\">";
                        retHtml += "<colgroup>";
                        retHtml += "    <col width=\"120\"/>";
                        retHtml += "    <col width=\"120\"/>";
                        retHtml += "    <col width=\"*\"/>";
                        // retHtml += "    <col width=\"130\"/>";
                        // retHtml += "    <col width=\"80\"/>";
                        retHtml += "</colgroup>";
                        retHtml += "<tbody>";
                        for (var i = 0; i < json.length; i++) {
                            // retHtml += "  <tr class=\"table_header\">";
                            retHtml += "  <tr class=\"search_result\"  style=\"border-bottom: 1px solid #E2E2E2;cursor:pointer;\" onclick=\"inwonDetail3('" + json[i]['user_id'] + "','" + json[i]['user_knm'] + "','" + json[i]['pstn_name'] + "');\">";
                            retHtml += "      <td style=\"border-right: 1px solid #E2E2E2;\">" + json[i]['user_id'] + "</td>";
                            retHtml += "      <td style=\"border-right: 1px solid #E2E2E2;\">" + json[i]['user_knm'] + "</td>";
                            retHtml += "      <td >" + json[i]['pstn_name'] + "</td>";
                            // retHtml += "      <td style=\"border-left:2px solid #E9F4F9\">"+json[i]['trns_dprt_key']+"</td>";
                            // retHtml += "      <td style=\"border-left:2px solid #E9F4F9\">";


                            // function inwonDetail2(emp,name,title)
                            // retHtml += "<a href=\"#\" onclick=\"inwonDetail2('"+json[i]['user_id']+"','"+json[i]['user_knm']+"','"+json[i]['pstn_name']+"');\"><img src=\"images/btn_confirm.gif\" alt=\"확인\"/></a>";
                            retHtml += "</td>";



                            retHtml += "  </tr>";
                        }
                        retHtml += "  </tbody>";
                        retHtml += "</table>";
                        $("#display").html(retHtml).show();
                    }
                });
            }
        });
    });
</script>

<body>

    <!-- <div id="tipdiv" style="display:none;position:absolute;"></div> -->
    <table width="600px" height="60px" align="center" style="margin-bottom:5px;border:1px solid #e2e2e2;background-color:#0a2554;">
        <colgroup>
            <col width="200">
            <col width="*">

        </colgroup>
        <tr>
            <td>
                <div style="color:#ffffff;font-size:25px;">보고자 검색</div>
            </td>
            <td bgcolor='#0a2554'>
                <input type="text" name="title" id="search" placeholder="" class="input_search" size="10" maxlength="10" autofocus />
            </td>
        </tr>
    </table>
    <!-- Search box. -->
    <!-- <table width="100%">
    <tbody><tr>
        <td> -->
    <table width="600px" align="center" id="tbl_tb">
        <colgroup>
            <col width="125">
            <col width="125">
            <col width="*">
            <!-- <col width="130">
                <col width="80"> -->
        </colgroup>
        <thead>
            <tr class="table_header">
                <td height="40">사번</td>
                <td>이름</td>
                <td>직책</td>
                <!-- <td>팀코드</td> -->
                <!-- <td>선택</td> -->
            </tr>
        </thead>
        <tbody id="display">
            <!-- <tr style="border-bottom: 1px solid #E2E2E2;"> -->
            <!-- <td colspan="5" height="40" class="search_result">검색 내용이 없습니다.</td> -->
            <!-- </tr> -->
        </tbody>
    </table>
    <!-- </td>
    </tr>
    </tbody>
    </table> -->


</body>

</html>