<?
/*########################################################
#                                                        #
#  프로그램명 : function.inc.php                         	 #
#                                                        #
#  기능설명 : 각종 처리 함수                          		     #
#                                                        #
########################################################*/

// 페이지 리다이렉션
function page_redirect ($url) {
	echo "<meta http-equiv=\"Refresh\" content=\"0;url=" . $url . "\">";
}

// 페이지 리다이렉션 (자바스크립트 location.href)
function page_redirect2 ($url, $target) {
	if (isset($target) && $target != "") $sTarget = $target . ".";

	echo "<script language=\"javascript\" type=\"text/javascript\">\n";
	echo "<!--\n";
	echo "${sTarget}location.href = \"${url}\";\n";
	echo "//-->\n";
	echo "</script>\n";
}

// 메시지 출력 후 페이지 리다이렉션
function alertMsg_move($text, $url, $target) {
	if ($target != "") $sTarget = $target . ".";
	
	echo "<script language=\"javascript\" type=\"text/javascript\">\n";
	echo "<!--\n";
	echo "alert (\"$text\");\n";
	echo $sTarget."location.href = \"".$url."\";";
	echo "//-->\n";
	echo "</script>\n";
}

// 따옴표 처리처
function charReplace($text) {
	$text=str_replace("\"","£¢",$text);
	$text=str_replace("\'","£§",$text);
	$text=str_replace("=","£œ",$text);
	$text=str_replace("-","£­",$text);
	$text=str_replace(";","£»",$text);
	$text=str_replace(":","£º",$text);
	$text=str_replace("<","&lt;",$text);
	$text=str_replace(">","&gt;",$text);
	$text=str_replace("%","&amp;",$text);
	$text=str_replace("select","<c>s</c>elect",$text);
	$text=str_replace("SELECT","<c>S</c>ELECT",$text);
	$text=str_replace("delete","<c>d</c>elete",$text);
	$text=str_replace("DELETE","<c>D</c>ELETE",$text);
	$text=str_replace("update","<c>u</c>pdate",$text);
	$text=str_replace("UPDATE","<c>U</c>pDATE",$text);
	$text=str_replace("exec","<c>e</c>xec",$text);
	$text=str_replace("EXEC","<c>E</c>XEC",$text);
	$text=str_replace("drop","<c>d</c>rop",$text);
	$text=str_replace("DROP","<c>D</c>ROP",$text);
	$text=str_replace("or","<c>o</c>r",$text);
	$text=str_replace("OR","<c>O</c>R",$text);
	$text=str_replace("and","<c>a</c>nd",$text);
	$text=str_replace("AND","<c>A</c>ND",$text);
	$text=trim($text);
	return $text;
}

// XSS 관련 방지 및 따옴표 등 처리
function viewReplace($text) {
	// echo '<pre>' , var_dump("text=".$text) , '</pre>';
	// var_dump("text=".$text);
	// console.log(json);
	$text = str_replace("<!--StartFragment-->","",$text);
	$use_tag = "font,p,br,a,img,map,table,tbody,th,tr,td,span,u,b,i,strong,ul,ol,li,style,div,map,area,pre";
	$tag = explode(",",$use_tag);
	// $tag = $use_tag;
	for ($i = 0; $i < count($tag); $i++){
		// $text = preg_replace("&lt;".$tag[$i]." ","<".$tag[$i]." ",$text);
		// $text = preg_replace("&lt;".$tag[$i]."&gt;","<".$tag[$i].">",$text);
		// $text = preg_replace("&lt;/".$tag[$i],"</".$tag[$i],$text);
		$text = preg_replace("/&lt;".$tag[$i]." /","<".$tag[$i]." ",$text);
		$text = preg_replace("/&lt;".$tag[$i]."&gt;/","<".$tag[$i].">",$text);
		// $text = preg_replace("&lt;/".$tag[$i],"</".$tag[$i],$text);
	}
	$text=str_replace("&gt;",">",$text);
	$text=str_replace("£¢","\"",$text);
	$text=str_replace("£§","\'",$text);
	$text=str_replace("£»",";",$text);
	$text=str_replace("£º",":",$text);
	$text=str_replace("&lt;!£­£­","<!--",$text);
	$text=str_replace("£­£­>","-->",$text);
	$text=str_replace("£­","-",$text);
	$text=str_replace("£œ","=",$text);
	$text=trim($text);
	return $text;
}

// 메세지 처리
function alertMsg($text) {
echo "<script language=\"javascript\" type=\"text/JavaScript\">\n";
echo "<!--\n";
echo "alert (\"$text\");\n";
echo "//-->\n";
echo "</script>\n";
}

// 파일 업로드 처리
function upload_file($path, $file, $file_name, $tmp_file, $seq, $empno, $regdate) {
	if ($seq != "" && $empno != "" && $regdate != "") {
		//파일 확장자 체크
		$full_filename = explode(".",$file_name);
		$extension = strtolower($full_filename[sizeof($full_filename)-1]);
		$real_filename = $regdate."_".$seq;
		$new_filename = $real_filename.".".$extension;

		if ($extension != "gif" && $extension != "bmp" && $extension != "jpg" && $extension != "jpeg" && $extension != "tif" && $extension != "tiff" && $extension != "doc" && $extension != "xls" && $extension != "ppt" && $extension != "docx" && $extension != "pptx" && $extension != "xlsx" && $extension != "hwp" && $extension != "pdf") {
			alertMsg("파일종류(확장자)가  '$extension'인 파일은 전송할 수 없습니다.");
			page_redirect2 ("javascript:history.back();", "");
			exit;
		}

		$flag = 1; 
		$count = 1;
		while ( $flag == 1 ) {
			if( file_exists("$path$new_filename") ) {
				$new_filename = $real_filename."_".$count.".".$extension;
				$count++;
			} else {
				$flag = 0; 
				break;
			}
		}

		if ( !copy($tmp_file,$path.$new_filename) ) {
			alertMsg($file_name ." 복사 실패");
			page_redirect2 ("javascript:history.back();", "");
			exit;
		}

		if ( !unlink($tmp_file) ) {
			alertMsg($file_name . " 임시파일 삭제 실패");
			page_redirect2 ("javascript:history.back();", "");
			exit;
		}

		return $new_filename;
	} else return "필수 정보가 누락되었습니다.";
}

// 파일 업로드 처리
function upload_file_svr($path, $file, $file_name, $tmp_file, $seq, $empno, $regdate) {
	if ($seq != "" && $empno != "" && $regdate != "") {
		//파일 확정자 체크
		$full_filename = explode(".",$file_name);
		$extension = strtolower($full_filename[sizeof($full_filename)-1]);
		$real_filename = $regdate."_".$seq;
		//$real_filename = $regdate."_".$seq."_".$empno;
		$new_filename = $real_filename.".".$extension;

		if ($extension != "gif" && $extension != "bmp" && $extension != "jpg" && $extension != "jpeg" && $extension != "tif" && $extension != "tiff" && $extension != "doc" && $extension != "xls" && $extension != "ppt" && $extension != "docx" && $extension != "pptx" && $extension != "xlsx" && $extension != "hwp" && $extension != "pdf" && $extension != "png" && $extension != "zip" && $extension != "mp4" && $extension != "wmv" && $extension != "avi" ) {
			alertMsg("파일종류(확장자)가 '$extension'인 파일은 전송할 수 없습니다.");
			page_redirect2 ("javascript:history.back();", "");
			exit;
		}

		$flag = 1; 
		$count = 1;
		while ( $flag == 1 ) {
			if( file_exists("$path$new_filename") ) {
				$new_filename = $real_filename."_".$count.".".$extension;
				$count++;
			} else {
				$flag = 0; 
				break;
			}
		}

		if ( !copy($tmp_file,$path.$new_filename) ) {
			alertMsg($file_name ." 복사 실패");
			page_redirect2 ("javascript:history.back();", "");
			exit;
		}

		if ( !unlink($tmp_file) ) {
			alertMsg($file_name . " 임시파일 삭제 실패");
			page_redirect2 ("javascript:history.back();", "");
			exit;
		}

		return $new_filename;
	} else return "필수 정보가 누락되었습니다.";
}

// 파일 이동 처리
function move_file($src_path, $tar_path, $file_name, $del = 0) {
	$old_filename = $file_name;

	// 공백과 업로드 문제되는 파일명의 ASCII 코드값을  "_" 로 수정
	$ascii = Array ("32","34","36","38","39","40","41","42","47","60","62","63","92","96","124");

	for($i = 0; $i < strlen($file_name); $i++) {
		if( ord ( substr($file_name,$i,1) ) > 127 ) {
			$i++;
		}

		for($j=0; $j < sizeof($ascii); $j++) {
			if( ord( substr($file_name,$i,1) ) == $ascii[$j] ) {
				$file_name = str_replace( substr($file_name,$i,1),"_",$file_name );
			}
		}
	}

	$new_filename = $file_name;

	$count = 1;
	$flag = 1;

	while ( $flag ) {
		if( file_exists("$tar_path$new_filename") ) {
			$head = preg_replace("/.".$extension."/", "", $file_name);
			$new_filename = $head."_".$count.".".$extension;
			$count++;
		} else {
			break;
		}
	}

	if ( !copy($src_path.$old_filename,$tar_path.$new_filename) ) {
		alertMsg($old_filename ." 복사 실패");
		page_redirect2 ("javascript:history.back();", "");
		exit;
	}

	if ( $del ) {
		if ( !unlink($src_path.$old_filename) ) {
			alertMsg($old_filename . " 파일 삭제 실패");
			page_redirect2 ("javascript:history.back();", "");
			exit;
		}
	}
	return $new_filename;
}

// 전달변수 문자열값
function getRequestString ($name) {
	if ($name != "") {
		$ret = (isset($_POST[$name]) && $_POST[$name] != "") ? charReplace($_POST[$name]) : "";
		if ($ret == "") {
			$ret = (isset($_GET[$name]) && $_GET[$name] != "") ? charReplace($_GET[$name]) : "";
		}
		return $ret;
	} else {
		return "Input Name";
	}
}

// 전달변수 숫자값
function getRequestNumber ($name) {
	if ($name != "") {
		$ret = (isset($_POST[$name]) && $_POST[$name] != "") ? charReplace($_POST[$name]) : -1;
		if ($ret == "") {
			$ret = (isset($_GET[$name]) && $_GET[$name] != "") ? charReplace($_GET[$name]) : -1;
		}
		return $ret;
	} else {
		return "Input Name";
	}
}

?>
