<?
/*#########################################
# 시스템명 : 스마트보고 시스템		                  #
# 작 성 일 : 2021.04.05                    #
# 파 일 명 : wb_admin_list.php               #
# 기능설명 : 스마트보고 관리자 화면	 		          #
#########################################*/
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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

/*
// 인증정보
include "./include/sso_auth.inc.php";

// 개인별 등록/신고내역 목록
$sTable_name01 = "KOSPOWB_REPORT";

$dateInclease = '';
$sWrite_date = '';
$test = 0;

isset($_POST['rep_empno']) ? $rep_empno	= trim($_POST['rep_empno']) : "";
isset($_POST['dateInclease']) ? $dateInclease = trim($_POST['dateInclease']) : "";
isset($_POST['write_date']) ? $sWrite_date = trim($_POST['write_date']) : "0";
// $_SESSION["rep_empno"] = $rep_empno;

echo $sEmpno, $rep_empno;
*/

$isReport = getSmartIsAdmin($sEmpno, $rep_empno); // 비서

if ($isReport != 1){ // 관리자가 아닌 경우
    // echo "조회권한이 없습니다.";
	// exit;

	?>
	<script language="JavaScript">
		alert("비서권한이 없습니다.");
		location.replace("http://scal.daewooenc.com/smart2/");
	</script>
	<?
}


if($dateInclease == 'plus'){
$test = '+1';
} else if($dateInclease == 'minus'){
$test = '-1';
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
  //$sTo_date = charReplace($sTo_date);
  $sWrite_date = $oci -> result($nIdx++);
  $sWriteDay = $oci -> result($nIdx++);
  //$sWrite_date = charReplace($sWrite_date);
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
<script src="./JS/common.js"></script>
<script src="./JS/jquery-1.7.2.min.js"></script>
<title>스마트 보고관리(비서)</title>
<link href="CSS/imp.css" rel="stylesheet" type="text/css" />
<style type="text/css">
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
	margin-right:20px;
	display:block;
	margin-top:-45px;
}
</style>
<script language="javascript">
    var obt = new Object();  
    function fnInit(){
    	fnSetData();
    	fnStartChk();
    	setInterval(fnSetData, 3500);
    }

    function fnGo(incleas){
        var f = document.frm;
        f.dateInclease.value = incleas;
        f.action = "wb_admin_list.php";
        f.submit();
    }
    
    function fnGoToday(){
        var f = document.frm;
        f.write_date.value = '';
		f.action = "wb_admin_list.php";
        f.submit();
    }

	function fnGoAdd(){
		var f = document.frm;
        // f.write_date.value = '';
        f.dateInclease.value = '';
        f.action = "wb_list.php";
		f.target = "_blank";
		f.submit();
		f.target = "";
    }

    function fnSetData(){
    	$.ajax({
    		async : true,
    		type:'POST',
    		url: 'ajaxList.php',
    		//data: paramSub,
    		data:{
    		    'sWrite_date' : '<?=$sWrite_date;?>',
    		    'sRep_empno' : '<?=$rep_empno;?>'
    		},
    		dataType: "json",
    		success:function(json){
    // 		  alert(json);  		    			
    			var retHtml = "";
    			var retHtml2 = "";
    			retHtml += "<table align=\"center\" width=\"100%\">";
                retHtml += "<colgroup>";
                retHtml += "    <col width=\"5%\"/>";
                retHtml += "    <col width=\"*\"/>";
                retHtml += "    <col width=\"8%\"/>";
                retHtml += "    <col width=\"18%\"/>";
                retHtml += "</colgroup>";
                retHtml += "<tbody>";
                retHtml += "  <tr class=\"table_header\">";
                retHtml += "      <td height=\"50px\" style=\"border-right:2px solid #E9F4F9\">시간</td>";
                retHtml += "      <td>보고내용</td>";
                retHtml += "      <td style=\"border-left:2px solid #E9F4F9\">보고상태</td>";
                retHtml += "      <td style=\"border-left:2px solid #E9F4F9\">진행현황</td>";
                retHtml += "  </tr>";
    			for(var i=8; i < 19; i++){
    				var hour = i;
    				if(hour < 10)
    					hour = '0'+i;
    					var back = "";
    					if(hour == 12){
    					    back = " style='background-color:#f0f0f0;'"
    					}
    					retHtml += "<tr"+back+">";
                        retHtml += "    <td class=\"td_time\">"+hour+"</td>";
                        retHtml += "    <td class=\"td_content\">";
                        if(json != null){
    					  var tempHtml = "";
    					  var tempCnt = 0;
    					  if(json.length > 0){
      						tempHtml += "<table width=\"100%\">";
    						  for(var j=0; j < json.length; j++){
    						    var timeTxt = "time_off";
      							var rep_time = json[j]['rep_time1'];
      							var p = json[j]['rep_time1'].indexOf(":");
    							  if(i == rep_time.substring(0,p)){
    								var colorTxt = "";
    								var long = 0;
    								var longTxt = "";
    								var attachTxt = "";
      								tempHtml += "<tr>";
      							    var tempTime = json[j]['rep_time1'].substring(3,5)+"분 ~ "+json[j]['rep_time2'].substring(3,5)+"분";
      								if(json[j]['rep_time1'].substring(0,2) != json[j]['rep_time2'].substring(0,2)){
      								  tempTime = json[j]['rep_time1'].substring(3,5)+"분~"+json[j]['rep_time2'].substring(0,2)+"시"+json[j]['rep_time2'].substring(3,5)+"분";
      								    long = 1;
      								}
      								if(long == 0){
      								    longTxt = "letter-spacing:2.5px;";
      								}
    								  if(json[j]['gubun'] == '0' && json[j]['stats'] == 'on'){
    								    timeTxt = "time_ov";
    									colorTxt += "style='background:#CD1AD4;"+longTxt+"' ";
    								} else {
    								    if(long == 0)
    								        colorTxt += "style='"+longTxt+"' ";
    								}
    								
    								if(json[j]['file_yn'] == 'Y' && json[j]['fileid'] != null){
    								    var tt = json[j]['fileid'].split("#");
    								    for(var k=0; k < tt.length; k++){
    								        var kk = tt[k].split("@");
    								        attachTxt += "<a href='./wb_file.php?file="+kk[0]+"&wbid="+json[j]['repid']+"&seq="+kk[1]+"'><img src='images/icon_file_new.gif' border='0' /></a>";
    								    }
    								    
    								}
    								
    								var titles = json[j]['title'];
    								if(json[j]['sc_yn'] == 'Y'){ // 일정은 파란색 글자로 표시
    								    titles = "<span style='color:blue;'>"+titles+"</span>";
    								}
    								if(json[j]['open_yn'] == 'N' && '<?=$sEmpno;?>' != json[j]['empno'] && '<?=$sEmpno;?>' != json[j]['bogo_empno'] && '<?=$isReport;?>' != 1) titles = "업무보고"; // 제목 비공개의 경우
    								if(json[j]['sc_yn'] == 'Y'){
    								    titles = "<a href='#;' onclick=\"addManaSche('"+json[j]['repid']+"');\">"+titles+"</a>";
    								}
    								if(json[j]['open_yn'] == 'N') titles += '<img src=\"images/sec.gif\" border=\"0\" />';
    								if(json[j]['sc_yn'] != 'Y') titles += "&nbsp;&nbsp;&nbsp;("+json[j]['bogo_grade_name']+" "+json[j]['bogo_name']+")";
    								tempHtml += "    <td style=\"padding-top:4px;width:200px;\"><div class=\""+timeTxt+"\" "+colorTxt+" >"+tempTime+"</div></td><td>&nbsp;"+titles+""+attachTxt+"</td>";
                      
    								tempHtml += "</tr>";
    								tempHtml += "<tr><td colspan=\"2\" height=\"5\"></td></tr>";
    								tempCnt++;
    							  }
    						  }
    						  tempHtml += "</table>";
    						  if(tempCnt == 0){
    							  tempHtml += "&nbsp;";
    						  }
    					  }
    				  }
    				  retHtml += tempHtml;
    				    retHtml += "    </td>";
    				    retHtml += "    <td class=\"td_status\">";
                        if(json != null){
    					    var tempHtml2 = "";
    					    var tempCnt2 = 0;
    					    if(json.length > 0){
      						    tempHtml2 += "<table width=\"100%\">";
    						    for(var j=0; j < json.length; j++){
    							    var rep_time = json[j]['rep_time1'];
    							    var p = json[j]['rep_time1'].indexOf(":");
									// 라인 수 일정, 서면대체 승인대기 등등
    							    if(i == rep_time.substring(0,p)){ 
    							        var colorTxt = "";
    							        var stateTxt = "";
    								    tempHtml2 += "  <tr>";
    								    if(json[j]['dq_yn'] == 'Y'){
    							            colorTxt += "style='color:#8D8D8D;padding:4px 12px 8px 12px;cursor:pointer;' onmouseover=\"detailInfoScheView('"+json[j]['bogo_grade_name']+" "+json[j]['bogo_name']+"','','');\" onmouseout=\"divScheduleClose();\" ";
          								    stateTxt += "서면대체";
          								} else if(json[j]['sc_yn'] == 'Y'){
              							    colorTxt += "style='color:blue;padding:4px 12px 8px 12px;'";
              							    stateTxt += "일정";
    								    } else {
              								if(json[j]['gubun'] == '0' && json[j]['stats'] == '0'){
              								    colorTxt += "style='color:#3c3c3c;padding:4px 12px 8px 12px;' onmouseout=\"divScheduleClose();\" ";
              								    stateTxt += "확정";
              								} else if(json[j]['gubun'] == '0' && json[j]['stats'] == 'on'){
              								    colorTxt += "style='color:#CD1AD4;padding:4px 12px 8px 12px;cursor:pointer;' onmouseover=\"detailInfoScheView('"+json[j]['bogo_grade_name']+" "+json[j]['bogo_name']+"','"+json[j]['stats_date'].substring(0,5)+"','');\" onmouseout=\"divScheduleClose();\" ";
              								    stateTxt += "보고중";
              								} else if(json[j]['gubun'] == '0' && json[j]['stats'] == 'off'){
              								    colorTxt += "style='color:#8D8D8D;padding:4px 12px 8px 12px;cursor:pointer;' onmouseover=\"detailInfoScheView('"+json[j]['bogo_grade_name']+" "+json[j]['bogo_name']+"','"+json[j]['stats_date'].substring(0,5)+"','"+json[j]['end_date'].substring(0,5)+"');\" onmouseout=\"divScheduleClose();\" ";
              								    stateTxt += "보고완료";
              								} else if(json[j]['gubun'] == '2'){
              								    colorTxt += "style='color:#F48814;padding:4px 12px 8px 12px;' onmouseout=\"divScheduleClose();\" ";
              								    stateTxt += "반려";
              								} else {
              								    colorTxt += "style='color:#3c3c3c;padding:4px 12px 8px 12px;' onmouseout=\"divScheduleClose();\" ";
              									stateTxt += "승인대기";
              								}
          								}
        							if(json[j]['sc_yn'] != 'Y' && '<?=$sEmpno;?>' == json[j]['empno'] && (json[j]['gubun'] == '0' && json[j]['stats'] == '0' || json[j]['gubun'] == '2')){
    								    stateTxt = "<a href='#' onclick=\"fnView('"+json[j]['repid']+"');return false;\">"+stateTxt+"</a>";
    								}
      								tempHtml2 += "    <td "+colorTxt+">"+stateTxt+"</td>";
    								  tempHtml2 += "  </tr>";
    								  tempHtml2 += "  <tr><td height=\"5\"></td></tr>";
    								  tempCnt2++;
    							  }
    						  }
    						  tempHtml2 += "</table>";
    						  if(tempCnt2 == 0){
    							  tempHtml2 += "&nbsp;";
    						  }
    					  }
    				}
    				retHtml += tempHtml2;
    				retHtml += "    </td>";
    				retHtml += "    <td class=\"td_status\">";
    				if(json != null){
    					  var tempHtml3 = "";
    					  var tempCnt3 = 0;
    					  if(json.length > 0){
      						tempHtml3 += "<table width=\"100%\">";
    						  for(var j=0; j < json.length; j++){
    							  var rep_time = json[j]['rep_time1'];
    							  var p = json[j]['rep_time1'].indexOf(":");
    							  if(i == rep_time.substring(0,p)){				
    								  tempHtml3 += "  <tr>";
    								  tempHtml3 += "	  <td style='padding:4px 12px 8px 12px;'>";
    								  tempHtml3 += "	    <div id=\"proBtn\" >";
    								  if(json[j]['sc_yn'] != 'Y'){
        								  if(json[j]['dq_yn'] == 'Y'){
        								      tempHtml3 += "&nbsp;";
        								  } else {
            								  if(json[j]['gubun'] == '2'){
            									  tempHtml3 += "&nbsp;";
            								  } else if(json[j]['gubun'] == '0' && json[j]['stats'] == '0'){
            									//   tempHtml3 += "    <img src=\"./images/bt18.gif\" style=\"cursor:pointer;\" border=\"0\" onClick=\"return fnTimeMod('"+json[j]['repid']+"');\">&nbsp;";
            									//   tempHtml3 += "    <img src=\"./images/bt_start.gif\" border=\"0\"  onclick=\"fnTimeJo('"+json[j]['repid']+"','"+json[j]['rep_time1']+"');\" style=\"cursor:pointer;\">";
            									//   tempHtml3 += "    <img src=\"./images/bt13.gif\" style=\"cursor:pointer\" border=\"0\" onClick=\"return frmBak('"+json[j]['repid']+"',this);\">";
												  tempHtml3 += "    <span class=\"bt_confirm\" onClick=\"return fnTimeMod('"+json[j]['repid']+"');\">시간수정</span>&nbsp;";
            									  tempHtml3 += "    <span class=\"bt_confirm\" onclick=\"fnTimeJo('"+json[j]['repid']+"','"+json[j]['rep_time1']+"');\">시작</span>&nbsp;";
            									  tempHtml3 += "    <span class=\"bt_confirm\" onClick=\"return frmBak('"+json[j]['repid']+"',this);\">반려</span>";
            								  } else if(json[j]['gubun'] == '0' && json[j]['stats'] == 'on'){
            									//   tempHtml3 += "    <img src=\"./images/bt_stop.gif\" border=\"0\"  onclick=\"fnSetTime('"+json[j]['repid']+"','E',this);\" style=\"cursor:pointer;\">";
												  tempHtml3 += "    <span class=\"bt_confirm\" onclick=\"fnSetTime('"+json[j]['repid']+"','E',this);\">종료</span>";
            								  } else if(json[j]['gubun'] == '0' && json[j]['stats'] == 'off'){
            									  tempHtml3 += "&nbsp;";
            								  } else {
												//   tempHtml3 += "    <img src=\"./images/bt12.gif\" style=\"cursor:pointer\" border=\"0\" onClick=\"fnProcess('"+json[j]['repid']+"','0','"+json[j]['rep_time1']+"',this);\">&nbsp;";
												  tempHtml3 += "    <span class=\"bt_confirm\" onClick=\"fnProcess('"+json[j]['repid']+"','0','"+json[j]['rep_time1']+"',this);\">확정</span>&nbsp;";
            									  tempHtml3 += "    <span class=\"bt_confirm\" onClick=\"return fnTimeMod('"+json[j]['repid']+"');\">시간수정</span>&nbsp;";
            									  tempHtml3 += "    <span class=\"bt_confirm\" onClick=\"return frmBak('"+json[j]['repid']+"',this);\">반려</span>";
            									 
												//   tempHtml3 += "    <img src=\"./images/bt18.gif\" style=\"cursor:pointer\" border=\"0\" onClick=\"return fnTimeMod('"+json[j]['repid']+"');\">&nbsp;";
            									//   tempHtml3 += "    <img src=\"./images/bt13.gif\" style=\"cursor:pointer\" border=\"0\" onClick=\"return frmBak('"+json[j]['repid']+"',this);\">";
            								  }
        								  }
    								  } else { 
										//   일정이 있을경우 공백으로 두기
										tempHtml3 += "&nbsp;";
									  }
    
    								  tempHtml3 += "	    </div>";
    								  tempHtml3 += "	  </td>";
    								  tempHtml3 += "  </tr>";
    								  tempHtml3 += "   <tr><td height=\"5\"></td></tr>";
    								  tempCnt3++;
    							  }
    						  }
    						  tempHtml3 += "</table>";
    						  if(tempCnt3 == 0){
    							  tempHtml3 += "&nbsp;";
    						  }
    					  }
    				}
    				retHtml += tempHtml3;
    				retHtml += "    </td>";
    				retHtml += "  </tr>";
    			}
    			retHtml += "  </tbody>";
    			retHtml += "</table>";
    			$("#bogoCont").html(retHtml);
    		},
    		error : function (xhr, ajaxOptions,thrownError){
    			//alert('검색 대상자가 존재하지 않습니다.');
    			//alert(thrownError+':message=='+xhr.responseText);
    		}
    	});
    }
    
    function retBak(){
		document.getElementById('oDiv').style.display="none";
		$('#TextA').val("");
		//tinyMCE.activeEditor.getDoc().body.innerHTML = '';
		//tinyMCE.triggerSave();
	}

	function frmBak(sRepid, obj) {
        obt = obj;
        $('#retBtn').html("<input type=\"button\" value=\"확 인\" class=\"bt\" onclick=\"fnProcess('"+sRepid+"','2','','')\">&nbsp;<input type=\"button\" value=\"취 소\" class=\"bt\" onclick=\"retBak();\">");
        document.getElementById('oDiv').style.display="";
        document.getElementById('oDiv').style.posTop = "500";
        document.getElementById('oDiv').style.posLeft = "700";
        document.getElementById('TextA').focus();
	}
	
	function fnStartChk(){
		$.ajax({
			async : true,
			type:'POST',
			url: 'ajaxProc.php',
			//data: paramSub,
			data:{
                'procGubun':'startChk'
			},
			dataType: "json",
			success:function(json){
			},
			error : function (xhr, ajaxOptions,thrownError){
				//alert('검색 대상자가 존재하지 않습니다.');
				//alert(thrownError+':message=='+xhr.responseText);
			}
		});
    }
	
	function fnSetTime(sRepid, type, obj){
	    var msgtxt = "";
		if(type == 'S' && ingCnt > 0){
			alert('보고중 입니다.');
			return;
		}
	    if(type == 'S'){
		    msgtxt = "보고를 시작 하시겠습니까?"
		  } else if(type == 'E'){
		    msgtxt = "보고를 완료 하시겠습니까?"
		  }
		  
		  if(confirm(msgtxt)){	  
    		$.ajax({
    			async : true,
    			type:'POST',
                url: 'ajaxProc.php',    			
    			//data: paramSub,
    			data:{
                    'procGubun':'listProc',
    				'sRepid': sRepid,
    				'rep_empno': $('#rep_empno').val(),
    				'sType' : type
    			},
    			dataType: "json",
    			success:function(json){
  				  if(type == 'S'){
  				    // obj.parentNode.innerHTML = "<img src=\"./images/bt_stop.gif\" border=\"0\"  onclick=\"fnSetTime('"+sRepid+"','E',this);\" style=\"cursor:pointer;\">";
					obj.parentNode.innerHTML = "<span class=\"bt_confirm\" onclick=\"fnSetTime('"+sRepid+"','E',this);\">종료</span>";
  				  } else if(type == 'E'){
  				    obj.parentNode.innerHTML = "<img src=\"./images/bogowan.gif\" border=\"0\"";
  				  }
    			},
    			error : function (xhr, ajaxOptions,thrownError){
    				//alert('검색 대상자가 존재하지 않습니다.');
    				alert(thrownError+':message=='+xhr.responseText);
    			}
    		});
  		}
    }
	
    function fnProcess(sRepid, gubun, repTime, obj){
      var msgtxt = "";
    	if(gubun == '0'){
    		msgtxt = "보고를 확정 하시겠습니까?";
    		if(Number('<?=substr($sWrite_date,0,4);?><?=substr($sWrite_date,5,2);?><?=substr($sWrite_date,8,2);?>') < Number('<?=substr($sTo_date,0,4);?><?=substr($sTo_date,5,2);?><?=substr($sTo_date,8,2);?>') || Number('<?=substr($sWrite_date,0,4);?><?=substr($sWrite_date,5,2);?><?=substr($sWrite_date,8,2);?>') == Number('<?=substr($sTo_date,0,4);?><?=substr($sTo_date,5,2);?><?=substr($sTo_date,8,2);?>') && Number(repTime.substring(0,2)) < Number('<?=substr($sTo_date,11,2);?>')){
    			alert('지난시간에는 확정 할 수 없습니다.');
    			return;
    		}
    	} else if(gubun == '2'){
    		msgtxt = "보고를 반려 하시겠습니까?";
    	  //tinyMCE.triggerSave();
    	}
    
      if(confirm(msgtxt)){	  
        $.ajax({
       		async : true,
       		type:'POST',
       		url: 'ajaxProc.php',
       		//data: paramSub,
       		data:{
       		    'procGubun':'proc',
       			'sRepid': sRepid,
       			'sReason': $('#TextA').val(),
       			'sGubun' : gubun
       		},
       		dataType: "json",
       		success:function(json){
    		  	var btns = "";
          		if(gubun == '0'){
      		//   alert('확정되었습니다.');
    				//btns += "<img src=\"./images/bt18.gif\" style=\"cursor:pointer\" border=\"0\" onClick=\"return frmBak();\">&nbsp;<img src=\"./images/bt_start.gif\" border=\"0\"  onclick=\"fnSetTime('"+sRepid+"','S',this);\" style=\"cursor:pointer;\">";
    				// btns += "<img src=\"./images/bt18.gif\" style=\"cursor:pointer\" border=\"0\" onClick=\"return fnTimeMod('"+sRepid+"');\"();\">&nbsp;"
					// btns += "<img src=\"./images/bt_start.gif\" border=\"0\"  onclick=\"fnTimeJo('"+sRepid+"','"+repTime+"');\" style=\"cursor:pointer;\">&nbsp;"
					// btns += "<img src=\"./images/bt18.gif\" border=\"0\"  onclick=\"frmBak('"+sRepid+"');\" style=\"cursor:pointer;\">";
					btns += "    <span class=\"bt_confirm\" onClick=\"return fnTimeMod('"+sRepid+"');\"();\">시간수정</span>&nbsp;";
					btns += "    <span class=\"bt_confirm\" onclick=\"fnTimeJo('"+sRepid+"','"+repTime+"');\">시작</span>&nbsp;";
					btns += "    <span class=\"bt_confirm\" onclick=\"frmBak('"+sRepid+"');\">반려</span>";
    				obj.parentNode.innerHTML = btns;
    			} else if(gubun == '2'){
    				//alert('반려되었습니다.');
    				document.getElementById('oDiv').style.display="none";
    				$('#TextA').val("");
    				///tinyMCE.activeEditor.getDoc().body.innerHTML = '';
    			  obt.parentNode.innerHTML = btns;
    			}
    			  //obj.parentNode.innerHTML = btns;
        	},
        	error : function (xhr, ajaxOptions,thrownError){
        		//alert('검색 대상자가 존재하지 않습니다.');
        	  //alert(thrownError+':message=='+xhr.responseText);
          }
        });
      }
    }

    function fnTimeJo(reqid, repTime){
    	if(Number('<?=substr($sWrite_date,0,4);?><?=substr($sWrite_date,5,2);?><?=substr($sWrite_date,8,2);?>') != Number('<?=substr($sTo_date,0,4);?><?=substr($sTo_date,5,2);?><?=substr($sTo_date,8,2);?>') || Number('<?=substr($sWrite_date,0,4);?><?=substr($sWrite_date,5,2);?><?=substr($sWrite_date,8,2);?>') == Number('<?=substr($sTo_date,0,4);?><?=substr($sTo_date,5,2);?><?=substr($sTo_date,8,2);?>') && Number(repTime.substring(0,2)) < Number('<?=substr($sTo_date,11,2);?>')){
    		if(Number('<?=substr($sWrite_date,0,4);?><?=substr($sWrite_date,5,2);?><?=substr($sWrite_date,8,2);?>') != Number('<?=substr($sTo_date,0,4);?><?=substr($sTo_date,5,2);?><?=substr($sTo_date,8,2);?>')){
    			alert('금일자 보고만 시작 할 수 없습니다.');
    		} else if(Number('<?=substr($sWrite_date,0,4);?><?=substr($sWrite_date,5,2);?><?=substr($sWrite_date,8,2);?>') == Number('<?=substr($sTo_date,0,4);?><?=substr($sTo_date,5,2);?><?=substr($sTo_date,8,2);?>') && Number(repTime.substring(0,2)) < Number('<?=substr($sTo_date,11,2);?>')){
    			alert('지난시간에는 보고 시작 할 수 없습니다.');
    		}
    		
    		return;
    	}
    	window.open("wb_time_jo.php?repid="+reqid,"wb_view","width=710,height=300,menubar=no,center=yes,scrollbars=no,help=no,status=no,resizable=no,top=100,left=500");
    }

    function detailInfoScheView(bogoName, startTime, endTime){	
        var htmltxt = '';
        htmltxt += '<div id="DetailInfoViewTemp" style="z-index:999;border:1px solid #000;background-color:#fff;padding:10px 0;">';
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
    	htmltxt += '						<td class="td_status">'+bogoName+'</td>';
        htmltxt += '						<td class="td_status" style=\"border-left:2px solid #E9F4F9;\">'+startTime+'</td>';
        htmltxt += '						<td class="td_status" style=\"border-left:2px solid #E9F4F9;border-right:2px solid #E9F4F9;\">'+endTime+'</td>';
    	htmltxt += '					</tr>';
    	htmltxt += '				</table>';
    	htmltxt += '			</td>';
        htmltxt += '        </tr>';
        htmltxt += '        <tr>';
        htmltxt += '			<td colspan="3" bgcolor="4e7bbd"></td>';
        htmltxt += '		</tr>';
        htmltxt += '	</table>';
        htmltxt += '</div>';
        var top=event.clientY + document.body.scrollTop;
        var left=event.clientX + document.body.scrollLeft;
        
        top = top+10;
        left = left-190
        
        $('#tipdiv').css("left",left+'px');
        $('#tipdiv').css("top",top+'px');	
    
        $('#tipdiv').html(htmltxt);
        $('#tipdiv').show();
    }
    
    function divScheduleClose(){
        $('#tipdiv').html('');
        $('#tipdiv').hide();
    }

</script>
<body onload="fnInit();">
<div id="tipdiv" style="display:none;position:absolute;"></div>
<form name="frm" method="post">
<input type="hidden" name="dateInclease" id="dateInclease" value="<?=$dateInclease;?>" />
<input type="hidden" name="write_date" id="write_date" value="<?=$sWrite_date;?>" />
<input type="hidden" name="rep_empno" id="rep_empno" value="<?=$rep_empno;?>" />
<input type="hidden" name="sEmpNo" value="<?=$sEmpno?>">
<input type="hidden" name="repid" value="" />
<DIV id="oDiv" style="position:absolute;display:none;background-color:#FFFFFF;border:1px solid #CCCCCC;width:400px;height:100px;">
	<table border="1" cellpadding="0" cellspacing="0" borderCOlor="#BAE5F1" style="border-collapse:collapse;background-color:#fff;">
		<TR><TD align="center" style="color:#58B7D3;font-size:15pt;font-weight:bold;" height="30" bgColor="#D6EFF6">반려사유</TD></TR>
		<TR><TD align="center" style="color:#666666;font-size:9pt" height="1" bgColor="#CCCCCC"></TD></TR>
		<TR><TD><textarea  name="TextA" id="TextA" style="border:0px;background-color:#FFFFFF;width:400px;height:100px"></textarea></TD></TR>
		<TR><TD align="center" style="color:#666666;font-size:9pt" height="1" bgColor="#CCCCCC"></TD></TR>
		<TR><TD align="center" height="40"><div style="background-color:#fff;z-index:11"><div style="z-index:10" id="retBtn"></div></div></TD></TR>
    </table>
</DIV>	
</form>
<table width="100%" height="90px" style="margin-bottom:5px;border:1px solid #e2e2e2;background-color:#0a2554;">
    <tr>
        <td>
            <table width="1300px" height="87px" align="center" style="background:url(images/title_bg.gif) no-repeat;">
                <tr>
                    <td width="302px" align="left" style="color: white;">
						<!-- <a href="http://scal.daewooenc.com/smart2/"><img src="images/daewooenc_symbol.png" width="40"/>&nbsp;&nbsp;<img src="images/title_left.png"/></a> -->
						<div class="rotation_parent">
							<a href="http://scal.daewooenc.com/smart2/">
								<div class="rotation">
									<img src="images/daewooenc_symbol.png" width="40" />
								</div>
								<img src="images/title_left.png">
							</a>
						</div>
						<!-- 비서 -->
						<!-- <?= $sEmpno."->".$rep_empno."/".$isReport.$isMainReport ?> -->
                    </td>
                    <td align="right"><div class="date" style="color:#ffffff;font-size:25px;"><?=substr($sWrite_date,0,4);?>년 <?=substr($sWrite_date,5,2);?>월 <?=substr($sWrite_date,8,2);?>일&nbsp;(<?=$sWriteDay;?>)</div></td>
                    <td width="150px" align="right">
                        
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
<!--div style="position: absolute;right: 16.1%;padding:5px 5px;border:1px solid #000;"><a href="#" onclick="addManaSche('');" >일정등록</a></div>
<div style="position: absolute;right: 21.1%;top:127px;"></div-->
<table width="100%" >
    <tr>
        <td>
            <table width="1300px" height="40" align="center">
				<tr>
                    <!-- <td width="8%"><div style="float:left;padding:3px 11px;border:1px solid #000;background-color:lavender;"><a href="#" onclick="manaSche('');" >일정보기</a></div></td> -->
					<td width="16%" align="left"><a href="#" onclick="manaSche('');" ><span class="bt_main">일정보기</span></a>&nbsp;&nbsp;<a href="#" onclick="addManaSche('');" ><span class="bt_main">일정등록</span></a></td>
					<!-- <td><div style="float:left;padding:3px 11px;border:1px solid #000;background-color:lavender;"><a href="#" onclick="manaadd('');" >보고등록</a></div></td> -->
                    <!-- <td class="date_navi" align="center"> -->
                    <td>
						<span style="font-size:20px; font-weight:800;vertical-align: middle; ">&nbsp; <?=$sWrite_date;?> (<?=$sWriteDay;?>)&nbsp;</span>
						<img src="images/prev.gif" height="29px" border="0" onclick="fnGo('minus');" style="cursor:pointer; vertical-align:middle;"/>
					    <span style="border:1px solid #9c9c9c; padding:3px 8px; font-size:16px; font-weight:550; vertical-align:middle;"><a href="#" onclick="fnGoToday();" >오늘</a></span>
					    <img src="images/next.gif" height="29px" onclick="fnGo('plus');" style="cursor:pointer;vertical-align:middle;"/>&nbsp;&nbsp;
                    </td>
                    <td width="30%" style="text-align:right;">
                        <!-- <div style="float:left;padding-top:5px;padding-left:2px;text-align:right;"><?=$aPstnName?>님 보고일정</div>
						<div style="float:right;padding:3px 11px;border:1px solid #000;background-color:lavender;"> <a href="#" onclick="fnGoAdd();" >보고등록</a></div> -->
						<a href="#" onclick="fnGoAdd();"><span class="bt_main">보고등록</span></a>&nbsp;&nbsp;&nbsp;
						<span style="padding-top:5px;padding-left:2px;text-align:right;"><?=$aPstnName?>님 보고일정
						</span>
						
                    </td>
                </tr>
            </table>
            <table width="1300px" align="center">
				<tr>
					<td>
						<div id="bogoCont"></div>
					</td>
				</tr>
			</table>
        </td>
    </tr>
</table>
</body>
</html>
