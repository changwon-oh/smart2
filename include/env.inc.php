<?
/*########################################################
#                                                        									#
#  프로그램명 : env.inc.php                            		#
#                                                        									#
#  기능설명 : 각 페이지 공통 환경 변수 처리             				 		#
#                                                        									#
########################################################*/


session_start();

$_HOMEPAGE = "http://".$_SERVER['HTTP_HOST']."/smart2/";

// $_UPLOAD_PATH = "/var/www/html/smart2/upload/";
$_UPLOAD_PATH = "C:/xampp/htdocs/smart2/upload/";

$_LIST_CNT = 10;


$_PAGE_CNT = 10;

?>
