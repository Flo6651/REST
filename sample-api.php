<?php
include("./REST.php");
$_SERVER['CONTENT']=file_get_contents('php://input');	//
$conn=null;
$r=new REST($conn);

/*
 * called when path is 
 * e.g. 
 *    /JSON/path/50
 *    /JSON/Sensor/testSensor
 * 
 * the last element of the path is passed as $data
 * */

$r->register(function($ref,$input,$data){
	
		return Array("Error"=>"Sensor not found");
	
},"GET:path/*");

/*
 * callen when path is 
 * 
 * */
$r->register(function($ref,$input,$data){
	
		return Array("Error"=>"Sensor not found");
	
},"GET:path/");

//Executing of the Framework
$r->execute();
?>
