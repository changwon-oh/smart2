<?
/*////////////////////////////////////////////////////////////////////////////*/
/* Copyright (c) 2021 DWENC                                                   */
/*////////////////////////////////////////////////////////////////////////////*/
/*////////////////////////////////////////////////////////////////////////////*/
/*  1. 프로그램명    : commonDbClass.inc( Oracle DB 관련 )                     */
/*  2. 작 성 일 자     : 2021/04/05                                           */
/*  3. 작 성 자         : OCW                                                 */
/*  4. 특기 및 유의사항 : DB 관련 php API는 ORACLE 7기준으로 작성됨              */
/*////////////////////////////////////////////////////////////////////////////*/

class commonDb
{
    var $ociUSER;          // Oracle User id
    var $ociPASSWD;        // Oracle User Password
    var $ociSID;        // Oracle Server id
    var $conn;           // Oracle Db
    var $cursor;            // Oracle CURSOR
    var $numcols;           // count of rows
    var $autoCommit = true; // auto Commit
    var $debug = false;     // debugging

    function commonDb($OraUser='edu', $OraPass='edu', $OraSid='172.20.2.247:1521/DBDICM') // 생성자
    {
        $this->ociUSER     = $OraUser;
        $this->ociPASSWD   = $OraPass;
        $this->ociSID      = $OraSid;
    }

    function setInfo($OraUser, $OraPass)
    {
        $this->ociUSER     = $OraUser;
        $this->ociPASSWD   = $OraPass;
    }

    function dbConnect()    // Oracle Connect
    {
        $this->conn = oci_connect($this->ociUSER, $this->ociPASSWD, $this->ociSID,'UTF8');
        return $this->conn;
    }

    function disConnect()   // Oracle Disconnect
    {
        oci_close($this->conn);
    }

    function autoCommit($autoCommit = false)    // Auto Commit
    {
        $this->autoCommit = $autoCommit;
    }

    function Commit()       // Commit
    {
        oci_commit($this->conn);
    }

    function Rollback()     // Rollback
    {
        oci_rollback($this->conn);
    }

    function Query($query)      // Excute Query String...
    {
        $stmt = oci_parse($this->conn, $query);
        oci_execute($stmt);
        oci_free_statement($stmt);
    }

    function FetchRow($query)       // Fetch one row data
    {
        $stmt = oci_parse($this->conn, $query);
        oci_execute($stmt);
        $value = oci_fetch_assoc($stmt);
        oci_free_statement($stmt);
        return $value;
    }

    function FetchRows($query)      // Fetch multi row data
    {
        //$values[] = ""; // 5712todo
        $stmt = oci_parse($this->conn, $query);
        oci_execute($stmt);
        while($rows = oci_fetch_assoc($stmt)) { // 빈값도 리턴
            $values[]   = $rows;
        }
        oci_free_statement($stmt);
        return $values;
    }

    function SqlErrorMsg($errMsg, $url="JavaScript:history.back();", $target="")
    {
        printf("<HTML>\n");
        printf("<HEAD>\n");
        printf("<TITLE>Error Page</TITLE>\n");
        printf("<META HTTP-EQUIV=\"Content-type\" CONTENT=\"text/html; charset=euc-kr\">\n");
        printf("<STYLE TYPE=\"text/css\">\n");
        printf("    TD, TH  {font-size:9pt;font-family:±Œž²;line-height:180%%;color:#666666}\n");
        printf("    A:link      {font-family:gulim;color:#3F3F3F;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
        printf("    A:visited   {font-family:gulim;color:#3F3F3F;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
        printf("    A:active    {font-family:gulim;color:#3F3F3F;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
        printf("    A:hover     {font-family:gulim;color:#FF0000;font-size:9pt;text-decoration:none;line-height:180%%;}\n");
        printf("</STYLE>\n");
        printf("<style type=\"TEXT/CSS\">BODY\n");
        printf("{\n");
        printf("    scrollbar-face-color:#F0F0F0;\n");
        printf("    scrollbar-shadow-color:gray;\n");
        printf("    scrollbar-highlight-color:#ffffff;\n");
        printf("    scrollbar-3dlight-color:#F0F0F0;\n");
        printf("    scrollbar-darkshadow-color:#ffffff;\n");
        printf("    scrollbar-track-color:#F0F0F0;\n");
        printf("    scrollbar-arrow-color:gray\n");
        printf("}\n");
        printf("</style>\n");
        printf("</HEAD>\n");
        printf("<BODY TOPMARGIN=0 LEFTMARGIN=0 BGCOLOR=\"#FFFFFF\">\n");
        printf("<table width=100%% border=0>\n");
        printf("    <td align=center>\n");
        printf("    <DIV STYLE=\"LINE-HEIGHT:35px\">&nbsp;</DIV>\n");
        printf("    <table border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\n");
        printf("        <td><img src=\"./image/error_top.gif\"></td><tr>\n");
        printf("        <td> \n");
        printf("        <table width=\"100%%\" border=\"0\" cellspacing=\"1\" cellpadding=\"0\" bgcolor=\"DADADA\">\n");
        printf("            <td bgcolor=\"#FFFFFF\"> \n");
        printf("            <table width=\"100%%\" border=\"0\" cellspacing=\"0\" cellpadding=\"10\">\n");
        printf("                <td><img src=\"./image/error_img.gif\"></td>\n");
        printf("                <td width=\"100%%\">\n");
        //printf("                    <FONT color=FF3B3B><B>%d</B></FONT><BR>\n", $errNo);
        printf("                    관리자에게 문의하여 주시기 바랍니다.( 021-5431~5 )\n");
        printf("                </td>\n");
        printf("                    </table>\n");
        printf("                </td>\n");
        printf("        </table>\n");
        printf("        </td>\n");
        printf("    <tr>\n");
        printf("        <td><img src=\"./image/error_top.gif\"></td>\n");
        printf("    <tr>\n");
        printf("        <td>&nbsp;</td>\n");
        printf("    <tr>\n");
        if($target) {
            printf("        <td align=\"center\"><A HREF=\"%s\" target=%s><img BORDER=0 src=\"./image/new_confirm.gif\"></A></td>\n"
, $url, $target);
        } else {
            printf("        <td align=\"center\"><A HREF=\"%s\"><img BORDER=0 src=\"./image/new_confirm.gif\"></A></td>\n", $url);
        }
        printf("    </table>\n");
        printf("    </td>\n");
        printf("</table>\n");
        printf("</BODY>\n");
        printf("</HTML>\n");
        exit;
    }
}
?>
