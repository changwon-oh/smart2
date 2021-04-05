<?

//통합인증 미인증시 접근불가 
if(isset($_SESSION["username"])) {
	$sEmpno = $_SESSION["username"];
} else {
	?>
		<script language="JavaScript">
		// alert ("음....바로넷 로그인 화면으로 이동합니다.");
		window.location.href='http://scal.daewooenc.com/smart2/';
		// self.close();
		</script>
	<?php
	exit;
	
}

// 보고대상자
$rep_empno = '';
if(isset($_SESSION["rep_empno"])) {
	$rep_empno = $_SESSION["rep_empno"];
} else {
	$rep_empno = '';
}

//rep_empno가 없을 경우 DB에서 직속상관의 사번을 기본값으로 설정 
$rep_empnos = getRepEmpnos($sEmpno); // 직속상관리스트

// $rep_empno = "";
// isset($_POST['rep_empno']) ? $rep_empno = trim($_POST['rep_empno']) : "";
// echo '<pre>' , var_dump("rep_empno=".$rep_empno) , '</pre>';

/*
if(count($rep_empnos) != 0){
	$bossCount = count($rep_empnos)-1;
	$rep_empno = $rep_empnos[$bossCount]['user_id'];
} else {
	$rep_empno = "";
}

//보고대상자 사번체크
if(count($rep_empno) == 0){
	if (isset($_POST["rep_empno"])) {
		$rep_empno = $_POST["rep_empno"];
	} else {
		$rep_empno = '11111111';
	}
}
*/

// 스마트 보고관리자(시스템 관리자)
$aSmart = Array("11111113"); 
// 스마트 보고관리자(부속실 관리자)
$isReport = getSmartIsAdmin($sEmpno, $rep_empno); // 비서
// 스마트 메인(wb_main.php) 접근권한
$isMainReport = getSmartIsManage($sEmpno); // 본부장이상급

if($isReport == 0 || $isMainReport == 0){
    for ($a = 0; $a < count($aSmart); $a++) {
    	if ($aSmart[$a] == $sEmpno){
    		$isReport = 1;
    		$isMainReport = 1;
    		break;
    	}
    }
}

// $isReport = 1;
// $isMainReport = 1;

// echo '<pre>' , var_dump("sEmpno=".$sEmpno, "rep_empno=".$rep_empno, "isReport=".$isReport, "isMainReport=".$isMainReport) , '</pre>';
?>
