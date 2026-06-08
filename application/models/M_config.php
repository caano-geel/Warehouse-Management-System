<?php
class M_config extends CI_Model  {

    function __construct(){
        parent::__construct();
    }
	
	public function seo($s) {
		$c = array (' ');
		$d = array ('-','/','\\',',','.','#',':',';','\'','"','[',']','{','}',')','(','|','`','~','!','@','%','$','^','&','*','=','?','+');
	
		$s = str_replace($d, '', $s);
		
		$s = strtolower(str_replace($c, '-', $s));
		return $s;
	}
}
