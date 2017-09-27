<?php

//request data
$Zip_path='files.zip';//zip file path
$pass='';//user password. Its still not working correctly
$cmd='get';//'get','rname','del','open','c_dir','a_files'
$index=array('null' => 'files/New folder/') ;//calling indexs as array(as 'index' => 'selected index file or folder path including name that selected') if it not define as index leave it 'null'
$c_dir_path='.trash/159';//path to create folder includng that wanted to create folder's name
$a_files_path = array('New folder/'=>'C:\wamp64\www\files\New folder');//give you wanted to add files or folders path list to zip (as path inside zip => real path to file)
$rname='ee';//new file name for rename & folder rename immposible directly

$zip = new ZipArchive;

if($zip->open($Zip_path) == 'TRUE') {

	$zip->setPassword($pass);

	if($cmd=='open') {

		$contents='';
		$fp = $zip->getStream($zip->statIndex($index)['name']);
		if(!$fp) exit('failed\n');

		while (!feof($fp)) {
			$contents .= fread($fp, 2);
		};

		fclose($fp);
		file_put_contents('t',$contents);
		$filedetil=explode('/',strtolower($zip->statIndex($index)['name']));

		$filedetil=explode('.',end($filedetil));

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

	header('Content-type: '.$mime);
	echo ($contents);

	//$file=fopen(end($filedetil),'w');
	//$temp = tmpfile();
	//fwrite($temp ,$contents);
	//fseek($temp,0);
	//echo fread($temp,1024);
	//fclose($file);

	};

	if ($cmd=='get') {
		for ($i=0; !empty($zip->statIndex($i)['name']); $i++) {//generating list (including identification index, name or path, size, crc, mtime, compares_Size, comp_method)of folders and files inside the zip
			echo 'index: '.$i.' | ';
			if(substr($zip->statIndex($i)['name'], -1)=='/') {
				echo 'dirpath: ';
			} 
			else {
				echo 'Filepath: ';
			};
			echo $zip->statIndex($i)['name'].' | Size: '.$zip->statIndex($i)['size'].' bytes'.' | crc: '.$zip->statIndex($i)['crc'].' | mtime: '.$zip->statIndex($i)['mtime'].' | compares_Size: '.$zip->statIndex($i)['comp_size'].' | comp_method: '.$zip->statIndex($i)['comp_method'].'<br>';
		};
	};



	function ZipAddFileFolders($zip_p,$file_p,$real_p) {
		if (is_dir($real_p)) {
			print_r(scandir($real_p));


		}
		else {
			$zip_p->addFile($real_p,$file_p);
		};
	};



	function ZipDeleteFileFolder($zip_p,$indx,$path) {//delete file or folder using ZipDeleteFileFolder('Zip array','File/Folder index','File/Folder path including name that wanted to delete')

		if (is_numeric($indx)) {
			if ($zip_p->deleteIndex($indx)) {
				$sus = '1';
				}
				else {
					$sus = '0';
				};			
			};
			if(pathinfo($path,PATHINFO_DIRNAME) || chr(92)) {
				ZipCreateDir($zip_p,pathinfo($path,PATHINFO_DIRNAME));
			};
			if (is_dir($path)) {
				for ($i=0; !empty($zip_p->statIndex($i)['name']); $i++) {
					if (strpos($zip_p->statIndex($i)['name'],$path) !== false) {
						$index_list[]=$i;
					};
				};
				for ($i=0; !empty($index_list[$i]); $i++) {
					$zip_p->deleteIndex($index_list[$i]);
				};
			};
	};

	function ZipRenameFile($zip_p,$index,$newname) {//rename using Ziprenamefile('Zip array,'File index', 'New File Name')
		if($zip_p->renameIndex($index,$newname)) {
			return ('rname_ok');
		}
		else {
			return ('rname_fail');
		};
	};

	function ZipCreateDir($zip_p,$dir_path) {//Create dir inside zip using ZipCreateDir('Zip array','Folder Path')
		if($zip_p->addEmptyDir($dir_path)) {
			return('c_dir_ok');
		} 
		else {
			return ('c_dir_fail');
		};
	};

	switch($cmd) {
		case 'c_dir':
			echo ZipCreateDir($zip,$c_dir_path);
			break;

		case 'del':
			foreach ($index as $value => $key) {
				echo ZipDeleteFileFolder($zip,$value,$key);
			};
			break;
		case 'rname':
			foreach ($index as $value => $key) {
				echo ZipRenameFile($zip,$value,$rname);
			};
			break;

		case 'a_files':
			foreach ($a_files_path as $key => $value) {
				Print_r(ZipAddFileFolders($zip,$key,$value));
			};
			break;
	};

}
else {
	switch($zip->open($ZipFileName)) {
		case ZipArchive::ER_EXISTS: 
			$ErrMsg = 'ER_EXISTS';//File already exists.
			break;

		case ZipArchive::ER_INCONS: 
			$ErrMsg = 'ER_INCONS';//Zip archive inconsistent.
			break;
                
		case ZipArchive::ER_MEMORY: 
			$ErrMsg = 'ER_MEMORY';//Malloc failure.
			break;
                
		case ZipArchive::ER_NOENT: 
			$ErrMsg = 'ER_NOENT';//No such file.
			break;
                
		case ZipArchive::ER_NOZIP: 
			$ErrMsg = 'ER_NOZIP';//Not a zip archive.
			break;
                
		case ZipArchive::ER_OPEN: 
			$ErrMsg = 'ER_OPEN';//Can't open file.
			break;
                
		case ZipArchive::ER_READ: 
			$ErrMsg = 'ER_READ';//Read error.
			break;
                
		case ZipArchive::ER_SEEK: 
			$ErrMsg = 'ER_SEEK';//Seek error.
			break;
            
		default: 
			$ErrMsg = 'Unknow_(Code:'.$rOpen.')';
			break;
	}
	die( 'ZipArchive_Error:'.$ErrMsg);
};

$zip->close();

?>
