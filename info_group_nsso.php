<?
$sOrgTableName = "SMART_AUTH_DEPT";
 
// 전달받은 소속코드 배열처리
for($aryNumber=0,$i=0 ; $i<5 ; $i++,$aryNumber+=4){
    $deptno_sub[$i] = substr($deptno,$aryNumber,4);
	// echo '<pre>' , var_dump("deptno_sub[$i]=".$deptno_sub[$i]) , '</pre>';
}

function organism_list_group_first() // 남부 본사 테스트
{
	global $ora,$com_code,$sOrgTableName,$choGbn;
	$query_main = "SELECT ORG_TOP2_CD||ORG_TOP3_CD||ORG_TOP4_CD||ORG_TOP5_CD TOT_ORG_CD, NAME ORG_NM, ORG_LEVEL FROM $sOrgTableName WHERE ORG_LEVEL=2 AND BONSA_YN='Y' AND TRNS_KEY <> '0000' AND STATUS='Y'  ORDER BY DEPT_SORT";
	// SELECT ORG_TOP2_CD||ORG_TOP3_CD||ORG_TOP4_CD||ORG_TOP5_CD TOT_ORG_CD, NAME ORG_NM, ORG_LEVEL FROM SMART_AUTH_DEPT WHERE ORG_LEVEL=2 AND BONSA_YN='Y' AND TRNS_KEY <> '0000' AND STATUS='Y' ORDER BY DEPT_SORT
	// ORG_LEVEL이 2인 팀코드의 팀명 과 상위코드를 가져옴
	// echo($query_main);

	$rows_main = $ora->FetchRows($query_main);
	$rows_main_count = count($rows_main);
	for($i = 0 ; $i < $rows_main_count ; $i++){ // 본사 조직 및 사업소
		$deptname = $rows_main[$i]['ORG_NM'];

		// echo '<pre>' , var_dump("deptnoup=".$rows_main[$i]['TOT_ORG_CD'], "deptname=".$deptname, "choGbn=".$choGbn) , '</pre>';
		// organism_list_group_sub($rows_main[$i]['TOT_ORG_CD'],$deptname,0,0,$choGbn);
		organism_list_group_sub($rows_main[$i]['TOT_ORG_CD'],$deptname,0,0);
		

	}
}

function organism_list_group_branch() // 남부 사업소 리스트
{
	global $ora,$com_code,$sOrgTableName,$choGbn;
	$query_main = "SELECT ORG_TOP3_CD||ORG_TOP4_CD||ORG_TOP5_CD TOT_ORG_CD, NAME ORG_NM, ORG_LEVEL FROM $sOrgTableName WHERE ORG_LEVEL=3 AND BONSA_YN='N' ORDER BY DEPT_SORT";
	$rows_main = $ora->FetchRows($query_main);
	$rows_main_count = count($rows_main);
	for($i = 0 ; $i < $rows_main_count ; $i++){ // 본사 조직 및 사업소
		organism_list_group_sub($rows_main[$i]['TOT_ORG_CD'],$rows_main[$i]['ORG_NM'],0,1,$choGbn);
	}
}

function ext_list_group_first() // 남부 외부직원 리스트
{
	global $ora,$com_code,$sOrgTableName,$choGbn;
	$query_main = "SELECT ORG_TOP2_CD||ORG_TOP3_CD||ORG_TOP4_CD||ORG_TOP5_CD TOT_ORG_CD, NAME ORG_NM, ORG_LEVEL FROM $sOrgTableName WHERE ORG_LEVEL=2 AND BONSA_YN='Y' AND STATUS='Y' AND TRNS_KEY='0000' ORDER BY DEPT_SORT";
	$rows_main = $ora->FetchRows($query_main);
	$rows_main_count = count($rows_main);
	for($i = 0 ; $i < $rows_main_count ; $i++){ // 본사 조직 및 사업소
		$deptname = $rows_main[0]['ORG_NM'];
		organism_list_group_sub($rows_main[$i]['TOT_ORG_CD'],$deptname,0,0,$choGbn);
	}
}

// 사업소상위코드,사업소상위부서명,소속사업소dept 깊이, 본사사업소구분
// 0020, 보고부2, 0, 0
function organism_list_group_sub($deptnoup,$deptname,$length,$gubun)
{
	// echo '<pre>' , var_dump("deptnoup=".$deptnoup, "deptname=".$deptname, "length=".$length "gubun=".$gubun) , '</pre>';
	global $ora,$deptno_sub,$PAGE_NSSO_URL,$com_code,$sOrgTableName,$user_empno,$choGbn;

	for ($lengthNumber=0,$k=0; $k<10; $lengthNumber+=4,$k++) {
        $length_array[$lengthNumber] = $k; // 배열값 저장
		// echo($k);
    }
	$next_group = $deptno_sub[$length_array[$length]]; // 검색트리
	
    // 트리구조 길이
    if($gubun == 1) $nArange = 2;
    else $nArange = 1;
    
    // 인덴트 차수
    $dept_count = (strlen($deptnoup)/4) + $nArange;
    $nLevel = $dept_count + 1; // 쿼리내 조직차수
    
	$this_deptnoup = substr($deptnoup,$length,4); // 현재 상위코드
        
	// 현재트리의 하위 사업소 확인
	$query_subOfsub = "SELECT COUNT(*) CNT FROM $sOrgTableName WHERE ORG_TOP".$dept_count."_CD = '".$this_deptnoup."' AND ORG_LEVEL='$nLevel' ORDER BY DEPT_SORT";
	// echo '<pre>' , var_dump("query_subOfsub=".$query_subOfsub) , '</pre>';
	// SELECT COUNT(*)  CNT FROM SMART_AUTH_DEPT WHERE ORG_TOP2_CD = '0020' AND ORG_LEVEL='3' ORDER BY DEPT_SORT

	$is_subOfrow = $ora->FetchRow($query_subOfsub);
	$is_subOfsub = $is_subOfrow['CNT'];
	
	if($this_deptnoup === $next_group)  // 현재 트리와 검색트리가 같을 경우
	{
		// 검색트리의 하위 사업소 확인
		$query_tree = "SELECT COUNT(*) FROM $sOrgTableName WHERE ORG_TOP".$dept_count."_CD = '".$this_deptnoup."' AND ORG_LEVEL='$nLevel' ORDER BY DEPT_SORT";
		$is_tree = count($ora->FetchRow($query_tree));
		$buttom_img = "08";
		$font_style_forward ="<font color=\"#868038\"><b>";
		$font_style_rear ="</b></font>";
	}
	else
	{
		$buttom_img = "09";
		$font_style_forward ="";
		$font_style_rear ="";
	}

	$href_forward = "<a href=".$PAGE_NSSO_URL."?deptcode=".$deptnoup."&choGbn=".$choGbn.">";
	$href_rear = "</a>";

	if($is_subOfsub==0) // 현사업소가 예하사업소를 가지고 있지 않을경우
	{
		$buttom_img = "12";
	}
?>
	<table width="184" border="0" cellspacing="0" cellpadding="0">
		<tr> 
		<!-- 본사조직에 대한 사업소 인덴트 조정 -->
			<? for($dept=0; $dept <= ($length/4); $dept++){ ?>
			<td nowrap width="12"></td>
			<? } ?>
			<td nowrap width="13"><?=$href_forward?><img src="imagesGE/left_img<?=$buttom_img?>.gif" border="0"><?=$href_rear?></td>
			<td class="common-td-top2"><?=$href_forward?><?=$font_style_forward?><?=$deptname?><?=$font_style_rear?><?=$href_forward?></td>
		</tr>
	</table>
<?
	// if($is_tree){
		$query_sub = "SELECT ";
        if($gubun == 0) $query_sub .= " ORG_TOP2_CD|| ";
        $query_sub .= " ORG_TOP3_CD|| ";
		$query_sub .= "ORG_TOP4_CD||ORG_TOP5_CD||ORG_TOP6_CD TOT_ORG_CD, NAME ORG_NM FROM $sOrgTableName WHERE ORG_TOP".$dept_count."_CD = '".$this_deptnoup."' AND ORG_LEVEL='$nLevel' ORDER BY DEPT_SORT";

		$rows_sub = $ora->FetchRows($query_sub);
		$rows_sub_count = count($rows_sub);
		$next_length = $length + 4;
		
		for($i = 0 ; $i < $rows_sub_count ; $i++){
			// organism_list_group_sub($rows_sub[$i][0],$rows_sub[$i][1],$next_length,$gubun);
			organism_list_group_sub($rows_sub[$i]['TOT_ORG_CD'],$rows_sub[$i]['ORG_NM'],$next_length,$gubun);
		}
	// }
}
?>
<table width="184" border="0" cellspacing="0" cellpadding="0">
	<tr> 
		<?= $com_name="대우건설" ?>
		<td class="common-td-top2">&nbsp;&nbsp;<font color="#868038"><a href="./info_inwon_nsso.php"><b><?=$com_name?></b></a></font></td>
	</tr>
</table>
<?
organism_list_group_first(); // 리스트 함수호출
// organism_list_group_branch();
// ext_list_group_first();
?>