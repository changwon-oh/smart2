<?
include "./include/env.inc.php";
include "./include/function.inc.php";

$nFileid = getRequestString("file");
$nWbid = getRequestString("wbid");
$nSeq = getRequestString("seq");

if ($nFileid != "" && $nWbid != "" && $nSeq != "") {
	include "./include/oci8.inc.php";
	
	$sTable_name = "KOSPOWB_FILE";
	$qry_file = "SELECT * FROM $sTable_name WHERE FILEID=$nFileid AND GUBUN='B'";
	$oci -> parseExec($qry_file);
	$nFileIdx = 0;
	while($col = $oci -> fetchInto()) {
		$sUploadFile = $col["UPLOAD_NAME"];
		$sSavedFile = $col["SAVED_NAME"];
	}

	$oci -> parseFree();
	$oci -> disconnect();

	if(eregi("(MSIE 5.0|MSIE 5.1|MSIE 5.5|MSIE 6.0|MSIE 7.0|MSIE 8.0)", $_SERVER["HTTP_USER_AGENT"])) {
		Header("Content-Type: application/octet-stream");
	} else {
		Header("Content-type: file/unknown");
	}
	// header("Pragma: public");
	header("Pragma: no-cache"); 
	header("Expires: 0");
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	header("Content-Transfer-Encoding: binary");
	header("Content-Description: DAEWOOENC DOWNLOAD");

	$sFile = $_UPLOAD_PATH.$sSavedFile; // 절대경로
	if(is_file("$sFile")) { 
		$file_size = filesize("$sFile");
		header("Content-Length: $file_size");
		// header("Content-Disposition: attachment; filename=$sUploadFile");
		Header('Content-Disposition: attachment; filename='.iconv('UTF-8','CP949',$sUploadFile));
		ob_clean();
		// flush();

		$fp = fopen("$sFile","r"); 
		if (!fpassthru($fp)){ 
			fpassthru($fp); 
		}
	}

} else {
	echo "필수정보가 누락 되었습니다.";
	exit;
}
?>