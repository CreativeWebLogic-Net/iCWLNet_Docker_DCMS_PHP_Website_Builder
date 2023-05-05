<?php
	class DistributedCMS{
		var $ConfigServer="https://git.creativeweblogic.net/Server-Config-File.html";
		var $RemoteServer="access.sitemanage.info/";
		var $BaseCacheDirectory="cache/";
		var $BaseDomainCacheDirectory="../cache/";
		var $current_dir="";
		var $current_back_dir="";
		var $LocalServer;
		var $CacheText="Use Cached File";
		var $Current_File="";
		var $Current_Full_Cached_File="";
		var $RequestUnCachedFiles=true;
		var $RemoteServerIP="142.132.144.12";
		var $ForbiddenExtensions=array();
		var $useragent="curl";
		var $cookieFile = "cookies.txt";
		var $guid="";
		var $domain_folders=array("html","images","linked","cookies");
		var $domain_folder_index="";
		var $file_extensions=array();
		var $current_file_extension=array();
		var $Current_Full_Directories=array();
		var $Current_Full_Files=array();
		var $Current_Full_File_Index=0;
		var $Current_Cache_Dir="";
		var $Current_Opened_Cache_File_Location="";
		var $error_count=0;
		
		function __construct(){
			$this->create_domain_folders();
			$this->create_constants();
			$this->create_cache_variables();
			
			
			$this->create_file_extensions();
		}
		
		function create_domain_folders() 
		{ 
			foreach($this->domain_folders as $key=>$val){
				if($this->BaseDomainCacheDirectory="../cache/"){
					$this->create_cache_variables();
				}
				$current_domain_folder=$this->BaseDomainCacheDirectory;
				if (!file_exists($current_domain_folder)) {
					if(!mkdir($current_domain_folder)){
						echo "error cdf".$current_domain_folder."-\n\n";
						
					}
				}
				$this->Current_Cache_Dir=$current_domain_folder;
				
				$current_folder=$this->BaseDomainCacheDirectory.$val;
				if (!file_exists($current_folder)) {
					if(!mkdir($current_folder)){
						$this->Error("error".$current_folder."-\n\n",9);
						echo "error".$current_folder."-\n\n";
					}else{
						$this->Current_Full_Directories[$key]=$current_folder;
						$this->Error("=20011=".$current_folder."-20011-\n\n",1);
						//print "=20011=".$current_folder."-20011-\n\n";
					}
				}
			}
		}
		
		function create_file_extensions() 
		{ 
			$this->file_extensions[$this->domain_folders[0]]=array("html","htm","php","py","pl","ci","aspx","/");
			$this->file_extensions[$this->domain_folders[1]]=array("jpg","png","gif","svg","tiff","eps","psd","ico");
			$this->file_extensions[$this->domain_folders[2]]=array("css","js","xml","txt","csv");
			$this->file_extensions[$this->domain_folders[3]]=array("txt");
			$this->Current_Full_File_Index=0;
		}
		
		function create_cache_variables() 
		{ 
			//$this->Current_Full_File_Index
			$LocalServer=$_SERVER['HTTP_HOST'];
			//$this->BaseDomainCacheDirectory=$this->current_dir.$this->CacheDirectory().$LocalServer."/";
			$this->BaseDomainCacheDirectory=$this->CacheDirectory().$LocalServer."/";
			
		}
		
		function create_constants() 
		{ 
			$this->error_count=0;
			$this->Current_File=$_SERVER['REQUEST_URI'];//$_SERVER['REQUEST_URI']
			$this->LocalServer=$_SERVER['HTTP_HOST'];
			
			$current_dir=pathinfo(__DIR__);
			$this->current_back_dir=$current_dir["dirname"].'/';
			$this->current_dir=$current_dir['dirname'].'/'.$current_dir['basename']."/";
			
			//$this->LocalServer=urlencode($_SERVER['HTTP_HOST']);
			
			//$this->BaseDomainCacheDirectory=$this->CacheDirectory().$this->slash_wrap($this->LocalServer)."/";
			$this->BaseDomainCacheDirectory=$this->CacheDirectory().$this->domain_folder_index.$this->LocalServer."/";
			//print($this->BaseDomainCacheDirectory);
			//print "\n Base Directory |".$this->BaseDomainCacheDirectory."-URI-".$this->Current_File."\n\n";
			$this->Error("\n Base Directory |".$this->BaseDomainCacheDirectory."-URI-".$this->Current_File."\n\n");
			
		}
		
		function make_guid ($length=32) 
		{ 
			if (function_exists('com_create_guid') === true)
			{
					return trim(com_create_guid(), '{}');
			}else{
				$key="";    
				$minlength=$length;
				$maxlength=$length;
				$charset = "abcdefghijklmnopqrstuvwxyz"; 
				$charset .= "ABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
				$charset .= "0123456789"; 
				if ($minlength > $maxlength) $length = mt_rand ($maxlength, $minlength); 
				else                         $length = mt_rand ($minlength, $maxlength); 
				for ($i=0; $i<$length; $i++) $key .= $charset[(mt_rand(0,(strlen($charset)-1)))]; 
				return $key;
			}	
		}
		
		function get_file_date($filename){
			//echo "$filename was last modified: " . date ("F-d-Y H:i:s.", filemtime($filename));	
			$ret_val=date ("Y-m-d H:i:s");
			if(file_exists($filename)) {
				$ret_val=date ("Y-m-d H:i:s", filemtime($filename));
			}else{
				//$ret_val=date ("YmdHis");
			}
			$this->Error("=5678 get_file_date=-|=".$ret_val."=|=-\n\n",2);
			return urlencode($ret_val);
		}

		
		function set_cookie(){
			$this->Error("= set_cookie( 299=".var_export($_SESSION)."=|=-\n\n");
			if(!isset($_SESSION['guid'])){
				$this->guid=$this->make_guid();
				$_SESSION['guid']=$this->guid;
			}else{
				$this->guid=$_SESSION['guid'];
			}
			
			//print "=200=-\n\n";
			//print_r($this->Current_Full_Directories);
			//print "=/200=-\n\n";
			$this->Error("=200=-\n\n".var_export($this->Current_Full_Directories)."=/200=-\n\n");
			//$this->cookieFile = "cache/cookies/".$this->guid."-cookies.txt";
			if(isset($this->Current_Full_Directories["cookies"])){
				$this->cookieFile =$this->Current_Full_Directories["cookies"].$this->guid."-cookies.txt";
				if(!file_exists($this->cookieFile)) {
					$fh = fopen($this->cookieFile, "w");
					fwrite($fh, "");
					fclose($fh);
				}
			}else{
				$this->Error("=000003=-\n\n".var_export($this->Current_Full_Directories)."=000003=-\n\n");
			}
			
		}
		
		function slash_wrap($DisplayPage){
			return urlencode(base64_encode($DisplayPage));
		}
		
		function CacheDirectory(){
			//return $this->BaseCacheDirectory;
			$dir=$this->current_back_dir.$this->BaseCacheDirectory;
			//print "=2=".$dir."="."-\n\n";
			return $this->current_back_dir.$this->BaseCacheDirectory;
		}
		function CheckIfHTMLFile($DisplayPage){
			//print "-00123\n\n".$DisplayPage."-\n\n";
			$ret_val=false;
			$BSlashEncoded='/';
			$end_of_string=substr($DisplayPage,strlen($DisplayPage)-strlen($BSlashEncoded));
			
			if($end_of_string==$BSlashEncoded){
				$ret_val=true;
				$this->Error("-00123 true\n\n".$DisplayPage."-eos-".$end_of_string."-encoded-".$BSlashEncoded."-rv-".var_dump($ret_val)."\n\n");
				//print "-00123 true\n\n".$DisplayPage."-eos-".$end_of_string."-encoded-".$BSlashEncoded."-rv-".var_dump($ret_val)."\n\n";
			}else{
				$ret_val=false;
				$this->Error("-00123 false\n\n".$DisplayPage."-eos-".$end_of_string."-encoded-".$BSlashEncoded."-rv-".var_dump($ret_val)."\n\n");
				//print "-00123 false\n\n".$DisplayPage."-eos-".$end_of_string."-encoded-".$BSlashEncoded."-rv-".var_dump($ret_val)."\n\n";
			}
			return $ret_val;
		}
		
		function CheckFilesDuplicates($DisplayPage){
			//print "-00123\n\n".$DisplayPage."-\n\n";
			$ret_val=false;
			foreach($this->Current_Full_Files as $current_index=>$values){
				if($values["filename"]==$DisplayPage){
					$ret_val=true;
				}else{
					$ret_val=false;
				}
				
			}
			return $ret_val;
		}
		
		function Set_Full_Files($dimensions_array=array(),$files_index=0){
			if(!$this->CheckFilesDuplicates($dimensions_array["filename"])){
				if(count($dimensions_array)>0){
					//$dimensions_array["encoded_filename"]
					
					$current_index=count($this->Current_Full_Files);
					$this->Current_Full_File_Index=$current_index;
					$this->Current_Full_Files[$current_index]["filename"]=$dimensions_array["filename"];
					$this->Current_Full_Files[$current_index]["encoded_filename"]=$dimensions_array["encoded_filename"];
					$this->Current_Full_Files[$current_index]["extension"]=$dimensions_array["extension"];
					$this->Current_Full_Files[$current_index]["extension_type"]=$dimensions_array["extension_type"];
					$this->Current_Full_Files[$current_index]["directory"]=$dimensions_array["directory"];
					$this->Current_Full_Files[$current_index]["complete_cache_location"]=$dimensions_array["directory"].$dimensions_array["encoded_filename"];
					return false;
				}else{
					//$this->Current_Full_File_Index=$files_index;
					return $this->Current_Full_Files[$files_index];
				}
			}else{
				return false;
			}
			
		}
		
		function CheckFileDestination($DisplayPage){
			//print "-00123\n\n".$DisplayPage."-\n\n";
			$ret_val=false;
			
			foreach($this->file_extensions as $key=>$val){
				$this->Error("-00123\n\n".$key."-".var_export($val,true)."|\n\n",1);
				//print "-00123\n\n".$key."-".var_export($val,true)."|\n\n";
				
				foreach($val as $ext_key=>$extension){
					
					//$extension="/";
					$end_of_string=substr($DisplayPage,strlen($DisplayPage)-strlen($extension));
					//print "-0010002\n\n".$end_of_string."-\n\n";
					
					if($end_of_string==$extension){
						$array_dims["filename"]=$DisplayPage;
						$array_dims["encoded_filename"]=$this->slash_wrap($DisplayPage);
						$array_dims["extension"]=$extension;
						$array_dims["extension_type"]=$key;
						$array_dims["directory"]=$this->BaseDomainCacheDirectory.$key.$extension;
						
						$this->Set_Full_Files($array_dims);
						break;
						break;
						$ret_val=true;
					}else{
						if(!$ret_val) $ret_val=false;
					}
					
					
				}
				
			}
			
			$this->Error("-001234\n\n".$key."-".var_export($this->Current_Full_Files,true)."|\n\n");
			//print "-001234\n\n".$key."-".var_export($this->Current_Full_Files,true)."|\n\n";
			//print "-0010002\n\n".var_export($this->Current_Full_Files)."-".$ret_val."\n\n";
			return $ret_val;
		}
		
		function LocalFileName($DisplayPage){
			//print "-001\n\n".$DisplayPage."-\n\n";
			/*
			if($this->CheckFileDestination($DisplayPage)){
				$filename =$this->Current_Full_Files[$this->Current_Full_File_Index]["encoded_filename"];
			}else{
				$filename ="404 Error";
			}
			
			$this->Current_Full_Cached_File=$filename;
			$this->Error("002123-LocalFileName |".$filename."|-\n\n");
			*/
			//print "\n\n002123-".var_export($this->Current_Full_Files)."-\n\n";
			$filename=$this->BaseDomainCacheDirectory."html/".$this->slash_wrap($DisplayPage);
			return $filename;
		}
		
		function url_get_contents($url){//,$DisplayPage) {
			//print $url;
			$this->Error("first url_get_contents | ".$url." | get_url \n",1);
			
			$this->set_cookie();
			$encoded="";
			if(count($_GET)>0){
				foreach($_GET as $name => $value) {
					$encoded .= urlencode($name).'='.urlencode($value).'&';
			  	}
			}
			if(count($_POST)>0){
				foreach($_POST as $name => $value) {
					$encoded .= urlencode($name).'='.urlencode($value).'&';
				  }
			}
			  
			$encoded = substr($encoded, 0, strlen($encoded)-1);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_POSTFIELDS,  $encoded);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
			curl_setopt($ch, CURLOPT_USERAGENT, $this->useragent);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile); // Cookie aware
			curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile); // Cookie aware
			$result=curl_exec($ch);
			curl_close($ch);
			
			//$this->Error("\n 667 url_get_contents retrieved | ".$result." | content legth \n",1);
			$this->Error("\n 667 url_get_contents retrieved | ".strlen($result)." | content legth \n",1);
			return $result;
		}
		
		function WriteCacheFile($DisplayPage,$content){
			//$DisplayPage="xxx";
			//print_r($this->Current_Full_Files);
			if(isset($this->Current_Full_Files[$this->Current_Full_File_Index]["encoded_filename"])){
				$filename =$this->Current_Full_Files[$this->Current_Full_File_Index]["encoded_filename"];
				$this->Error("\n WriteCacheFile | ".$filename." | write cache",1,$this->Current_Full_Files);
				$filename =$this->slash_wrap($DisplayPage);
				
				if(strlen($content)>0){
					$filename =$this->Current_Cache_Dir."/html/".$filename;
					$this->Current_Opened_Cache_File_Location=$filename;
					//$filename = $this->LocalFileName($DisplayPage);

					//$filename =$this->Current_Full_Files[$this->Current_Full_File_Index]["encoded_filename"];
					//$DisplayPage
					//$this->Error("\n 999876 | ".$DisplayPage." | ",1,$this->Current_Full_Files);
					$this->Error("\n WriteCacheFile 2 | ".$filename." | is not writable",1);
					$fh = fopen($filename, "w");
					fwrite($fh, $content);
					fclose($fh);
				}else{
					$this->Error("\n 9992 | ".$filename." | no content",1);
				}
			}
			
		}
		
		function CheckIfCacheExists($DisplayPage){
			//$filename = $DisplayPage;
			$filename=$this->BaseDomainCacheDirectory."html/".$this->slash_wrap($DisplayPage);
			$this->Error("-hh6 CheckIfCacheExists-|".$filename."|--".$DisplayPage."|-",2);
			
			if($filename!=""){
				if(file_exists($filename)){
					if(filesize($filename)!=0){
						return true;
					}else{
						return false;
					}
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
		
		function DisplayCacheFile($DisplayPage){
			//$filename = $this->LocalFileName($DisplayPage);
			//$filename =$this->Current_Opened_Cache_File_Location;
			$filename=$this->BaseDomainCacheDirectory."html/".$this->slash_wrap($DisplayPage);
			//print "\n-DCacheFile=".$filename."\n";
			if($this->CheckIfCacheExists($DisplayPage)){
				$this->Error("000111-DisplayCacheFile Exists |".$filename."-|",2);
				$handle = fopen($filename, "r");
				$contents = fread($handle, filesize($filename));
				//if(substr($DisplayPage,strlen($DisplayPage)-1)=="/"){
				fclose($handle);
				//print "-1-".$contents."-"."-\n\n";
				if(strlen($contents)==0){
					unlink($filename);
					//$ContType="Content-Type: ".exec("file -bi '$filename'");
					$ContType=mime_content_type($filename);
					header($ContType);
					//print $contents."-\n\n"."-\n\n";
				}else{
					//print "-".$contents."-\n\n"."-\n\n";
					$this->Error("000111-DisplayCacheFile Not Exists |".$filename."-|",2);
					return $contents;
					//print $this->DisplayRealtime($DisplayPage);
					
				}
			}else{
				$this->Error("0001119-DisplayCacheFile Non Exists |".$filename."-|",2);
			}
			
			
			//print $ContType;
		}
		
		function Error($error_text,$error_type=-1,$error_array=Array()){
			$line_number=__LINE__;
			$this->error_count++;
			$error_array_text="";
			if(count($error_array)>0){
				$error_array_text=var_export($error_array,true);
			}
			$pval="\n | ".$this->error_count." | ".$line_number."-|-".$error_text."-|-".$error_array_text."-|-".$error_type.=" | \n";
			if($error_type>0){
				print $pval;
			};
		}
		
		function IsValidFile($DisplayPage){
			/*
			if(substr($DisplayPage,strlen($DisplayPage)-1)=="/"){
				return true;
			}else{
				if(strlen($DisplayPage)>3){
					if(in_array(substr($DisplayPage,strlen($DisplayPage)-3),$this->ForbiddenExtensions)){
						return false;	
					}else{
						return true;
					}	
				}else{
					return false;	
				}
			}
			*/
			return true;
		}
		/*
		function RequestUpdate($DisplayPage){
			//print $DisplayPage;
			//$filename=$this->Current_Opened_Cache_File_Location;
			$last_cache_date=$this->get_file_date($filename);
			$DisplayCachePage=$this->Current_Opened_Cache_File_Location;
			if($this->IsValidFile($DisplayPage)){
				//$DisplayPage=urlencode($DisplayPage);
				$urldetails=$this->RemoteServer."?x=1&dcmshost=".$this->LocalServer."&dcmsuri=".$DisplayPage."&change=".$last_cache_date;	
				$retdata=$this->url_get_contents($urldetails);
				$this->WriteCacheFile($this->Current_Opened_Cache_File_Location,$retdata);
			}
		}
		*/
		
		function DisplayRealtime($DisplayPage="/"){
			//$filename=$this->Current_Opened_Cache_File_Location;
			$filename=$this->BaseDomainCacheDirectory."html/".$this->slash_wrap($DisplayPage);
			$this->Error("00001-DisplayRealtime |".$DisplayPage."|".$filename,2);
			
			$last_cache_date=$this->get_file_date($filename);
			$urldetails=$this->RemoteServer."?x=1&dcmshost=".urlencode($this->LocalServer)."&dcmsuri=".urlencode($this->Current_File)."&change=".urlencode($last_cache_date);
			//$this->url_get_contents($urldetails,$DisplayPage);
			//print $urldetails."-\n\n";
			$retdata=$this->url_get_contents($urldetails);
			$mystring = $retdata;
			$findme   = 'Use Cached File';//'Use Cached File';
			$pos = strpos($mystring, $findme);
			$this->Error("aaa 00000-DisplayRealtime | pos=".$pos." | f=".$filename."|sl=".strlen($retdata),2);
			$string_size=strlen($retdata);
			if($pos>-1){
				if(($string_size==0)||($retdata==$this->CacheText)){
					$retdata=$this->DisplayCacheFile($DisplayPage);
				}else{
					$retdata=$this->WriteCacheFile($DisplayPage,$retdata);
				}
				//$retdata=$this->DisplayCacheFile($DisplayPage);
				$this->Error("00-DisplayRealtime | p=".$pos." | f=".$filename."|sl=".strlen($retdata),2);
			}else{
				$retdata=$this->WriteCacheFile($DisplayPage,$retdata);
				$this->Error("11-DisplayRealtime |".$DisplayPage." | ".$pos." | ".$filename,2);
			}
			$this->Error("11223-all-DisplayRealtime |dp=".$DisplayPage." | p=".$pos." | f=".$filename."|",2);
			
			return $retdata;
		
		}
		
		function DisplayHTML($DisplayPage){
			
			//$DisplayPage=urlencode($DisplayPage);
			//$DisplayPage=$this->Current_Opened_Cache_File_Location;
			//if($this->IsValidFile($DisplayPage)){
				//$DisplayPage=urlencode($DisplayPage);
				//$DisplayPage=$DisplayPage;
			/*
				$this->Error("\n 76811 DisplayHTML |".$DisplayPage."|\n\n",2);
				if(!$this->CheckIfCacheExists($DisplayPage)){
					
					//print "-No File-l".$DisplayPage."l-\n\n"."-\n\n";
					if($this->RequestUnCachedFiles){
						print $this->DisplayRealtime($DisplayPage);
					}else{
						//echo"404"."-\n\n";	
					}
					$this->Error("1234 DisplayHTML | ".$DisplayPage."|\n\n"."-\n\n");
					//print "1234 New Data Page | ".$DisplayPage."\n\n"."-\n\n";
				}else{
					$this->DisplayCacheFile($DisplayPage);
					$this->Error("769 DisplayHTML| ".$DisplayPage."| \n\n",2);
					//print "Retrieved From Cache\n\n";
				}
			*/
			$this->Error("First Step DisplayHTML |".$DisplayPage."|\n\n",2);
			print $this->DisplayRealtime($DisplayPage);
			//}
		}
		
		function CommandInterface($DisplayPage){
			//if(eregi("update=",$DisplayPage)){
			/*
			if(strpos("update=",$DisplayPage)){
			
				if($_SERVER['REMOTE_ADDR']==$this->RemoteServerIP){
					$this->RequestUpdate($_GET['update']);
				}else{
					//echo "Invalid Requestor\n\n";	
				}
			}else{
				$this->DisplayHTML($DisplayPage);
			}
			*/
			$this->Error("111 CommandInterface| ".$DisplayPage."| \n\n",2);
			$this->DisplayHTML($DisplayPage);
		}
		
	}



?>