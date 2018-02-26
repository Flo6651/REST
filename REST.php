<?php

class REST{
   public $verbose=false;   //if true debugginginformation is printed
   private $method;     //Contains the http Method
   private $request;    // contains the path of REST 
                        //  (http://domain.at/api.php/$request) 
                        //  as an array
   private $input;        // contains the decoded body 
                          //  input send with the request
   private $registers;    // contains function to execute 
   public $output;        // stores the output as array
   public $success=true;  // remains true until an error occures
   private $data;         // contains data used in every 
                          //  functions friom $registrs
   /*
    * 
    * 
    * 
    * 
    * */
    
   function __construct($data){
      $this->method=$_SERVER['REQUEST_METHOD'];
      $request=@$_SERVER['PATH_INFO'];
      $this->data=$data;
      $this->request=explode('/', trim($request,'/'));   
      switch($this->request[0]){      //selceting the language
         case "XML":
         $this->output = array("Error"=>"unsopported language");
         $this->success=false;
      break;
      case "JSON":
         $this->input = json_decode($_SERVER['CONTENT'],true);
      break;
      default:
         $this->output = Array("Error"=>"wrong language");
         $this->success=false;
      break;
      }
   }
   /* 
    * @param $funct
    *    contains the function
    * @param $key
    *    sets the path when to execute $funct 
    */
   function register($funct,$key){      
      $this->registers[$key]=$funct;
   }
   
   /*
    * @return
    *    returns the userspezific data
    */
   
   function getData(){
      return $this->data;
   }
   
   /*
    * @param $key
    *    contains the key from the input
    * 
    * @param $default
    *    contains teh return value if no key is found
    * 
    * @param $type
    *    contains the datatype returned (int|string|bool)
    * 
    */
   
   function get($key,$default,$type="string"){
      if(is_array($this->input)){
         if(array_key_exists($key,$this->input)){
            switch($type){
               case "int": return (int)$this->input[$key];
               case "string": return (string)$this->input[$key];            
               case "bool": return (bool)$this->input[$key];
            }
         }
      }
   return $default;
   }


   /*
    * executed once when all functions are registered
    * 
    */

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
         if($this->verbose ) echo $temp2;
         if(array_key_exists($temp2,$this->registers)){   
            $this->output=$this->registers[$temp2]
               ($this,$this->input,$data);
         }elseif(array_key_exists($k,$this->registers)){
            $this->output=$this->registers[$k]
               ($this,$this->input,$data);
         }else{
            $this->output = Array("error"=>"Unknown path");
         }
      }   
   echo json_encode($this->output);
   }
}

?>
