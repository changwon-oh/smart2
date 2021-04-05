<html>
<head>
<title>보고대상 선택</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="CSS/style.css" rel="stylesheet" type="text/css">
<SCRIPT LANGUAGE="JavaScript" SRC="./JS/common.js"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript" SRC="./JS/jquery-1.7.2.min.js"></SCRIPT>
</head>
<?php
include "./include/oci8.inc.php";

$qry_create = " CREATE TABLE KOSPOWB_REPORT
  (
    REPID       VARCHAR2(5 BYTE) NOT NULL ENABLE,
    EMPNO       VARCHAR2(20 BYTE) NOT NULL ENABLE,
    TITLE       VARCHAR2(1000 BYTE),
    USERNAME    VARCHAR2(20 BYTE),
    DEPT_NAME   VARCHAR2(50 BYTE),
    GRADE_NAME  VARCHAR2(20 BYTE),
    BOGO_EMPNO  VARCHAR2(50 BYTE),
    REP_EMPNO   VARCHAR2(10 BYTE),
    REP_DATE1   VARCHAR2(20 BYTE),
    REP_TIME1   VARCHAR2(10 BYTE),
    REP_DATE2   VARCHAR2(20 BYTE),
    REP_TIME2   VARCHAR2(10 BYTE),
    REP_CONTENT VARCHAR2(500 BYTE),
    REG_DATE DATE DEFAULT sysdate,
    GUBUN      VARCHAR2(3 BYTE),
    STATS      VARCHAR2(5 BYTE),
    REASON     VARCHAR2(1000 BYTE),
    STATS_DATE VARCHAR2(20 BYTE),
    END_DATE   VARCHAR2(20 BYTE),
    OPEN_YN    VARCHAR2(3 BYTE),
    DQ_YN      VARCHAR2(3 BYTE),
    SC_YN      VARCHAR2(3 BYTE) DEFAULT 'N',
    FILE_YN         VARCHAR2(1 BYTE),
    BOGO_NAME       VARCHAR2(100 BYTE),
    BOGO_GRADE_NAME VARCHAR2(100 BYTE),
    BOGO_DPRT_NAME  VARCHAR2(100 BYTE),
    PRIMARY KEY (REPID)
  )";
$oci -> parseExec($qry_create);
$oci -> parseFree();

$qry_create2= "CREATE TABLE KOSPOWB_FILE
  (
    FILEID      NUMBER(5,0),
    SEQ         NUMBER(5,0) NOT NULL ENABLE,
    WBID        NUMBER(5,0) NOT NULL ENABLE,
    UPLOAD_NAME VARCHAR2(250 BYTE) NOT NULL ENABLE,
    SAVED_NAME  VARCHAR2(250 BYTE) NOT NULL ENABLE,
    REGDATE DATE DEFAULT sysdate,
    GUBUN VARCHAR2(5 BYTE) DEFAULT 'S',
    PRIMARY KEY (FILEID)
  )";
$oci -> parseExec($qry_create2);
$oci -> parseFree();

$qry_create2_S= "CREATE SEQUENCE SEQ_KOSPOWB_FILE
	START WITH 1
 	MAXVALUE 100000
	MINVALUE 1
	NOCYCLE
	CACHE 20
	NOORDER";
$oci -> parseExec($qry_create2_S);
$oci -> parseFree();

$qry_create3 = "CREATE TABLE KOSPOWB_REPORT_ADMIN
  (
    REPID          VARCHAR2(5 BYTE) NOT NULL ENABLE,
    ADMIN_EMPNO    VARCHAR2(20 BYTE) NOT NULL ENABLE,
    BOGO_EMPNO     VARCHAR2(10 BYTE),
    MANAGER_EMPNO1 VARCHAR2(10 BYTE),
    MANAGER_EMPNO2 VARCHAR2(10 BYTE),
    MAIN_EMPNO     VARCHAR2(10 BYTE),
    SB_YN          VARCHAR2(3 BYTE) DEFAULT 'N',
    REG_DATE DATE DEFAULT sysdate,
    PRIMARY KEY (REPID)
  )";

$oci -> parseExec($qry_create3);
$oci -> parseFree();

$qry_create4 = "create sequence SEQ_KOSPOWB_FILE start with 1 maxvalue 1000000 minvalue 1 nocycle cache 20 noorder;";

$oci -> parseExec($qry_create4);
$oci -> parseFree();

$oci -> disconnect();
?>
