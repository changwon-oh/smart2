<?
// 조직도 출력
include "lib/group_lib.php"; // 조직도 출력
include "lib/commonLibClass.inc";
include "lib/commonDbClass.inc";

$ora    = new commonDb();
$conn	= $ora->dbConnect();

// 조회대상 조직코드(상위~부서)
$deptno = (isset($_GET["deptcode"]) && $_GET["deptcode"]!= "") ? doFilterSpecialChar($_GET["deptcode"]) : "0010";
$pid = (isset($_GET["pid"]) && $_GET["pid"]!= "") ? doFilterSpecialChar($_GET["pid"]) : "";
$pseq = (isset($_GET["pseq"]) && $_GET["pseq"]!= "") ? doFilterSpecialChar($_GET["pseq"]) : "";

// 조회대상 부서코드

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
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
    function inwonMngDetail(emp, name, title) {
        opener.document.getElementById("<?= $pid; ?>_name<?= $pseq; ?>").value = title + " " + name;
        opener.document.getElementById("<?= $pid; ?>_no<?= $pseq; ?>").value = emp;
        // if (opener.document.getElementById("<?= $pid; ?>_dept<?= $pseq; ?>") != null)
        //     opener.document.getElementById("<?= $pid; ?>_dept<?= $pseq; ?>").value = deptNo;
        self.close();
    }

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
                            retHtml +=
                                "  <tr class=\"search_result\"  style=\"border-bottom: 1px solid #E2E2E2;cursor:pointer;\" onclick=\"inwonMngDetail('" +
                                json[i]['user_id'] + "','" + json[i]['user_knm'] + "','" + json[i]['pstn_name'] + "');\">";
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