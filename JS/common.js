function MM_swapImgRestore() { //v3.0
  var i,x,a=document.MM_sr; for(i=0;a&&i<a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
}

function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}

function MM_findObj(n, d) { //v4.0
  var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}                        
  if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  if(!x && document.getElementById) x=document.getElementById(n); return x;
}

function MM_swapImage() { //v3.0
  var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i<(a.length-2);i+=3)
   if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
}

function FormQueryString(form) {
  if (typeof form != "object" || form.tagName != "FORM") {
    alert("FormQueryString함수의 인자는 FORM 태그가 아닙니다.");
    return "";
  }

	var name = new Array(form.elements.length);
	var value = new Array(form.elements.length);
	var j = 0;
	var plain_text="";

	//사용간,ㅇ한 컨트롤을 배열로 생성한다.
	len = form.elements.length;
	for (i = 0; i < len; i++) {
	  switch (form.elements[i].type) {
	    case "button":
	    case "reset":
	    case "submit":
	      break;
	    case "radio":
	    case "checkbox":
  				if (form.elements[i].checked == true) {
  					name[j] = form.elements[i].name;
  					value[j] = form.elements[i].value;
  					j++;
  				}
  				break;
  		case "select-one":
  				name[j] = form.elements[i].name;
					var ind = form.elements[i].selectedIndex;
					if(ind >= 0) {
						if (form.elements[i].options[ind].value != null)
							value[j] = form.elements[i].options[ind].value;
						else
							value[j] = form.elements[i].options[ind].text;
					} else {
						value[j] = "";
					}
					j++;
					break;
			case "select-multiple":
  				name[j] = form.elements[i].name;
					var llen = form.elements[i].length;
					var increased = 0;
					for( k = 0; k < llen; k++) {
						if (form.elements[i].options[k].selected) {
							name[j] = form.elements[i].name;
							if (form.elements[i].options[k].value != null)
								value[j] = form.elements[i].options[k].value;
							else
								value[j] = form.elements[i].options[k].text;
							j++;
							increased++;
						}
					}
					if(increased > 0) {
						j--;
					} else {
						value[j] = "";
					}
					j++;
					break;
				default :
  				name[j] = form.elements[i].name;
					value[j] = form.elements[i].value;
  				j++;
		}
	}

  //QueryString 을 조합한다.
	for (i = 0; i < j; i++) {
		 if (name[i] != '') plain_text += name[i]+ "=" + value[i] + "&";
	}

  //마지막에 &를 없애기 위함
  if (plain_text != "")
    plain_text = plain_text.substr(0, plain_text.length -1);
    return plain_text;
}

function inwonDetail(emp,name,title){
if (confirm("선택하시겠습니까?")){
	opener.document.getElementById("approvalName").innerHTML = title + " " +name;
	opener.document.frm.approvalEmpno.value = emp;
	self.close();
} else {
	alert("취소되었습니다.");
	return;	
	} 
}
function inwonDetail2(emp,name,title){
if (confirm("선택하시겠습니까?")){
	opener.document.getElementById("bogoName").innerHTML = title + " " +name;
	opener.document.frm.BogoEmpno.value = emp;
	opener.document.frm.testUserId.value = emp;
	self.close();
} else {
	alert("취소되었습니다.");
	return;	
	} 
}	
function inwonDetail3(emp,name,title){
	opener.document.getElementById("bogoName").innerHTML = title + " " +name;
	opener.document.frm.BogoEmpno.value = emp;
	opener.document.frm.testUserId.value = emp;
	self.close();
}

function viewDept(deptno,target){
	if(target == ""){
		opener.top.location.href = "./info_inwon.php?deptcode="+deptno;
	}
	else{
		window.open("./info_inwon.php?deptcode="+deptno,'inwonView','location=no,resizable=yes,status=no,scrollbars=yes,toolbar=no,width=920,height=600,top=0,left=0');
	}
}

function fnView(reqid){
	window.open("","wb_view","width=720,height=500,menubar=no,center=yes,scrollbars=no,help=no,status=no,resizable=no,top=100,left=500");
    var f = document.frm;	
	f.repid.value = reqid;
	f.action = "wb_view.php";
	f.target = "wb_view";
	f.submit();
	f.target = "";
}

function manaSche(){
    var f = document.frm;
	window.open("","mana_sche","width=950,height=850,menubar=no,center=yes,scrollbars=yes,help=no,status=no,resizable=no,top=100,left=500");
	f.action = "wb_sche_list.php";
	f.target = "mana_sche";
	f.submit();
	f.target = "";
}

function manaadd(){
    var f = document.frm;
	f.dateInclease.value = '';
	f.write_date.value = '';
	f.action = "wb_list.php";
	f.target = "_blank";
	f.submit();
	f.target = "";
}

function manaSearch(){
    var f = document.frm;
	window.open("","mana_search","width=1100,height=850,menubar=no,center=yes,scrollbars=yes,help=no,status=no,resizable=no,top=100,left=500");
	f.action = "wb_search.php";
	f.target = "mana_search";
	f.submit();
	f.target = "";
}

function fnTimeMod(reqid){
	window.open("","wb_time","width=720,height=320,menubar=no,center=yes,scrollbars=no,help=no,status=no,resizable=no,top=100,left=500");
    var f = document.frm;	
	f.repid.value = reqid;
	f.action = "wb_time_mod.php";
	f.target = "wb_time";
	f.submit();
	f.target = "";
}


function fnAwms(reqid){
	window.open("","wb_awms","width=710,height=430,menubar=no,center=yes,scrollbars=no,help=no,status=no,resizable=no,top=100,left=500");
    var f = document.frm;	
	f.repid.value = reqid;
	f.action = "wb_awms.php";
	f.target = "wb_awms";
	f.submit();
	f.target = "";
}

// 비서 화면에서 일정등록 팝업
function addManaSche(reqid){
    var f = document.frm;
	window.open("","wb_sche_add","width=720,height=320,menubar=no,center=yes,scrollbars=no,help=no,status=no,resizable=no,top=100,left=500");
	f.repid.value = reqid;
	f.action = "wb_sche_add.php";
	f.target = "wb_sche_add";
	f.submit();
	f.target = "";
}

