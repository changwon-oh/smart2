<?
/*#########################################
# 시스템명 : 스마트보고 시스템		                  #
# 작 성 일 : 2021.04.05                    #
# 파 일 명 : wb_admin_manage.php             #
# 기능설명 : 스마트보고 관리자 목록 		          #
#########################################*/
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<META http-equiv="Expires" content="-1">
<META http-equiv="Pragma" content="no-cache">
<META http-equiv="Cache-Control" content="No-Cache">
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
<title>스마트 보고관리(관리자)</title>
<script language="javascript">
    function addRow(metald, tempateType){
        var tbld = metald+'_tb';
        var trld = tbld+'_tr';
        var lblRows = $('#'+tbld+' > tbody > tr:[id^='+trld+']').size();
        var lastSubRows = $('#'+tbld+' > tbody > tr:[id^='+trld+']:last').attr('id');
        var rows = 0;
        
        if(lastSubRows != null && lastSubRows != ''){
            rows = lblRows;
            //rows = eval($('#'+tbld+' > tbody > tr:[id^='+trld+']:last').attr('id').replace(new RegExp(trld+'_','gi'), ''))+1;
        } else {
            rows = 0;
            $('#'+tbld+' > tbody > tr').remove();
        }
    
        var newRowld = trld +'_'+rows;
        var newColumld1 = newRowld+'_td_1';
        var newColumld2 = newRowld+'_td_2';
        var newColumld3 = newRowld+'_td_3';
        var newColumld4 = newRowld+'_td_4';
        var newColumld5 = newRowld+'_td_5';
        var newColumld6 = newRowld+'_td_6';
        var newColumld7 = newRowld+'_td_7';
        
        
        if(rows == 0)
            $('#'+tbld+' > tbody').append('<tr id="'+newRowld+'"></tr>');
        else
            $('#'+tbld+' > tbody > tr:last').after('<tr id="'+newRowld+'"></tr>');
        
        if(tempateType == '1'){
            var tdtxt = "";
            for(var k=0; k < 7; k++){
                var classTxt = "td_manage";
                if(k == 6)
                    classTxt = "td_manage_last";
                tdtxt += '<td id="' +newRowld+'_td_'+(k+1)+'" class="'+classTxt+'"></td>';
            }
            $('#'+newRowld).append(tdtxt);
            // $('#'+newRowld+' > td:[id='+newColumld1+']').append((rows+1));
            // $('#'+newRowld+' > td:[id='+newColumld2+']').append('&nbsp;<input type="text" name="emp_name[]" id="rep_name'+rows+'" size="20" value="" /><input type="text" name="rep_no[]" id="rep_no'+rows+'" value="" /><input type="text" name="rep_dept[]" id="rep_dept'+rows+'" value="" />&nbsp;<img src="./images/bt16.gif" border="0" style="cursor:pointer;" onClick="selMng(\'rep\','+rows+',\'rep_dept'+rows+'\');" />&nbsp;<a href="#" style="color:red;" onclick="fnX(\'rep\','+rows+');">X</a>');
            // $('#'+newRowld+' > td:[id='+newColumld3+']').append('&nbsp;<input type="text" name="man1_name[]" id="man1_name'+rows+'" size="20" value="" /><input type="text" name="man1_no[]" id="man1_no'+rows+'" value="" />&nbsp;<img src="./images/bt16.gif" border="0" style="cursor:pointer;" onClick="selMng(\'man1\','+rows+',\'rep_dept'+rows+'\');" />&nbsp;<a href="#" style="color:red;" onclick="fnX(\'man1\','+rows+');">X</a>');
            // $('#'+newRowld+' > td:[id='+newColumld4+']').append('&nbsp;<input type="text" name="man2_name[]" id="man2_name'+rows+'" size="20" value="" /><input type="text" name="man2_no[]" id="man2_no'+rows+'" value="" />&nbsp;<img src="./images/bt16.gif" border="0" style="cursor:pointer;" onClick="selMng(\'man2\','+rows+',\'rep_dept'+rows+'\');" />&nbsp;<a href="#" style="color:red;" onclick="fnX(\'man2\','+rows+');">X</a>');
            // //$('#'+newRowld+' > td:[id='+newColumld5+']').append('&nbsp;<input type="text" name="main_name[]" id="main_name'+rows+'" size="20" value="" /><input type="text" name="main_no[]" id="main_no'+rows+'" value="" />&nbsp;<img src="./images/bt16.gif" border="0" style="cursor:pointer;" onClick="selMng(\'main\','+rows+',\'rep_dept'+rows+'\');" />');
            // $('#'+newRowld+' > td:[id='+newColumld5+']').append('&nbsp;<input type="text" name="main_no[]" id="main_no'+rows+'" size="10" value=""  maxlength="8" />&nbsp;<a href="#" style="color:red;" onclick="fnX(\'main\','+rows+');">X</a>');
            // $('#'+newRowld+' > td:[id='+newColumld6+']').append('<select name="useYn[]" id="useYn"><option value="Y">사용</option><option value="N">미사용</option><option value="P">열람자</option></select>');
            // $('#'+newRowld+' > td:[id='+newColumld7+']').append('<img src="./images/bt06.gif" border="0" style="cursor:pointer;" onClick="removeRow(\'tbl\','+rows+',1);">');        

            $('#'+newRowld+' > td:[id='+newColumld1+']').append((rows+1));
            $('#'+newRowld+' > td:[id='+newColumld2+']').append('<input type="hidden" class="" name="rep_no[]" id="rep_no'+rows+'" value="" /><input type="text" class="" name="emp_name[]" id="rep_name'+rows+'" size="22" value="" onClick="selMng(\'rep\','+rows+',\'rep_dept'+rows+'\');"/>&nbsp;<span class="bt_confirm" onClick="selMng(\'rep\','+rows+',\'rep_dept'+rows+'\');">선택</span>');
            $('#'+newRowld+' > td:[id='+newColumld3+']').append('<input type="hidden" class="" name="man1_no[]" id="man1_no'+rows+'" value="" /><input type="text" class="" name="man1_name[]" id="man1_name'+rows+'" size="22" value="" onClick="selMng(\'man1\','+rows+',\'rep_dept'+rows+'\');" />&nbsp;<span class="bt_confirm" onClick="selMng(\'man1\','+rows+',\'rep_dept'+rows+'\');">선택</span>');
            $('#'+newRowld+' > td:[id='+newColumld4+']').append('<input type="hidden" class="" name="man2_no[]" id="man2_no'+rows+'" value="" /><input type="text" class="" name="man2_name[]" id="man2_name'+rows+'" size="22" value="" onClick="selMng(\'man2\','+rows+',\'rep_dept'+rows+'\');" />&nbsp;<span class="bt_confirm" onClick="selMng(\'man2\','+rows+',\'rep_dept'+rows+'\');">선택</span>');

            $('#'+newRowld+' > td:[id='+newColumld5+']').append('<input type="text" name="main_no[]" id="main_no'+rows+'" size="8" value=""  maxlength="8" />');
            $('#'+newRowld+' > td:[id='+newColumld6+']').append('<select name="useYn[]" id="useYn"><option value="Y">사용</option><option value="N">미사용</option><option value="P">열람자</option></select>');
            $('#'+newRowld+' > td:[id='+newColumld7+']').append('<span class="bt_confirm" onClick="removeRow(\'tbl\','+rows+',1);">삭제</span>');        

        }
    }
    
    function removeRow(metald, rowld, gubun){
        var tbld = metald+'_tb';
        var subCount = 0;
        subCount = $('#'+tbld+' > tbody > tr:[id^='+rowld+'_sub_]').size();
        if(gubun == 0){
            if(confirm('삭제하시겠습니까?')){
                $('#'+tbld+'_tr_'+rowld).remove();
                fnSaveData();
            }
        } else {
            $('#'+tbld+'_tr_'+rowld).remove();
        }
    }
    
    function fnX(id,seq){
        if(document.getElementById(id+'_name'+seq) != null)
            document.getElementById(id+'_name'+seq).value = "";
        document.getElementById(id+'_no'+seq).value = "";
        if(document.getElementById(id+'_dept'+seq) != null)
            document.getElementById(id+'_dept'+seq).value = "";    
    }
        
    function selMng(pid,pseq){
    	var frm = document.forms[0];
    	// window.open('./info_inwon_mng.php', 'insa', 'width=650,height=500,menubar=no,center=yes,scrollbars=yes,help=no,status=no,resizable=yes,top=100,left=500');
        // window.open('./info_inwon_mng.php?deptcode='+(pdept == "" ? "0000" : document.getElementById(pdept).value)+'&pid='+pid+'&pseq='+pseq, 'insa', 'width=650,height=500,menubar=no,center=yes,scrollbars=yes,help=no,status=no,resizable=yes,top=100,left=500');
        window.open('./info_inwon_mng.php?pid='+pid+'&pseq='+pseq, 'insa', 'width=650,height=500,menubar=no,center=yes,scrollbars=yes,help=no,status=no,resizable=yes,top=100,left=500');

    }
    	
    function fnInit(){
    	fnSetData();
    	// setInterval(fnSetData, 3000);
    }
    
    function fnSaveData(){
        var paramSub = FormQueryString(document.frm);
    	$.ajax({
    		async : true,
    		type:'POST',
    		url: 'ajaxProc.php',
    		data: paramSub,
    		dataType: "json",
    		success:function(json){
    		    alert('저장되었습니다.');
    		    fnSetData();
    		},
        	error : function (xhr, ajaxOptions,thrownError){
    		    //alert('검색 대상자가 존재하지 않습니다.');
    		    alert(thrownError+':message=='+xhr.responseText);
    	    }
        });
    }
    
    function fnSetData(){
    	$.ajax({
    		async : true,
    		type:'POST',
    		url: 'ajaxManage.php',
    		//data: paramSub,
    		data:{
    		
    		},
    		dataType: "json",
    		success:function(json){
    // 		  alert(json);
                var tid = 'tbl_tb';
                var retHtml = "";
                if(json != null){
                    if(json.length > 0){
        			    for(var j=0; j < json.length; j++){
        			        var trid = tid+'_tr_'+j;
        			        var tdId = trid + '_td_';
        			        var sel1 = "";
        			        var sel2 = "";
							var sel3 = "";
        			         if(json[j]['use_yn'] == 'Y'){
        			            sel1 = "selected";
							} else if(json[j]['use_yn'] == 'P'){
        			            sel3 = "selected";
        			        } else {
        			            sel2 = "selected";
        			        }
                            /*
                            retHtml += '  <tr id="'+trid+'">';
                            retHtml += '      <td class="td_manage" id="'+tdId+'1">'+(j+1)+'</td>';
                            retHtml += '      <td class="td_manage" id="'+tdId+'2">&nbsp;<input type="text" name="emp_name[]" id="rep_name'+j+'" size="20" value="'+json[j]['title']+' '+json[j]['bogo_name']+'" readonly />';
                            retHtml += '                                                <input type="text" name="rep_no[]" id="rep_no'+j+'" value="'+json[j]['bogo_empno']+'" />';
                            retHtml += '                                                <input type="text" name="rep_dept[]" id="rep_dept'+j+'" value="'+json[j]['bogo_deptno']+'" />&nbsp;';
                            retHtml += '                                                <img src="./images/bt16.gif" border="0" style="cursor:pointer;" onClick="selMng(\'rep\','+j+',\'rep_dept'+j+'\');" />&nbsp;';
                            retHtml += '      <td class="td_manage" id="'+tdId+'3">&nbsp;<input type="text" name="man1_name[]" id="man1_name'+j+'" size="20" value="'+json[j]['manager_name1']+'" readonly />';
                            retHtml += '                                                <input type="hidden" name="man1_no[]" id="man1_no'+j+'" value="'+json[j]['manager_empno1']+'" />&nbsp;';
                            retHtml += '                                                <img src="./images/bt16.gif" border="0" style="cursor:pointer;" onClick="selMng(\'man1\','+j+',\'rep_dept'+j+'\');" />&nbsp;';
                            retHtml += '                                                <a href="#" style="color:red;" onclick="fnX(\'man1\','+j+');">X</a></td>';
                			retHtml += '      <td class="td_manage" id="'+tdId+'4">&nbsp;<input type="text" name="man2_name[]" id="man2_name'+j+'" size="20" value="'+json[j]['manager_name2']+'" readonly />';
                            retHtml += '                                                <input type="hidden" name="man2_no[]" id="man2_no'+j+'" value="'+json[j]['manager_empno2']+'" />&nbsp;';
                            retHtml += '                                                <img src="./images/bt16.gif" border="0" style="cursor:pointer;" onClick="selMng(\'man2\','+j+',\'rep_dept'+j+'\');" />&nbsp;';
                            retHtml += '                                                <a href="#" style="color:red;" onclick="fnX(\'man2\','+j+');">X</a></td>';
                			retHtml += '      <td class="td_manage" id="'+tdId+'5">&nbsp;<input type="text" name="main_no[]" id="main_no'+j+'" size="10" value="'+json[j]['main_empno']+'" maxlength="8" />&nbsp;';
                            retHtml += '                                                <a href="#" style="color:red;" onclick="fnX(\'main\','+j+');">X</a></td>';
                            retHtml += '      <td class="td_manage" id="'+tdId+'6"><select name="useYn[]" id="useYn"><option value="Y" '+sel1+'>사용</option><option value="N" '+sel2+'>미사용</option><option value="P" '+sel3+'>열람자</option></select></td>';
                            retHtml += '      <td class="td_manage_last" id="'+tdId+'7"><img src="./images/bt06.gif" border="0" style="cursor:pointer;" onClick="removeRow(\'tbl\','+j+',0);"></td>';
                            retHtml += '  </tr>';
                            */
                            retHtml += '  <tr id="'+trid+'">';
                            // 순번
                            retHtml += '      <td class="td_manage" id="'+tdId+'1">'+(j+1)+'</td>';
                            // 보고대상
                            retHtml += '      <td class="td_manage" id="'+tdId+'2"><span onClick="selMng(\'rep\','+j+');">';
                            retHtml += '        <input type="text" class="td_manage_text" name="rep_no[]" id="rep_no'+j+'" size="4" value="'+json[j]['bogo_empno']+'" readonly />';
                            retHtml += '        <input type="text" class="td_manage_text" name="emp_name[]" id="rep_name'+j+'" size="25" value="'+json[j]['bogo_name']+'" readonly />';
                            // 비서1
                            retHtml += '      <td class="td_manage" id="'+tdId+'3"><span onClick="selMng(\'man1\','+j+');">';
                            retHtml += '        <input type="text" class="td_manage_text" name="man1_no[]" id="man1_no'+j+'" size="4" value="'+json[j]['manager_empno1']+'" readonly />';
                            retHtml += '        <input type="text" class="td_manage_text" name="man1_name[]" id="man1_name'+j+'" size="25" value="'+json[j]['manager_name1']+'" readonly /></span>';
                            retHtml += '        <a href="#" style="color:green;" onclick="fnX(\'man1\','+j+');">X</a>'
                            // 비서2
                            retHtml += '      <td class="td_manage" id="'+tdId+'3"><span onClick="selMng(\'man2\','+j+');">';
                            retHtml += '        <input type="text" class="td_manage_text" name="man2_no[]" id="man2_no'+j+'" size="4" value="'+json[j]['manager_empno2']+'" readonly />';
                            retHtml += '        <input type="text" class="td_manage_text" name="man2_name[]" id="man2_name'+j+'" size="25" value="'+json[j]['manager_name2']+'" readonly /></span>';
                            retHtml += '        <a href="#" style="color:green;" onclick="fnX(\'man2\','+j+');">X</a>'
                            // 보고화면관리 사번
                			retHtml += '      <td class="td_manage" id="'+tdId+'5">&nbsp;<input type="text" name="main_no[]" id="main_no'+j+'" size="8" value="'+json[j]['main_empno']+'" maxlength="8" />&nbsp;';
                            // 사용여부
                            retHtml += '      <td class="td_manage" id="'+tdId+'6"><select name="useYn[]" id="useYn"><option value="Y" '+sel1+'>사용</option><option value="N" '+sel2+'>미사용</option><option value="P" '+sel3+'>열람자</option></select></td>';
                            // 삭제
                            retHtml += '      <td class="td_manage_last" id="'+tdId+'7"><span class="bt_confirm" onClick="removeRow(\'tbl\','+j+',0);">삭제</td>';
                            // retHtml += "    <td class=\"td_status\"><span class=\"bt_confirm\" onClick=\"popup('"+hour+"');\">삭제</span>&nbsp;</td>";
                            retHtml += '  </tr>';

                        }
                    } else {
                        retHtml += '<tr><td align="center" height="35" colspan="7" class="td_manage_last"><b>데이터가 없습니다.</b></td></tr>';
                    }
                } else {
                    retHtml += '<tr><td align="center" height="35" colspan="7" class="td_manage_last"><b>데이터가 없습니다.</b></td></tr>';
                }
                $('#'+tid+' > tbody > tr').remove();
    		    $('#'+tid+' > tbody').append(retHtml);
    	    },
        	error : function (xhr, ajaxOptions,thrownError){
    		    //alert('검색 대상자가 존재하지 않습니다.');
    		    alert(thrownError+':message=='+xhr.responseText);
    	    }
        });
    }

</script>
<body onload="fnInit()">
<div id="tipdiv" style="display:none;position:absolute;"></div>
<table width="100%" height="90px" style="margin-bottom:30px;border:1px solid #e2e2e2;background-color:#0a2554;">
    <tr>
        <td>
            <table width="1300px" height="87px" align="center" style="background:url(images/title_bg.gif) no-repeat;">
                <tr>
                    <td width="302px" align="left">
                        <!-- <a href="http://scal.daewooenc.com/smart2/"><img src="images/title_left.png"/></a> -->
                        <div class="rotation_parent">
								<a href="http://scal.daewooenc.com/smart2/">
									<div class="rotation">
										<img src="images/daewooenc_symbol.png" width="40" />
									</div>
									<img src="images/title_left.png">
								</a>
							</div>
                    </td>
                    <td align="right"></td>
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
<form name="frm" method="post">
<input type="hidden" name="procGubun" value="manage" />
<table width="100%" >
    <tr>
        <td>
            <table width="1300" align="center" style="margin-bottom:4px;">
                <tr>
                    <td align="right"><div style="float:right;padding:2px 10px;border:1px solid #000;background-color:lavender;"><a href="#" onclick="addRow('tbl',1);" >추가</a></div><br></td>
                </tr>
            </table>
            <table width="1300" align="center" id="tbl_tb">
            <colgroup>
                <col width="3%"/>
                <col width="26%"/>
                <col width="26%"/>
                <col width="26%"/>
                <col width="9%"/>
                <col width="6%"/>                
                <col width="4%"/>
            </colgroup>
            <thead>
                <tr class="table_header">
                    <td height="40" >순번</td>
                    <td>보고대상</td>
                    <td>비서1</td>
                    <td>비서2</td>
                    <td>보고화면관리</td>
                    <td>사용여부</td>
                    <td>&nbsp;</td>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td align="center" height="35" colspan="7" class="td_manage_last"><b>데이터가 없습니다.</b></td>
                </tr>
            </tbody>
            </table>
            <table width="1300px" align="center" style="margin-top:4px;">
		        <tr>
			        <td align="right"><br><div style="float:right;padding:2px 11px;border:1px solid #000;background-color:lavender;"><a href="#" onclick="fnSaveData()">저장</a></div></td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</form>
</body>
</html>
