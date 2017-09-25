<?php

//request data
$Zip_path='files.zip';//zip file path
$pass="";//user password. Its still not working correctly
$cmd='del';//'get','rname','del','open','c_dir','a_files'
$index='2' ;//calling index
$index_name="";//calling index file or folder name
$c_dir_path='.trash/159';//path to create folder includng that wanted to create folder's name
$a_files_path = array('gsd/gsgd/1.php'=>'C:\wamp64\www\files\1.php' , 'gsd\gsd\zip.php'=>'C:\wamp64\www\files\zip.php');//give you wanted to add files path list to zip (as path inside zip => real path to file)
$rname='ee';//new file name for rename & folder rename immposible directly

$zip = new ZipArchive;

if($zip->open($Zip_path) == 'TRUE') {

	$zip->setPassword($pass);

	if($cmd=='open') {

		$contents='';
		$fp = $zip->getStream($zip->statIndex($index)['name']);
		if(!$fp) exit("failed\n");

		while (!feof($fp)) {
			$contents .= fread($fp, 2);
		};

		fclose($fp);
		file_put_contents('t',$contents);
		$filedetil=explode("/",strtolower($zip->statIndex($index)['name']));
		$filedetil=explode(".",end($filedetil));

		$mime_types = array(
			'txt' => 'text/plain',
			'htm' => 'text/html',
			'html' => 'text/html',
			'php' => 'text/html',
			'css' => 'text/css',
			'js' => 'application/javascript',
			'json' => 'application/json',
			'xml' => 'application/xml',
			'swf' => 'application/x-shockwave-flash',
			'flv' => 'video/x-flv',

			// images
			'png' => 'image/png',
			'jpe' => 'image/jpeg',
			'jpeg' => 'image/jpeg',
			'jpg' => 'image/jpeg',
			'gif' => 'image/gif',
			'bmp' => 'image/bmp',
			'ico' => 'image/vnd.microsoft.icon',
			'tiff' => 'image/tiff',
			'tif' => 'image/tiff',
			'svg' => 'image/svg+xml',
			'svgz' => 'image/svg+xml',

			// archives
			'zip' => 'application/zip',
			'rar' => 'application/x-rar-compressed',
			'exe' => 'application/x-msdownload',
			'msi' => 'application/x-msdownload',
			'cab' => 'application/vnd.ms-cab-compressed',

			// audio/video
			'mp3' => 'audio/mpeg',
			'qt' => 'video/quicktime',
			'mov' => 'video/quicktime',

			// adobe
			'pdf' => 'application/pdf',
			'psd' => 'image/vnd.adobe.photoshop',
			'ai' => 'application/postscript',
			'eps' => 'application/postscript',
			'ps' => 'application/postscript',

			// ms office
			'doc' => 'application/msword',
			'rtf' => 'application/rtf',
			'xls' => 'application/vnd.ms-excel',
			'ppt' => 'application/vnd.ms-powerpoint',

			// open office
			'odt' => 'application/vnd.oasis.opendocument.text',
			'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
		);

		if (array_key_exists(end($filedetil), $mime_types)) {
			$mime=$mime_types[end($filedetil)];
		}
		else {
			$mime= 'application/octet-stream';
		};

	header("Content-type: ".$mime);
	echo $contents;

	//$file=fopen(end($filedetil),"w");
	//$temp = tmpfile();
	//fwrite($temp ,$contents);
	//fseek($temp,0);
	//echo fread($temp,1024);
	//fclose($file);


};

	if ($cmd=='get') {
		for ($i=0; !empty($zip->statIndex($i)['name']); $i++) {//generating list (including identification index, name or path, size, crc, mtime, compares_Size, comp_method)of folders and files inside the zip
			echo "index: ".$i." | ";
			if(substr($zip->statIndex($i)['name'], -1)=='/') {
				echo "dirpath: ";
			} 
			else {
				echo "Filepath: ";
			};
			echo $zip->statIndex($i)['name']." | Size: ".$zip->statIndex($i)['size']." bytes"." | crc: ".$zip->statIndex($i)['crc']." | mtime: ".$zip->statIndex($i)['mtime']." | compares_Size: ".$zip->statIndex($i)['comp_size']." | comp_method: ".$zip->statIndex($i)['comp_method']."<br>";
			};
		};
	
	if ($cmd=='rname') {//rename using index given file rname
		if($zip->renameIndex($index,$rname))	{
			echo "rname_ok";
		}
		else {
			echo "rname_fail";
		};
	};
	
	if ($cmd=='del') {//delete file using index
		$deleted=$zip->statIndex($index)['name'];
		if(substr($deleted, -1)=='/'){
			$type="dir";
		} 
		else {
			$type="file";
		};
		
		if($zip->deleteIndex($index)) {
			echo $type."_del";
		}
		else {
			echo $type."_fail";
		};
		
		for ($i=0; !empty($zip->statIndex($i)['name']); $i++) {
			
		};
			
	};
	
	if ($cmd=='c_dir') {//Create dir inside zip using $c_dir_path
		if($zip->addEmptyDir($c_dir_path)) {
			echo 'c_dir_ok';
		} 
		else {
			echo 'c_dir_fail';
		};
	};
	
	if ($cmd=='a_files') {
		foreach ($a_files_path as $key => $value) {
			$zip->addFile($value,$key);
		};

	};
		
		

}
else {
	switch($zip->open($ZipFileName)) {
		case ZipArchive::ER_EXISTS: 
			$ErrMsg = "ER_EXISTS";//File already exists.
			break;

		case ZipArchive::ER_INCONS: 
			$ErrMsg = "ER_INCONS";//Zip archive inconsistent.
			break;
                
		case ZipArchive::ER_MEMORY: 
			$ErrMsg = "ER_MEMORY";//Malloc failure.
			break;
                
		case ZipArchive::ER_NOENT: 
			$ErrMsg = "ER_NOENT";//No such file.
			break;
                
		case ZipArchive::ER_NOZIP: 
			$ErrMsg = "ER_NOZIP";//Not a zip archive.
			break;
                
		case ZipArchive::ER_OPEN: 
			$ErrMsg = "ER_OPEN";//Can't open file.
			break;
                
		case ZipArchive::ER_READ: 
			$ErrMsg = "ER_READ";//Read error.
			break;
                
		case ZipArchive::ER_SEEK: 
			$ErrMsg = "ER_SEEK";//Seek error.
			break;
            
		default: 
			$ErrMsg = "Unknow_(Code:".$rOpen.")";
			break;
	}
	die( 'ZipArchive_Error:'.$ErrMsg);
};


$zip->close();

?>
