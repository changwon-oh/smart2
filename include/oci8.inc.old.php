<?php
/*########################################################
#                                                       									 #
#  프로그램명 : oci8.inc.php                                   #
#                                                       									 #
#  기능설명 : 오라클 DB 관련 클래스						                         #
#                                                    										 #
#  작성일 :  2019. 09. 02                                   							#
#                                                        									#
#                     								     					#
#                                                        									#
########################################################*/

class OCI_Class {
   var $conn = FALSE;
   var $stmt = 0;
   var $abstractData= FALSE;
   var $error= FALSE;

function connect($id="devel",$passwd="1234",$oracle_sid="ORCL") {
   $this->conn = OCILogon($id,$passwd,$oracle_sid);
}

function serverVersion() {
   return @OCIServerVersion($this->conn);
}

function parse($qry) {
   $this->stmt=@OCIParse($this->conn,$qry);
}

function parseExec($qry) {
   $this->stmt=@OCIParse($this->conn,$qry);
   $this->exec();
}

function exec($mode=OCI_DEFAULT) { // OCI_DEFAULT : 매번 실행시마다 자동 commit가 되지 않도록 함.

   @OCIExecute($this->stmt,$mode);
   $this->error = ocierror($this->stmt);
   if($this->error) $this->disconnect();
}

/*
OCI_D_FILE
OCI_D_LOB
OCI_D_ROWID
*/

function newDescriptor($type) {
   $this->abstractData = @OCINewDescriptor($this->conn,$type);
}

function freeDescriptor() {
   OCIFreeDescriptor($this->abstractData);
}

function defineByName($upper,&$var) {
   @OCIDefineByName($this->stmt,$upper,&$var);
}

function bindByName($place_holder,&$var,$length) {
   @OCIBindByName($this->stmt,$place_holder,$var,$length);
}

/*
OCI_B_FILE(Binary File)
OCI_B_CFILE(Character-File)
OCI_B_CLOB(Character_LOB)
OCI_B_BLOB(Binary-LOB)
OCI_B_B_ROWID(ROWID)
*/

function bindByNameAbstract($place_holder,$type) {
   @OCIBindByName($this->stmt,$place_holder,&$this->abstractData,-1,$type);
   return $this->abstractData;
}

function fetch() {
   return @OCIFetch($this->stmt);
}

function result($i) { // $index ŽÂ 1 ºÎÅÍ œÃÀÛ
   return @OCIresult($this->stmt,$i);
}

function fetchInto(&$col,$mode=OCI_ASSOC) {     // 클래스는 디폴트가 OCI_ASSOC 또는 function fetchInto(&$col,$mode=OCI_NUM) {
   return @OCIFetchInto($this->stmt,&$col,$mode);
}

function fetchRows(){
	while($this->fetchInto(&$col,OCI_RETURN_NULLS)){
		$rows[] = $col;
	}
	$this->parseFree();
	return $rows;
}

function fetchStatement(&$arr) {
   return  @OCIFetchStatement($this->stmt,&$arr);
}

function numCols() {
   return @OCINumCols($this->stmt);
}

function columnName($index) { // $index 는 1 부터 시작
   return @OCIColumnName($this->stmt,$index);
}

function columnisNULL($index) {
   return @OCIColumnIsNULL($this->stmt,$index);
}

function columnType($index) {
   return @OCIColumnType($this->stmt,$index);
}

function columnSize($index) {
   return @OCIColumnSize($this->stmt,$index);
}

function StatementType() {
   return @OCIStatementType($this->stmt);
}

function rowCount() {
   return @OCIRowCount($this->stmt);
}

function parseFree() {
   @OCIFreeStatement($this->stmt);
}

function disconnect() {
   if($this->error) {
      @OCIRollback($this->conn);
      die("<font color=red style=\"font-size:9pt\">ROLLBACK OCCURRED!! ".$this->error["message"]."</font>");
   }
   else {
      @OCICommit($this->conn);
   }
   @OCILogoff($this->conn);
}

} // end class

// Object 자동 생성
$oci = new OCI_Class;

// 오라클 DB 접속
$oci -> connect();

$roci = new OCI_Class;

// 오라클 DB 접속
$roci -> connect("smsusr","fheh!emd","SMS_NEW");
?>
