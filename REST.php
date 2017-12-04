<?php
//include("SmartArray.php");

class REST{
	private $method;
	private $request;
	private $input;
	private $registers;
	public $output;
	public $success=true;
	private $data;
	function __construct($data){
		$this->method=$_SERVER['REQUEST_METHOD'];
		$request=@$_SERVER['PATH_INFO'];
		$this->data=$data;
		$this->request=explode('/', trim($request,'/'));
		switch($this->request[0]){
			case "XML":
/*			$p = xml_parser_create();
			xml_parse_into_struct($p, file_get_contents('php://input'), $vals, $index);
			xml_parser_free($p);

			$this->input = $vals;*/
			$this->output = array("error"=>"wrong language");
			$this->success=false;
		break;
		case "JSON":
			$this->input = json_decode(file_get_contents('php://input'),true);
		break;
		default:
			$this->output = Array("error"=>"wrong language");
			$this->success=false;
		break;
		}
		//echo"input:";
		//print_r($this->input);
		//echo "request:";
		//print_r($this->request);
	}
	
	function register($funct,$key){
		$this->registers[$key]=$funct;
	}
	function execute(){
		if($this->success){
			$k="";
			foreach($this->request as $r){
				$k.=$r."/";
			}
			$k=$this->method.":".substr($k,strpos($k,"/")+1);
			$temp1=substr($k,0,strrpos($k,"/"));
			$data=substr($temp1,strrpos($temp1,"/")+1);
			$temp2=substr($temp1,0,strrpos($temp1,"/"))."/*";
			//echo $temp2;
			if(array_key_exists($temp2,$this->registers)){	
				$this->output=$this->registers[$temp2]($this->data,$this->input,$data);
			}elseif(array_key_exists($k,$this->registers)){
				$this->output=$this->registers[$k]($this->data,$this->input,$data);
			}else{
				$this->output = Array("error"=>"Unknown path");
			}
		}
		
		echo json_encode($this->output);
	}
}

?>