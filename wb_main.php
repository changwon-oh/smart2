<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
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
// 인증정보
include "./include/sso_auth.inc.php";

if($isMainReport != 1){ // 관리자가 아닌 경우
    // echo "조회권한이 없습니다.";
	// exit;

	?>
	<script language="JavaScript">
		alert("권한이 없습니다.");
		location.replace("http://scal.daewooenc.com/smart2/");
	</script>
	<?
}

isset($_POST['rep_empno']) ? $rep_empno	= trim($_POST['rep_empno']) : "";
$dateInclease = '';
$sWrite_date = '';

isset($_POST['dateInclease']) ? $dateInclease = trim($_POST['dateInclease']) : "";
isset($_POST['write_date']) ? $sWrite_date = trim($_POST['write_date']) : "0";

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
  $sWrite_date = $oci -> result($nIdx++);
  $sWrite_day = $oci -> result($nIdx++);
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
	$aPstnName 			    = $oci -> result(1);
}
$oci -> parseFree();

$oci -> disconnect();
$roci -> disconnect();
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>스마트 보고관리(본부장님)</title>
<script src="./JS/common.js"></script>
<script src="./JS/jquery-1.7.2.min.js"></script>
<link href="CSS/imp.css" rel="stylesheet" type="text/css" />
<script language="javascript">
var exam_count = 0;
var temp_count = 0;
var over_count = 0;
var temp_over_count = 0;
var timeStop = 0;
var temp = 1;
    function fnInit(){
      fnSetData();
      setInterval(fnSetData, 3500);
    }
    
    function fnGo(incleas){
        var f = document.frm;
        f.dateInclease.value = incleas;
        f.action = "wb_main.php";
        f.submit();
    }

    function fnGoToday(){
        var f = document.frm;
        f.write_date.value = '';
        f.action = "wb_main.php";
        f.submit();
    }
    function fnSetData(){
        $.ajax({
      	    async : true,
      		type:'POST',
      		url: 'ajaxMain.php',
      		data:{
				'sWrite_date' : '<?=$sWrite_date;?>'.replace(/－/gi,'-'),
				'sRep_empno' : '<?=$rep_empno;?>'
      		},
      		dataType: "json",
      		success:function(json){
				// console.log(json)
    			var onCnt = 0;
    			var retHtml = "";
    			var retHtml2 = "";  				
    			var retHtml3 = "";
    			retHtml += "<table align=\"center\" width=\"100%\">";
                retHtml += "    <colgroup>";
                retHtml += "        <col width=\"5%\"/>";
                retHtml += "        <col width=\"*\"/>";
                retHtml += "        <col width=\"19%\"/>";
                retHtml += "    </colgroup>";
                retHtml += "    <tbody>";
    		    retHtml += "    <tr class=\"table_header\">";
    		    retHtml += "        <td height=\"50px\" style=\"border-right:2px solid #E9F4F9\">시간</td>";
    		    retHtml += "        <td>보고내용</td>";
    		    retHtml += "        <td style=\"border-left:2px solid #E9F4F9\">보고상태</td>";
    		    retHtml += "    </tr>";
    			for(var i=8; i < 19; i++){
    				var hour = i;
    				if(hour < 10)
    				hour = '0'+i;
    				var back = "";
    				if(hour == 12){
    				    back = " style='background-color:#f0f0f0;'"
    				}
    				retHtml += "<tr"+back+">";
    	            retHtml += "        <td class=\"td_time\">"+hour+"</td>";
    				retHtml += "        <td class=\"td_content\">";
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
    								
    								if(json[j]['fileid'] != null){
    								    var tt = json[j]['fileid'].split("#");
    								    for(var k=0; k < tt.length; k++){
    								        var kk = tt[k].split("@");
    								        attachTxt += "<a href='./wb_file.php?file="+kk[0]+"&wbid="+json[j]['repid']+"&seq="+kk[1]+"'><img src='images/icon_file_new.gif' border='0' /></a>";
    								    }
    								}
    								
    								var titles = json[j]['title'];
    								if(json[j]['sc_yn'] == 'Y'){
    								    titles = "<span style='color:blue;'>"+titles+"</span>";
    								}
    								
    								if(json[j]['sc_yn'] != 'Y') titles += "&nbsp;&nbsp;&nbsp;("+json[j]['bogo_grade_name']+" "+json[j]['bogo_name']+")";									
    								tempHtml += "    <td style=\"padding-top:4px;width:200px;\"><div class=\""+timeTxt+"\"  "+colorTxt+" >"+tempTime+"</div></td><td>&nbsp;"+titles+""+attachTxt+"</td>";
                                    tempHtml += "</tr>";
                                    tempHtml += "<tr><td colspan=\"2\" height=\"5\" ></td></tr>";
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
    					var tempHtml4 = "";
    					var tempCnt4 = 0;
                        if(json.length > 0){
      						tempHtml4 += "<table width=\"100%\">";
                            for(var j=0; j < json.length; j++){
    							var rep_time = json[j]['rep_time1'];
    							var p = json[j]['rep_time1'].indexOf(":");
    						    if(i == rep_time.substring(0,p)){
    						        var colorTxt = "";
    						        var stateTxt = "";
    						        var dqTxt = "";
    							    tempHtml4 += "  <tr>";
    							    if(json[j]['sc_yn'] == 'Y'){
           							    colorTxt += "style='color:blue;padding:4px 12px 8px 12px;'";
           							    stateTxt += "일정";
              						} else {
          								if(json[j]['gubun'] == '0' && json[j]['stats'] == 'on'){
          								    colorTxt += "style='color:#CD1AD4;padding:4px 12px 8px 12px;' ";
          								    stateTxt += "보고중";
          								} else if(json[j]['gubun'] == '0' && json[j]['stats'] == 'off'){
          								    colorTxt += "style='color:#8D8D8D;padding:4px 12px 8px 12px;' ";
          									stateTxt += "보고완료";
          								} else {
        									colorTxt += "style='padding:4px 12px 8px 12px;' ";
        									if(json[j]['dq_yn'] == 'Y') dqTxt = "checked";
          								    stateTxt += "보고대기(서면대체 <input type=\"checkbox\" name=\"dqYn\" value=\"Y\" onclick=\"fnDqChg('"+json[j]['repid']+"', this)\" "+dqTxt+" />)";
          								}
      								}
    							    tempHtml4 += "    <td "+colorTxt+">"+stateTxt+"</td>";
    							    tempHtml4 += "  </tr>";
                                    // tempHtml4 += "   <tr><td height=\"5\"></td></tr>";							    
    							    tempCnt4++;
    						    
        						    if(json[j]['stats'] == 'on'){
                    				    retHtml2 += "<table class=\"status_box\" width=\"100%\">";
                                        retHtml2 += "   <colgroup>";
                                        retHtml2 += "       <col width=\"20%\"/>";
                                        retHtml2 += "       <col width=\"*\"/>";
                                        retHtml2 += "   </colgroup>";
                                        retHtml2 += "   <tbody>";
                                        retHtml2 += "   <tr>";
                    	                retHtml2 += "       <td colspan=\"2\" class=\"right_header\"><img src=\"images/cont_p.png\"> &nbsp;&nbsp; 보고자 "+json[j]['bogo_name']+"("+json[j]['bogo_dprt_name']+")</td>";
                    	                retHtml2 += "   </tr>";
                    	                retHtml2 += "   <tr>";
                    	                retHtml2 += "       <td colspan=\"2\" class=\"right_title\" style=\"line-height:36px;\">"+json[j]['title']+"</td>";
                    	                retHtml2 += "   </tr>";
                    	                retHtml2 += "   <tr>";
                    	                retHtml2 += "       <th width=\"200\">시작시간 </th>";
                    	                retHtml2 += "       <td style='font-size:50px;'>"+json[j]['stats_date']+"</td>";
                    	                retHtml2 += "   </tr>";
                    	                retHtml2 += "   <tr>";
                    	                retHtml2 += "       <th height=\"100\">경과시간 </th>";
                    	                retHtml2 += "       <td style=\"height:200px;\"><div id='ingTime' style='font-size:90px;'></div></td>";
                    	                retHtml2 += "   </tr>";
                    	                retHtml2 += "   <tr id='overTr' style=\"display:none;\">";
                    	                retHtml2 += "       <th>초과시간 </th>";
                    	                retHtml2 += "       <td style=\"color:red;height:200px;\"><div style=\"position:relative;top:-45px;font-size:30px;\">보고 시간이 초과되었습니다.</div><div id='overTime' style='position:relative;top:12px;font-size:90px;'></div></td>";
                    	                retHtml2 += "   </tr>";
                    					retHtml2 += "   <tbody>";
                    					retHtml2 += "</table>";
                    					temp_count = json[j]['ing_time'];
                    					temp_over_count = json[j]['over_time'];
                    					
                    					onCnt++;
                    				}
                    			}
    					    }
    					    tempHtml4 += "</table>";
    					    if(tempCnt4 == 0){
    						    tempHtml4 += "&nbsp;";
    					    }
    				    }
    				}
    				retHtml += tempHtml4;
    				retHtml += "        </td>";
    				retHtml += "    </tr>";
    				tempCnt++;
    			}				
                retHtml += "  </tbody>";
    		    tempHtml += "</table>";

    		    if(onCnt == 0){
    			    retHtml3 += "<table width=\"100%\">";
    			    retHtml3 += "<tr><td style='text-align:center;height:700px;'><img src='images/daewooenc_logo.png' width='400'/></td></tr>";
    			    retHtml3 += "</table>";
    		    }

                $("#leftBox").html(retHtml);
                if(retHtml2 != ""){
    				if(exam_count == 0){
    					timeStop = 0;
    					exam_count = temp_count;
    					$('#overTr').hide();
    					fnTimeCount();
    						
    					$("#rightBox").html(retHtml2);
    				}

                    if(temp_over_count > 0 && over_count == 0){
    					//over_count = temp_over_count-60;
						  over_count = temp_over_count;
    				    $('#overTr').show();
                        fnOverTimeCount();
    				}
                } else {
    				timeStop = 1;
    				exam_count = 0;
					over_count = 0;
					temp = 1;
    				if($("#rightBox").html().indexOf('table') == -1){
    				  $("#rightBox").html(retHtml3);
    				}
                }
      		},
      		error : function (xhr, ajaxOptions,thrownError){
      	    }
        });
    }

    function fnDqChg(sRepid, obj){
        var chk = (obj.checked == true ? "Y" : "N");
        $.ajax({
    		async : true,
    		type:'POST',
    		url: 'ajaxProc.php',
    		data:{
    		    'procGubun':'dpChk',
                'sRepid': sRepid,
                'dqChk' : chk
    		},
    		dataType: "json",
    		success:function(json){
    		},
    		error : function (xhr, ajaxOptions,thrownError){
    		}
    	});
    }
    
    function fnTimeCount(){
    	hh = parseInt(exam_count / 60);
    	strhh = hh;
    	mm = exam_count - (hh * 60);
    	strmm = "0" + mm;				
    	strmm = strmm.substring(strmm.length -2 ,strmm.length);

    	if(timeStop != 1){
    		$('#ingTime').html(strhh + "분" + strmm + "초");
    	}
    	if(timeStop == 0){
    		setTimeout("fnTimeCount()",1002); 		
    		exam_count++;
    	}
    }
	
	function fnOverTimeCount(){
		if(temp > 0 && timeStop != 1) Pop();
		temp = -1;
	 	hh = parseInt(over_count / 60);
    	strhh = hh;
    	mm = over_count - (hh * 60);
    	strmm = "0" + mm;		
    	strmm = strmm.substring(strmm.length -2 ,strmm.length);
    	
    	if(timeStop != 1){
    		$('#overTime').html(strhh + "분" + strmm + "초");
    	}
    	if(timeStop == 0){
    		setTimeout("fnOverTimeCount()",1002); 		
    		over_count++;
    	} 
	}

	function Pop(){
		var x = (window.screen.width / 2) - (400 / 2);
		var y = (window.screen.height / 2) - (100 / 2);
		window.open("Pop.html","_blank","width=470, height=170, left="+x+",top="+y);
}

	</script>
</head>

<body onload="fnInit();">
<form name="frm" method="post">
<input type="hidden" name="dateInclease" id="dateInclease" value="<?=$dateInclease;?>" />
<input type="hidden" name="write_date" id="write_date" value="<?=$sWrite_date;?>" />
<input type="hidden" name="rep_empno" id="rep_empno" value="<?=$rep_empno;?>" />
<input type="hidden" name="sEmpNo" value="<?=$sEmpno?>">
</form>
<table width="100%" height="90px" style="margin-bottom:5px;border:1px solid #e2e2e2;background-color:#0a2554;">
    <tr>
        <td>
            <table width="1800px" height="87px" align="left" style="background:url(images/main_title_bg.gif) no-repeat;">
                <tr>
					<td width="50px"></td>
                    <td width="302px" align="left">
						<!-- <a href="http://scal.daewooenc.com/smart2/"><img src="images/daewooenc_symbol.png" width="40"/>&nbsp;&nbsp;<img src="images/title_left.png"/></a> -->
						<div class="rotation_parent">
							<a href="http://scal.daewooenc.com/smart2/">
								<div class="rotation">
									<img src="images/daewooenc_symbol.png" width="40" />
								</div>
								<img src="images/title_left.png">
								</a>
					</div>
					
                    </td>
                    <td align="right"><div class="date" style="color:#ffffff;font-size:25px;"><?=substr($sWrite_date,0,4);?>년 <?=substr($sWrite_date,5,2);?>월 <?=substr($sWrite_date,8,2);?>일&nbsp;(<?=$sWrite_day;?>)</div></td>
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
<table width="100%">
    <tr>
        <td>
			<table width="100%" align="center">
			    <tr>
			        <td width="1200" align="right">
			            <table width="1150" height="40" align="right">
                            <tr>
                                <td width="25%" align="left"><a href="#" onclick="manaSche('');" ><span class="bt_main">일정보기</span></a>&nbsp;&nbsp;<a href="#" onclick="manaSearch('');" ><span class="bt_main">보고검색</span></a></td>
                                <td class="date_navi" align="center">
									<span style="border:0px solid #000;font-size:20px; font-weight:800;vertical-align: middle; ">&nbsp; <?=$sWrite_date;?> (<?=$sWrite_day;?>)&nbsp;</span>
									<img src="images/prev.gif" height="29px" border="0" onclick="fnGo('minus');" style="cursor:pointer; vertical-align:middle;"/>
									<span style="border:1px solid #9c9c9c; padding:3px 8px; font-size:16px; font-weight:550; vertical-align:middle;"><a href="#" onclick="fnGoToday();" >오늘</a></span>
									<img src="images/next.gif" height="29px" onclick="fnGo('plus');" style="cursor:pointer;vertical-align:middle;"/>&nbsp;&nbsp;
								</td>
                                <td  width="25%">
                                    <div style="padding-top:5px;padding-left:2px;text-align:right;"><?=$aPstnName?>님 보고일정</div>
                                </td>
                            </tr>
                        </table>
			            <div id="leftBox" style="width:1150px"></div>
			        </td>
					<td width="20"></td>
			        <td align="left" valign="top">
			            <div id="rightBox" style="width:620px"></div>
			        </td>
			    </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>