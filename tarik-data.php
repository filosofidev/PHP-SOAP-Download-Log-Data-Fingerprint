<?
$xhostname = "10.5.250.222";
$xdatabase = "presensi_pegawai";
$xusername = "root";
$xpassword = "kintiljirin";
$Face_db['host']=$xhostname;
$Face_db['user']=$xusername;
$Face_db['pass']=$xpassword;
$Face_db['db']=$xdatabase;
$Face_db['table']="checkinout";
$FaceDbConn = mysql_connect($Face_db['host'], $Face_db['user'], $Face_db['pass']) or DIE("Connection to database failed, perhaps the service is down !!");
  mysql_select_db($Face_db['db']) or DIE("Database Failed !!");

$IP="10.5.252.17";
$Key="0";
	$Connect = fsockopen($IP, "80", $errno, $errstr, 1);
	if($Connect){
		$soap_request="<GetAttLog><ArgComKey xsi:type=\"xsd:integer\">".$Key."</ArgComKey><Arg><PIN xsi:type=\"xsd:integer\">All</PIN></Arg></GetAttLog>";
		$newLine="\r\n";
		fputs($Connect, "POST /iWsService HTTP/1.0".$newLine);
	    fputs($Connect, "Content-Type: text/xml".$newLine);
	    fputs($Connect, "Content-Length: ".strlen($soap_request).$newLine.$newLine);
	    fputs($Connect, $soap_request.$newLine);
		$buffer="";
		while($Response=fgets($Connect, 1024)){
			$buffer=$buffer.$Response;
		}
	}else echo "Koneksi Gagal";	
	include("parse.php");
	$buffer=Parse_Data($buffer,"<GetAttLogResponse>","</GetAttLogResponse>");
	$buffer=explode("\r\n",$buffer);
	for($a=0;$a<count($buffer);$a++){
		$data=Parse_Data($buffer[$a],"<Row>","</Row>");
		$PIN=Parse_Data($data,"<PIN>","</PIN>");
		$DateTime=Parse_Data($data,"<DateTime>","</DateTime>");
		$Verified=Parse_Data($data,"<Verified>","</Verified>");
		$Status=Parse_Data($data,"<Status>","</Status>");
mysql_query("INSERT IGNORE INTO ".$Face_db['table']." (userid, checktime, verifycode, checktype, WorkCode ) VALUES ('" .  $PIN . "', '" . $DateTime. "', " .  $Verified . ", " .  $Status . ", " .  $Status . ")", $FaceDbConn);

}?>