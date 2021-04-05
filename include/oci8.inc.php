<?

class OCI_Class {
var $conn = FALSE;
var $stmt = 0;
var $abstractData = FALSE;
var $error = FALSE;

function connect($id="edu",$passwd="edu",$oracle_sid="172.20.2.247:1521/DBDICM",$charset="UTF8") {
$this->conn = oci_connect($id,$passwd,$oracle_sid,$charset);
}

function parse($qry) {
  $this->stmt = oci_parse($this->conn,$qry);
}

function parseExec($qry) {
  $this->stmt = oci_parse($this->conn,$qry);
  $this->exec2();
}

function exec2($mode=OCI_NO_AUTO_COMMIT) {
  oci_execute($this->stmt);
  $this->error = oci_error($this->stmt);
  if($this->error) $this->disconnect();
}

function newDescriptor($type) {
  $this->abstractData = oci_new_descriptor($this->conn,$type);
}

function freeDescriptor() {
  oci_free_descriptor($this->abstractData);
}

function defineByName($upper,$var) {
  oci_define_by_name($this->stmt,$upper,$var);
}

function bindByName($place_holder,$var,$length) {
  oci_bind_by_name($this->stmt,$place_holder,$var,$length);
}

function fetch() {
  return oci_fetch($this->stmt);
}

function fetchInto() {
  return oci_fetch_assoc($this->stmt);
}

function fetchRows() {
  while($this->getchInto($col,OCI_RETURN_NULLS)){
    $rows[] = $col;
  }
  $this->parseFree();
  return $rows;
}

function result($i) {
  return oci_result($this->stmt,oci_field_name($this->stmt,$i));
}

function rowCount() {
  return oci_num_rows($this->stmt);
}

function parseFree() {
  oci_free_statement($this->stmt);
}

function disconnect() {
  if($this->error) {
    oci_rollback($this->conn);
  } else {
    oci_commit($this->conn);
  }  
  oci_close($this->conn);
}

}
$oci = new OCI_Class;
$oci -> connect();

$roci = new OCI_CLASS;
$roci -> connect();
