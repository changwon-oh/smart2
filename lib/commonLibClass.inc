<?
/*////////////////////////////////////////////////////////////////////////////*/
/* Copyright (c) 2019 KDN                                                     */
/*////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////*/
/*  1. 프로그램명    : commonLibClass.inc( 공통 lib 정의 )
/*  2. 작 성 일 자     : 2019/09/02                                          */
/*  3. 작 성 자         : sinzi76                                               */
/*  4. 특기 및 유의사항 : DB 관련 php API는 ORACLE 9기준으로 작성됨           */
/*////////////////////////////////////////////////////////////////////////////*/

function getSeedId()
{
    $userid = "11111113";
    return $userid;
}

function GetCookie($name) {
  	$value=isset($_COOKIE[$name]) ? $_COOKIE[$name] : "";

    if($name == "xkodfz" || $name == "ZW1wbm8"){
        if($value != ""){
            $value = base64_decode($value);
        }else{
            // ÀÎÁõÆäÀÌÁö·Î ÀÌµ¿
            //$url = "http://".$_SERVER['HTTP_HOST']."/auth_check.php";
            setcookie("PREV_PAGE",$_SERVER["REQUEST_URI"],"-1","/","kospo.co.kr"); // 메모리쿠키
            page_redirect2($url,"top");
        }
    }
   	return $value;
}

// 페이지 리다이렉션
function page_redirect ($url) {
	echo "<meta http-equiv=\"Refresh\" content=\"0;url=" . $url . "\">";
}

// 페이지 리다이렉션(자바스크립트 location.href)
function page_redirect2 ($url, $target) {
	if (isset($target) && $target != "") $sTarget = $target . ".";
	echo "<script language=\"JavaScript\">
<!--
${sTarget}location.href = \"${url}\";
//-->
</script>";
}

$user_empno = getSeedId();

function hanSubStr($text, $length, $suffix="")
{ 
	if (strlen($text) <= $length)
		return $text;

	$cpos = $length - 1;
	$count_2B = 0; 
	$lastchar = $text[$cpos]; 

	while (ord($lastchar)>127 && $cpos>=0) { 
		$count_2B++; 
		$cpos--; 
		$lastchar = $text[$cpos]; 
	} 

	return substr($text, 0, (($count_2B % 2) ? $length-1 : $length)).$suffix; 
} 

function doFilterSpecialChar($strSource)
{	
	$strSource = htmlspecialchars($strSource);
	$strSource = str_replace("<","&lt;",$strSource);
	$strSource = str_replace(">","&gt;",$strSource);
	$strSource = str_replace("\'","£§",$strSource);
	$strSource = str_replace("select","<c>s</c>elect",$strSource);
	$strSource = str_replace("SELECT","<c>S</c>ELECT",$strSource);
	$strSource = str_replace("delete","<c>d</c>elete",$strSource);
	$strSource = str_replace("DELETE","<c>D</c>ELETE",$strSource);
	$strSource = str_replace("update","<c>u</c>pdate",$strSource);
	$strSource = str_replace("UPDATE","<c>U</c>PDATE",$strSource);
	$strSource = str_replace("exec","<c>e</c>xec",$strSource);
	$strSource = str_replace("EXEC","<c>E</c>XEC",$strSource);
	$strSource = str_replace("drop","<c>d</c>rop",$strSource);
	$strSource = str_replace("DROP","<c>D</c>ROP",$strSource);
	$strSource = str_replace("or","<c>o</c>r",$strSource);
	$strSource = str_replace("OR","<c>O</c>R",$strSource);
	$strSource = str_replace("and","<c>a</c>nd",$strSource);
	$strSource = str_replace("AND","<c>A</c>ND",$strSource);
	
	return $strSource;		
}

/*****************************************************************/
/* 기 능 : 인증되지 않은 사용자 접근시 메세지							                   */
/* 등록일 : 2019/09/02                                           */
/* 작성자 : Rogue                                                */
/*****************************************************************/
function NotLogin($quit=1)
{
	printf("<HTML>\n");
	printf("<HEAD>\n");
	printf("<TITLE>Not Login</TITLE>\n");
	printf("<META HTTP-EQUIV=\"Content-type\" CONTENT=\"text/html; charset=euc-kr\">\n");
	printf("<STYLE TYPE=\"text/css\">\n");
	printf("	TD, TH	{font-size:9pt;font-family:굴림;line-height:180%%;color:#666666;}\n");
	printf("	A:link    	{font-family:gulim;color:#3F3F3F;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
	printf("	A:visited 	{font-family:gulim;color:#3F3F3F;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
	printf("	A:active  	{font-family:gulim;color:#3F3F3F;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
	printf("	A:hover   	{font-family:gulim;color:#FF0000;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
	printf("	INPUT		{FONT:12PX 굴림체;BACKGROUND-COLOR:#F9F9F9;COLOR:#000000;BORDER:1PX SOLID #A7A7A7;}\n");
	printf("	SELECT		{FONT:12PX 굴림체;BACKGROUND-COLOR:#F9F9F9;COLOR:#000000;BORDER:1PX SOLID #A7A7A7;HEIGHT:18PX;}\n");
	printf("	</STYLE>\n");
	printf("	<style type=\"TEXT/CSS\">BODY\n");
	printf("	{\n");
	printf("		scrollbar-face-color:#F0F0F0;\n");
	printf("		scrollbar-shadow-color:gray;\n");
	printf("		scrollbar-highlight-color:#ffffff;\n");
	printf("		scrollbar-3dlight-color:#F0F0F0;\n");
	printf("		scrollbar-darkshadow-color:#ffffff;\n");
	printf("		scrollbar-track-color:#F0F0F0;\n");
	printf("		scrollbar-arrow-color:gray\n");
	printf("	}\n");
	printf("	</style>\n");
	printf("</HEAD>\n");
	printf("<BODY TOPMARGIN=0 LEFTMARGIN=0 BGCOLOR=\"#FFFFFF\">\n");
	printf("<CENTER>\n");
	printf("<table width=589 border=0><td align=center>\n");
	printf("<DIV STYLE=\"LINE-HEIGHT:35px\">&nbsp;</DIV>\n");
	printf("	<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
	printf("  		<td><img src=\"./IMAGE/error_top.gif\"></td><tr>\n");
	printf("		<td> \n");
	printf("			<table width=\"100%%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"#DADADA\">\n");
	printf("				<td bgcolor=\"#FFFFFF\"> \n");
	printf("					<table width=\"100%%\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\">\n");
	printf("						<td><img src=\"./IMAGE/error_img.gif\"></td>\n");
	printf("						<td width=\"100%%\">정상적으로 Login하지 않거나<BR>Login정보가 변경되었습니다.<BR>PowerNet에 Login한 후 사용해 주시기 바랍니다.\n");
	printf("						</td>\n");
	printf("              		</table>\n");
	printf("          		</td>\n");
	printf("			</table>\n");
	printf("		</td><tr>\n");
	printf("		<td><img src=\"./IMAGE/error_top.gif\"></td><tr>\n");
	printf("		<td>&nbsp;</td><tr>\n");
	printf("		<td align=\"center\"><A HREF=\"JavaScript:history.back();\"><IMG BORDER=0 src=\"./IMAGE/new_confirm.gif\"></A></td>\n");
	printf("	</table>\n");
	printf("	</TD>\n");
	printf("</table>\n");
	printf("</CENTER>\n");
	printf("</BODY>\n");
	printf("</HTML>\n");
	if($quit==1)
		exit;
}

/*****************************************************************/
/* 기 능: 일반 MESSAGE                                         */
/* 등록일 : 2019/09/02                                           */
/* 작성자 : Rogue                                                */
/*****************************************************************/
function PrintMessage($message, $url="JavaScript:history.back();", $target="")
{
	printf("<HTML>\n");
	printf("<HEAD>\n");
	printf("<TITLE></TITLE>\n");
	printf("<META HTTP-EQUIV=\"Content-type\" CONTENT=\"text/html; charset=euc-kr\">\n");
	printf("<STYLE TYPE=\"text/css\">\n");
	printf("	TD, TH	{font-size:9pt;font-family:굴림굴;line-height:180%%;color:#666666}\n");
	printf("	A:link    	{font-family:gulim;color:#3F3F3F;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
	printf("	A:visited 	{font-family:gulim;color:#3F3F3F;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
	printf("	A:active  	{font-family:gulim;color:#3F3F3F;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
	printf("	A:hover   	{font-family:gulim;color:#FF0000;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
	printf("</STYLE>\n");
	printf("<style type=\"TEXT/CSS\">BODY\n");
	printf("{\n");
	printf("	scrollbar-face-color:#F0F0F0;\n");
	printf("	scrollbar-shadow-color:gray;\n");
	printf("	scrollbar-highlight-color:#ffffff;\n");
	printf("	scrollbar-3dlight-color:#F0F0F0;\n");
	printf("	scrollbar-darkshadow-color:#ffffff;\n");
	printf("	scrollbar-track-color:#F0F0F0;\n");
	printf("	scrollbar-arrow-color:gray\n");
	printf("}\n");
	printf("</style>\n");
	printf("</HEAD>\n");
	printf("<BODY TOPMARGIN=0 LEFTMARGIN=0 BGCOLOR=\"#FFFFFF\">\n");
	printf("<table width=100%% border=0>\n");
	printf("	<td align=center>\n");
	printf("	<DIV STYLE=\"LINE-HEIGHT:35px\">&nbsp;</DIV>\n");
	printf("	<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
	printf("  		<td><img src=\"./IMAGE/error_top.gif\"></td><tr>\n");
	printf("		<td> \n");
	printf("		<table width=\"100%%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"DADADA\">\n");
	printf("			<td bgcolor=\"#FFFFFF\"> \n");
	printf("			<table width=\"100%%\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\">\n");
	printf("				<td><img src=\"./IMAGE/error_img.gif\"></td>\n");
	printf("				<td width=\"100%%\">%s</td>\n", $message);
	printf("              		</table>\n");
	printf("          		</td>\n");
	printf("		</table>\n");
	printf("		</td>\n");
	printf("	<tr>\n");
	printf("		<td><img src=\"./IMAGE/error_top.gif\"></td>\n");
	printf("	<tr>\n");
	printf("		<td>&nbsp;</td>\n");
	printf("	<tr>\n");
	if($target) {
		printf("		<td align=\"center\"><A HREF=\"%s\" target=%s><img BORDER=0 src=\"./IMAGE/new_confirm.gif\"></A></td>\n", $url, $target);
	} else {
		printf("		<td align=\"center\"><A HREF=\"%s\"><img BORDER=0 src=\"./IMAGE/new_confirm.gif\"></A></td>\n", $url);
	}
	printf("	</table>\n");
	printf("	</td>\n");
	printf("</table>\n");
	printf("</BODY>\n");
	printf("</HTML>\n");
}

/*****************************************************************/
/* 기 능 : Sql Error MESSAGE                                    */
/* 등록일 : 2019/09/02                                           */
/* 작성자 : Rogue                                                */
/*****************************************************************/
function SqlErrorMsg($errNo, $errMsg, $url="JavaScript:history.back();", $target="")
{
	printf("<HTML>\n");
	printf("<HEAD>\n");
	printf("<TITLE>Error Page</TITLE>\n");
	printf("<META HTTP-EQUIV=\"Content-type\" CONTENT=\"text/html; charset=euc-kr\">\n");
	printf("<STYLE TYPE=\"text/css\">\n");
	printf("	TD, TH	{font-size:9pt;font-family:굴림;line-height:180%%;color:#666666}\n");
	printf("	A:link    	{font-family:gulim;color:#3F3F3F;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
	printf("	A:visited 	{font-family:gulim;color:#3F3F3F;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
	printf("	A:active  	{font-family:gulim;color:#3F3F3F;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
	printf("	A:hover   	{font-family:gulim;color:#FF0000;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
	printf("</STYLE>\n");
	printf("<style type=\"TEXT/CSS\">BODY\n");
	printf("{\n");
	printf("	scrollbar-face-color:#F0F0F0;\n");
	printf("	scrollbar-shadow-color:gray;\n");
	printf("	scrollbar-highlight-color:#ffffff;\n");
	printf("	scrollbar-3dlight-color:#F0F0F0;\n");
	printf("	scrollbar-darkshadow-color:#ffffff;\n");
	printf("	scrollbar-track-color:#F0F0F0;\n");
	printf("	scrollbar-arrow-color:gray\n");
	printf("}\n");
	printf("</style>\n");
	printf("</HEAD>\n");
	printf("<BODY TOPMARGIN=0 LEFTMARGIN=0 BGCOLOR=\"#FFFFFF\">\n");
	printf("<table width=100%% border=0>\n");
	printf("	<td align=center>\n");
	printf("	<DIV STYLE=\"LINE-HEIGHT:35px\">&nbsp;</DIV>\n");
	printf("	<table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
	printf("  		<td><img src=\"./IMAGE/error_top.gif\"></td><tr>\n");
	printf("		<td> \n");
	printf("		<table width=\"100%%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"DADADA\">\n");
	printf("			<td bgcolor=\"#FFFFFF\"> \n");
	printf("			<table width=\"100%%\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\">\n");
	printf("				<td><img src=\"./IMAGE/error_img.gif\"></td>\n");
	printf("				<td width=\"100%%\">\n");
	printf("					<FONT color=FF3B3B><B>%d</B></FONT><BR>\n", $errNo);
	printf("					관리자에게 문의하여 주시기 바랍니다.(021-5431~5 )\n");
	printf("				</td>\n");
	printf("              		</table>\n");
	printf("          		</td>\n");
	printf("		</table>\n");
	printf("		</td>\n");
	printf("	<tr>\n");
	printf("		<td><img src=\"./IMAGE/error_top.gif\"></td>\n");
	printf("	<tr>\n");
	printf("		<td>&nbsp;</td>\n");
	printf("	<tr>\n");
	if($target) {
		printf("		<td align=\"center\"><A HREF=\"%s\" target=%s><img BORDER=0 src=\"./IMAGE/new_confirm.gif\"></A></td>\n", $url, $target);
	} else {
		printf("		<td align=\"center\"><A HREF=\"%s\"><img BORDER=0 src=\"./IMAGE/new_confirm.gif\"></A></td>\n", $url);
	}
	printf("	</table>\n");
	printf("	</td>\n");
	printf("</table>\n");
	printf("</BODY>\n");
	printf("</HTML>\n");
	exit;
}


?>
