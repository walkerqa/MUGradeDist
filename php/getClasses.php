<?php
/*
	This page receives all of the form information and returns a JSON list of classes
*/
	
$data = array(
                "DEPARTMENT"=>$_GET['dept'],
                "CLASS_NO"=>$_GET['num'],
                "INSTRUCTOR"=>$_GET['inst'],
                "FROM_YEAR"=>$_GET['from'],
                "TO_YEAR"=>$_GET['to'],
		"DID"=>$_GET['did'],
		"IID"=>$_GET['iid'],
		"SEM"=>$_GET['sem'],
		"LOC"=>$_GET['loc']
        );

$getClasses = sprintf("SELECT D.NameShort, I.iid, I.name, C.*, G.avggpa, G.finished FROM 
                  departments AS d JOIN
                  class AS c ON c.did = d.did JOIN
                  instructors AS i on i.iid = c.iid JOIN
                  grades as g on g.cid = c.cid
                  WHERE year >= %s ", $data['FROM_YEAR']);
if($data['LOC'] != ""){
  $getClasses = sprintf($getClasses." AND campus like '%s' ",$data['LOC']);
}
if($data['SEM'] != ""){
  $getClasses = sprintf($getClasses." AND Semester == %s ",$data['SEM']);
}
if($data['IID'] != -1){
	$getClasses = sprintf($getClasses." AND c.iid == %s ",$data['IID']);
}else if($data['IID'] == -1 && $data['INSTRUCTOR'] != ""){
	$getClasses = sprintf($getClasses." AND I.name like '%%%%%s%%%%' ",$data['INSTRUCTOR']);
}

if($data['DID'] != -1){
	$getClasses = sprintf($getClasses." AND d.did == %s ",$data['DID']);
}

if($data['CLASS_NO'] != ""){
	$getClasses = sprintf($getClasses." AND c.number == '%s' ",$data['CLASS_NO']);
}

$getClasses = $getClasses." AND year <= ".$data["TO_YEAR"]." ORDER BY c.number LIMIT 500 ";

$db = new SQLite3('../db/grades.db');                        
$db -> busyTimeout(5000);

$result = $db -> query($getClasses);

$row = array();
$i = 0;
while($res = $result -> fetchArray(SQLITE3_ASSOC)){
  $row[$i] = $res;
  $i++;
}

$db ->close();
unset($db);
header('Content-Type: application/json');
echo json_encode($row);
?>
