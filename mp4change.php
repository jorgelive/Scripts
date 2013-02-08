<?php
//format 2012-12-22 24-00-00

$carpeta='/Volumes/Archivos/Fotos';


function processdir($carpeta){

	$allowed = array('mp4','jpg');
	$carpetaRes = opendir($carpeta);
	
	if (empty($carpetaRes)) die;
	
    while (false !== ($archivo = readdir($carpetaRes))) {
        if (!is_dir($carpeta.'/'.$archivo)){
			$archivoArray=explode(".",$archivo);
			if(in_array($archivoArray[1],$allowed)){
				$archivoArray=explode("(",$archivoArray[0]); //en caso de multiples
				$timestamp=str_replace("-", "", $archivoArray[0]);
				$timestamp=str_replace(" ", "", $timestamp);
				$timestamp=implode('.',str_split($timestamp,12));
				echo "touch -t ".$timestamp." \"".$carpeta."/".$archivo."\"" ;
				echo "\n";
				exec("touch -t ".$timestamp." \"".$carpeta."/".$archivo."\"");
			}
		}elseif (is_dir($carpeta.'/'.$archivo)&&($archivo!="..")&&($archivo!=".")){
			echo $archivo."\n";
			processdir($carpeta.'/'.$archivo);
		}
    }
}

processdir($carpeta);