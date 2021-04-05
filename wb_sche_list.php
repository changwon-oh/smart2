<?
/*#########################################
# 시스템명 : 스마트보고 시스템
# 작 성 일 : 2021.04.05
# 파 일 명 : wb_sche_list.php
# 기능설명 : 일정보기
#########################################*/
?>
<!DOCTYPE html
    PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
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

isset($_POST['dateInclease']) ? $dateInclease	= trim($_POST['dateInclease']) : "";
isset($_POST['write_date']) ? $sWrite_date	= trim($_POST['write_date']) : "0";
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
	$aPstnName 			    = $oci -> result(1);
}
$oci -> parseFree();

$qry_list = "         SELECT REP_EMPNO, substr(REP_DATE1,1,4)||'.'||substr(REP_DATE1,6,2)||'.'||substr(REP_DATE1,9,2) SCH_DATE, REP_TIME1, REP_TIME2, TITLE, OPEN_YN ";
$qry_list .= "           FROM KOSPOWB_REPORT  ";
$qry_list .= "          WHERE REP_EMPNO = '$rep_empno' AND TO_DATE(substr(REP_DATE1,1,4)||substr(REP_DATE1,6,2)||substr(REP_DATE1,9,2)||'000000','YYYYMMDDHH24MISS') BETWEEN TRUNC(TO_DATE(TO_CHAR(SYSDATE,'YYYYMMDD')||'000000','YYYYMMDDHH24MISS'),'IW') AND TRUNC(TO_DATE(TO_CHAR(SYSDATE+7,'YYYYMMDD')||'000000','YYYYMMDDHH24MISS'),'IW')+4 AND SC_YN = 'Y' ORDER BY substr(REP_DATE1,1,4)||'.'||substr(REP_DATE1,6,2)||'.'||substr(REP_DATE1,9,2), TO_NUMBER(substr(REP_DATE1,1,4)||substr(REP_DATE1,6,2)||substr(REP_DATE1,9,2)) ";
$oci -> parseExec($qry_list);
$resultArray = array();
while($col = $oci -> fetchInto()) {
    $arrayMiddle = array(
        "rep_empno" => $col['REP_EMPNO'], // 보고대상자        
		"sch_date" => $col['SCH_DATE'], // 일정일자
		"rep_time1" => $col['REP_TIME1'], // 일정시작
		"rep_time2" => $col['REP_TIME2'], // 일정종료
		"title" => $col['TITLE'], //일정제목
        "open_yn" => $col['OPEN_YN'] // 공개여부
  );
  
  array_push($resultArray, $arrayMiddle);
}
$oci -> parseFree();

$oci -> disconnect();
$roci -> disconnect();

$startDate = date("Y-m-d",strtotime(date("Y-m-d",time())." -".(date("w",time())-1)."days"));

?>
<script src="./JS/jquery-1.7.2.min.js"></script>
<title>스마트 보고관리(일정보기)</title>
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

</script>

<body>
    <div id="tipdiv" style="display:none;position:absolute;"></div>
    <table width="100%" height="90px" style="margin-bottom:5px;border:1px solid #e2e2e2;background-color:#0a2554;"">
    <tr>
        <td>
            <!--table width=" 1300px" height="87px" align="center"
        style="background:url(images/title_bg.gif) no-repeat;">
        <tr>
            <td width="242px" align="left">
                <img src="images/title_left.png" />
            </td>
            <td align="right">
                <div class="date" style="color:#ffffff;font-size:25px;"></div>
            </td>
            <td width="150px" align="right">

            </td>
        </tr>
        </table-->
        <div class="date" style="color:#ffffff;font-size:30px;"><?= $aPstnName; ?>님 일정</div>
        </td>
        </tr>
    </table>
    <form name="frm" method="post">
        <input type="hidden" name="dateInclease" id="dateInclease" value="" />
        <input type="hidden" name="write_date" id="write_date" value="" />
        <input type="hidden" name="repid" value="">
        <input type="hidden" name="reqTime" value="">
        <input type="hidden" name="rep_empno" value="<?= $rep_empno ?>">
        <input type="hidden" name="sEmpNo" value="<?= $sEmpno ?>">
        <table width="100%">
            <tr>
                <td>
                    <table width="900" height="40" align="center">
                        <tr>
                            <td width="16%">&nbsp;</td>
                            <td width="16%" style="text-align:right;">기간 : <?= str_replace('-', '.', $startDate); ?> ~
                                <?= date("Y.m.d", strtotime($startDate . " +11days")) ?>
                            </td>
                        </tr>
                    </table>

                    <table width="900" align="center">
                        <tr>
                            <td>
                                <div id="bogoCont">
                                    <table width="100%">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <table width="100%" align="center" id="tbl_tb">
                                                        <colgroup>
                                                            <col width="170">
                                                            <col width="170">
                                                            <col width="*">
                                                        </colgroup>
                                                        <thead>
                                                            <tr class="table_header">
                                                                <td height="40">일자</td>
                                                                <td>시간</td>
                                                                <td>주요일정</td>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?
    $aSmart = Array("(월)","(화)","(수)","(목)","(금)");
    
    for($i=0; $i < 12; $i++){
        if($i != 5 && $i != 6){
            $strTb = "";
            $strTime = "";
            $strTitle = "";
            $dup = 0;
            $dDay = date("Y.m.d",strtotime($startDate." +".$i."days"));
            echo '<tr style="border-bottom: 1px solid #E2E2E2;">';
            echo '<td style="height:45px;border-right: 1px solid #E2E2E2;">'.substr($dDay,2,strlen($dDay)).$aSmart[date("w",strtotime($startDate." +".$i."days"))-1].'</td>';
            echo '<td colspan="2">';
            $strTb = '<table width="100%">';
            for($j=0; $j < count($resultArray); $j++){
                if($resultArray[$j]['sch_date'] == $dDay){
                    $strTb .= '<tr>';
                    $strTb .= '    <td style="width:170px; height:45px;border-right: 1px solid #E2E2E2;">'.substr($resultArray[$j]['rep_time1'],0,5).'~'.substr($resultArray[$j]['rep_time2'],0,5).'</td>';

                    //일정 비공개
                    if($resultArray[$j]['open_yn']=='N' && 
                       $sEmpno != $resultArray[$j]['rep_empno'] && 
                       $isReport != 1) { 
                        $title = "일정";
                    } else {
                        $title = $resultArray[$j]['title'];
                    }

                    if($resultArray[$j]['open_yn']=='N') $title .= '<img src="images/sec.gif" border=\"0\" />'; // 비공개 자물쇠

                    $strTb .= '    <td align="left">&nbsp; □&nbsp;'.$title.'</td>';
                    $strTb .= '</tr>';
                    $dup++;
                }
            }
            if($dup == 0) $strTb .= '<tr><td style="width:170px; height:45px;border-right: 1px solid #E2E2E2;"></td><td></td></tr>';
            $strTb .= '</table>';
            echo $strTb;
            //echo '<td>'.$strTime.'</td>';
            //echo '<td>'.$strTitle.'</td>';
            echo '</td>';
            echo '</tr>';
        }
    }
?>
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

    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td align="center">
                <span class="bt_summit" onClick="self.close();">닫기</span>
            </td>
        </tr>
    </table>


</body>

</html>