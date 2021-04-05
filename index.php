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

<?   
// header("Pragma: no-cache");   
// header("Cache-Control: no-cache,must-revalidate");   
?>

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
// include "./include/sso_auth.inc.php";

// SSO 인증여부 확인 / 미인증시 바로넷 로그인 화면으로 이동
$sEmpno = "";
if (isset($_SESSION["username"])) {
    $sEmpno = $_SESSION["username"];
} elseif (isset($_POST["username"])) {
    $sEmpno = $_POST["username"];
    $_SESSION["username"] = $sEmpno;
} else {

    if(isset($_SESSION["tryCount"])) {
        ?>
<script language="JavaScript">
alert("바로넷 로그인 후 이용 바랍니다.");
// window.location.href='http://iworks.daewooenc.com/etls/et/common/sso/smart';
location.replace("http://baronet.dwconst.co.kr/");
// window.open('http://baronet.dwconst.co.kr/');
// self.close();
</script>
<?
        $_SESSION = []; 
// echo '<center><br><br><br><br>';
// echo("★ 바로넷 로그인 후 이용 바랍니다. ★");
// echo '<p><a href="http://baronet.dwconst.co.kr/" target="_blank">'.'▶ 바로넷 로그인하러 가기'.'</a></p>';
// echo '<p><a href="http://iworks.daewooenc.com/etls/et/common/sso/smart">'.'▶ 이미 로그인이 되었어요'.'</a></p>';
    } else {
        $_SESSION["tryCount"] = 1;
        header('Location: http://iworks.daewooenc.com/etls/et/common/sso/smart');
    }
exit;
}

/*
$sEmpno = '1202539';
$_SESSION["username"] = $sEmpno;
*/

// 접속자 이름 직급가져오기
$sEmpt = getEmpno($sEmpno);

// 인증완료 후 처리
//rep_empno가 없을 경우 DB에서 직속상관의 사번을 기본값으로 설정 
$rep_empnos = getRepEmpnos($sEmpno); // 직속상관리스트

if(count($rep_empnos) != 0){
	$bossCount = count($rep_empnos)-1;
	$rep_empno = $rep_empnos[$bossCount]['user_id'];
} else {
	$rep_empno = "";
}
// echo $rep_empno;

$isAwmsAdmin = 0; // 시스템 관리자
$isMainReport = 0; // 보고대상장(집행임원)

// 스마트 보고관리자(시스템 관리자)
$aSmart = Array("1111111"); 
// 스마트 보고관리자(부속실 관리자)
$rep_managers = getRepManagers($sEmpno); //보고관리대상자 리스트(부속실 관리자)

// 스마트 메인(wb_main.php) 접근권한
$isMainReport = getSmartIsManage($sEmpno); // 본부장이상급
$_SESSION["isMainReport"] = $isMainReport;

$main_managers = getMainManagers($sEmpno); //보고대상자리스트(집행임원+관리자)

// header('Location: wb_list.php');
// unset($_SESSION['username']);
// session_destroy();

?>

<!-- <link href="CSS/imp.css" rel="stylesheet" type="text/css" /> -->
<style>
a:link {
    color: #3c3c3c;
    text-decoration: none;
}

a:visited {
    color: #3c3c3c;
    text-decoration: none;
}

a:active {
    color: #3c3c3c;
    text-decoration: none;
}

body {
    font-family: 'NanumGothic', '나눔고딕', '맑은 고딕', 'MalgunGothic';
    color: #3C3C3C;
    font-size: 18px;
    font-weight: 800;
    line-height: 23px;
    /*letter-spacing:-0.3px;*/
    text-align: center;
    margin: 0 auto;
    background-color: #284971;
}

.content_wrapper {
    background-color: #fff;
    border-radius: 16px;
    border: 1px solid #eceff1;
    box-shadow: 0 1px 2px 0 rgb(60 64 67 / 30%), 0 1px 3px 1px rgb(60 64 67 / 15%);
    margin: 0 auto 20px;
    /* max-width: 1280px; */
    overflow: hidden;
    width: 1300px;
    height: 300px;
}

.cols_header {
    vertical-align: top;
    border-radius: 16px;
    background-color: #f8f9fa;
    box-shadow: 0 1px 2px 0 rgb(60 64 67 / 30%), 0 1px 3px 1px rgb(60 64 67 / 15%);
    padding: 40px 15px 40px 15px;
    font-size: 34px;
}

.cols_header:hover {
    background-color: #e8f0fe;
}

.cols_header_disable {
    vertical-align: top;
    border-radius: 16px;
    background-color: #f8f9fa;
    box-shadow: 0 1px 2px 0 rgb(60 64 67 / 30%), 0 1px 3px 1px rgb(60 64 67 / 15%);
    padding: 40px 15px 40px 15px;
    font-size: 34px;
    /* opacity: 0.7; */
    color: rgb(60 64 67 / 30%)
}

/* .cols_header_disable:hover {
    /* background-color: #e8f0fe; */
/* } */

.cols_header_name {
    line-height: 30px;
    font-size: 22px;
}

.cols_header_name a:hover {
    cursor: pointer;
    color: #105899;
}
</style>

<script language="javascript">
function fn_list(repEmpno) {
    var f = document.frm;
    // f.write_date.value = '';
    f.dateInclease.value = '';
    f.rep_empno.value = repEmpno;
    f.action = "wb_list.php";
    // f.target = "wb_list";
    f.submit();
    // f.target = ""; // 새창
}

function fn_admin_list(repEmpno) {
    var f = document.frm;
    // f.write_date.value = '';
    f.dateInclease.value = '';
    f.rep_empno.value = repEmpno;
    f.action = "wb_admin_list.php";
    // f.target = "wb_list";
    f.submit();
    // f.target = ""; // 새창
}

function fn_main(repEmpno) {
    var f = document.frm;
    // f.write_date.value = '';
    f.dateInclease.value = '';
    f.rep_empno.value = repEmpno;
    f.action = "wb_main.php";
    // f.target = "wb_list";
    f.submit();
    // f.target = ""; // 새창
}
</script>
<title>스마트 보고관리</title>
<link href="CSS/imp.css" rel="stylesheet" type="text/css" />

<body>
    <center>
        <div style="width:1300px; color:white; font-size: 44px; text-align:left">
            <!-- <br><br>&nbsp;&nbsp;&nbsp;스마트보고관리<br><br><br> -->
            <br>
            <div class="rotation_parent">
            <a href="http://scal.daewooenc.com/smart2/">
            <div class="rotation" style="margin:0px;">
            <img src="images/daewooenc_symbol.png" width="40" />
            </div>
            <img
                    src="images/title_left.png" /></a>
                    <br><br>
            </div>
        </div>
    </center>
    <form name="frm" method="post">
        <input type="hidden" name="sEmpNo" value="<?= $sEmpno ?>">
        <input type="hidden" name="rep_empno" value="<?= $rep_empno ?>">
        <input type="hidden" name="dateInclease" id="dateInclease" value="" />
        <table class="content_wrapper">
            <colgroup align="center">
                <col width="62.5px">
                <col width="365px">
                <col width="40px">
                <col width="365px">
                <col width="40px">
                <col width="365px">
                <col width="62.5px">
            </colgroup>
            <thead>
                <tr align="center">
                    <td colspan="7" height="100px" align="right" style="font-size: 30px; padding:0px 75px 0px 0px">
                        <?= $sEmpt[0]['user_knm'] . ' ' . $sEmpt[0]['posit_cd'] //.$sEmpt[0]['user_id'] 
                        ?>
                        <!-- <img src="http://was01.dwconst.co.kr:8010/download?path=&name=<?=$sEmpt[0]['user_id'];?>.jpg&type=employee" style="border-radius:50%; height:100px;"> -->
                    </td>
                </tr>
                <tr align="center">
                    <td></td>

                    <? if(count($rep_empnos)==0) {
                        $td_cols_header = 'cols_header_disable';
                    } else {
                        $td_cols_header = 'cols_header';
                    }
                    ?>
                    <td class="<?= $td_cols_header ?>">
                        보고하기<br><br><br>
                        <div class="cols_header_name">
                            <table width='100%'>

                                <? for($i=0; $i < count($rep_empnos); $i++){ ?>
                                <tr height="60px">
                                    <td width="55px">
                                        <img src="http://was01.dwconst.co.kr:8010/download?path=&name=<?=$rep_empnos[$i]['user_id'];?>.jpg&type=employee"
                                            style="border-radius:50%; width:55px;">
                                    </td>
                                    <td width="20"></td>
                                    <td>
                                        <!-- 텍스트 형광펜 효과 -->
                                        <div class="highcontent"><span class="highlight"><span>
                                                    <a href="#" onclick="fn_list('<?=$rep_empnos[$i]['user_id'];?>');">
                                                        <? if($sEmpno!='122539'){ ?>
                                                            <?= $rep_empnos[$i]['user_knm'] . " " . $rep_empnos[$i]['posit_cd']; ?>
                                                        <? } else { ?>
                                                            <?= $rep_empnos[$i]['posit_cd'];  ?>
                                                        <? } ?>
                                                    </a>
                                                </span></span></div> <!-- 텍스트 형광펜 효과 -->
                                    </td>
                                </tr>
                                <tr>
                                    <td height="10"></td>
                                </tr>
                                <? } ?>
                            </table>
                        </div>
                    </td>
                    <td></td>
                    <? if(count($rep_managers)==0) {
                        $td_cols_header = 'cols_header_disable';
                    } else {
                        $td_cols_header = 'cols_header';
                    }
                    ?>
                    <td class="<?= $td_cols_header ?>">
                        보고관리(비서)<br><br><br>
                        <div class="cols_header_name">
                            <table width='100%'>
                                <? for($i=0; $i < count($rep_managers); $i++){ ?>
                                <tr height="60px">
                                    <td width="55px">
                                        <img src="http://was01.dwconst.co.kr:8010/download?path=&name=<?=$rep_managers[$i]['user_id'];?>.jpg&type=employee"
                                            style="border-radius:50%; width:55px;">
                                    </td>
                                    <td width="20"></td>
                                    <td align="left">
                                        <!-- 텍스트 형광펜 효과 -->
                                        <div class="highcontent"><span class="highlight"><span>
                                        <a href="#"
                                            onclick="fn_admin_list('<?= $rep_managers[$i]['user_id']; ?>');">
                                            <? if($sEmpno!='122539'){ ?>
                                                <?= $rep_managers[$i]['user_knm'] . " " . $rep_managers[$i]['posit_cd']; ?>
                                            <? } else { ?>
                                                <?= $rep_managers[$i]['posit_cd'];  ?>
                                            <? } ?>
                                        </a>
                                        </span></span></div> <!-- 텍스트 형광펜 효과 -->
                                    </td>
                                </tr>
                                <tr>
                                    <td height="10"></td>
                                </tr>
                                <? } ?>                                
                            </table>

                            <? 
                            if(count($rep_managers)!=0) { ?>
                                <!-- <br>
                                <a style="color:#1967d2" href="http://scal.daewooenc.com/smart2/wb_admin_manage.php">비서 지정하기</a> -->
                                <br>
                            <? } ?>


                            <a style="color:#284971;" href="http://scal.daewooenc.com/smart2/wb_admin_manage.php">비서 지정하기</a>
                        </div>
                    </td>
                    <td></td>
                    <? if(count($main_managers)==0) {
                        $td_cols_header = 'cols_header_disable';
                    } else {
                        $td_cols_header = 'cols_header';
                    }
                    ?>
                    <td class="<?= $td_cols_header ?>">
                        보고받기<br><br><br>
                        <div class="cols_header_name">
                        <table width='100%'>
                            <? for($i=0; $i < count($main_managers); $i++){ ?>
                                <tr height="60px">
                                    <td width="55px">
                                        <img src="http://was01.dwconst.co.kr:8010/download?path=&name=<?=$main_managers[$i]['user_id'];?>.jpg&type=employee"
                                            style="border-radius:50%; width:55px;">
                                    </td>
                                    <td width="20"></td>
                                    <td>

                            <!-- 텍스트 형광펜 효과 -->
                            <div class="highcontent"><span class="highlight"><span>
                                        <a href="#" onclick="fn_main('<?= $main_managers[$i]['user_id']; ?>');">
                                        <? if($sEmpno!='122539'){ ?>
                                            <?= $main_managers[$i]['user_knm'] . " " . $main_managers[$i]['posit_cd']; ?>
                                        <? } else { ?>
                                            <?= $main_managers[$i]['posit_cd'];  ?>
                                        <? } ?>
                                    </span></span></div> <!-- 텍스트 형광펜 효과 -->
                            </a>

                            </td>
                                </tr>
                                <tr>
                                    <td height="10"></td>
                                </tr>
                                <? } ?>
                            </table>
                        </div>
                    <td>
                    <td></td>
                </tr>
                <tr><td colspan="7" height="80px"></td></tr>
                <tr align="center">
                    <td colspan="7" height="1px">
                        <div style="width: 90%; height: 1px; background-color:#dadce0"></div>
                    </td>
                </tr>
                <tr><td colspan="7" height="40px"></td></tr>
                <tr align="center">
                    <td colspan="7">
                        <table width="600px">
                        <colgroup align="center">
                            <col width="50%">
                            <col width="50%">
                        </colgroup>
                        <thead>
                            <tr>
                                <td align="center">
                                    <!-- <img src="images/feedback.png" style=" width:72px; border-radius:50%; box-shadow:0 1px 2px 0 rgba(60,64,67,.3),0 2px 6px 2px rgba(60,64,67,.15)" alt="IT운영팀 오창원과장 내선 4389"><p>
                                    <a style="color:dimgrey" href="" onMouseOver="this.innerHTML='오창원과장(4389)'" onMouseOut="this.innerHTML='피드백 보내기'">피드백 보내기 -->

                                    <!-- <img src="images/feedback.png" style=" width:72px; border-radius:50%; box-shadow:0 1px 2px 0 rgba(60,64,67,.3),0 2px 6px 2px rgba(60,64,67,.15)" alt="IT운영팀 오창원과장 내선 4389"><p> -->
                                    
                                    <!-- <img src="images/feedback.png" style=" width:72px; border-radius:50%; box-shadow:0 1px 2px 0 rgba(60,64,67,.3),0 2px 6px 2px rgba(60,64,67,.15)" 
                                    onMouseOver="this.src='images/feedback.png';" onMouseOut="this.src='images/feedback.png';" alt="오창원과장(4389)"/><p> -->
                                    
                                    <img src="images/feedback.png" style="width:72px; border-radius:50%; box-shadow:0 1px 2px 0 rgba(60,64,67,.3),0 2px 6px 2px rgba(60,64,67,.15)" 
                                    onMouseOver="document.getElementById('feedback').innerHTML='<p>오창원과장(4389)'" onMouseOut="document.getElementById('feedback').innerHTML='<p>피드백 보내기'"/>
                                    
                                    
                                     <style>
                                    .test_button {
                                        width:72px; border-radius:50%; box-shadow:0 1px 2px 0 rgba(60,64,67,.3),0 2px 6px 2px rgba(60,64,67,.15)
                                    }
                                    .test_button:hover {
                                        box-shadow: 0 0 0 30px #000 inset; 
                                        outline: 1px solid white;
                                        outline-offset: -4px;
                                    }
                                    </style>
                                     <!-- <img src="images/feedback.png" class="test_button"/> -->
                                    
                                    <!-- <a style="color:dimgrey" href="" onMouseOver="this.innerHTML='오창원과장(4389)'" onMouseOut="this.innerHTML='피드백 보내기'">피드백 보내기</a> -->
                                    <span style="color:dimgrey" id="feedback" href="" onMouseOver="document.getElementById('feedback').innerHTML='<p>오창원과장(4389)'" onMouseOut="this.innerHTML='<p>피드백 보내기'"><p>피드백 보내기</span>

                                </td>
                                <td align="center">
                                <a href="https://www.notion.so/93c633354c9049dd810acfa8384da357" target="_blank">
                                <!-- microsoft-edge:https://notion.so/comento/blahblah -->
                                    <img src="images/manual.png" style=" width:72px; border-radius:50%; box-shadow:0 1px 2px 0 rgba(60,64,67,.3),0 2px 6px 2px rgba(60,64,67,.15)"><p>
                                    <span style="color:dimgrey">도움말 보기</span>
                                </a>
                                </td>
                            </tr>
                        </thead>
                        </table>
                    </td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" colspan="7" height="20"></td>
                </tr>
            </tbody>
        </table>
    </form>

</body>

</html>