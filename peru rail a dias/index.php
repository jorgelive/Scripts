<?php

$archivo = "fperurail2013.csv";

$lines = file($archivo);

function servicios($valor){
	return substr($valor,0,1);
}

function tipoPax($valor){
	return substr($valor,1,1);
}

function tipoRtOn($ida,$retorno){
	if($ida!=$retorno){
		return 'ON';
	}else{
		return 'RT';
	}
}


foreach ($lines as $line):
	$item=explode(';',$line);
	//print_r($item);
	
	if(!isset($newitems[$item[1]][servicios($item[2]).' '.tipoPax($item[3]).' '.tipoRtOn($item[1],$item[5])])){
		$newitems[$item[1]][servicios($item[2]).' '.tipoPax($item[3]).' '.tipoRtOn($item[1],$item[5])]=0;
	}
	$newitems[$item[1]][servicios($item[2]).' '.tipoPax($item[3]).' '.tipoRtOn($item[1],$item[5])]=$newitems[$item[1]][servicios($item[2]).' '.tipoPax($item[3]).' '.tipoRtOn($item[1],$item[5])]+(int)trim($item[8]);
endforeach;

//print_r ($newitems);

foreach ($newitems as $key => $item):
//print_r($item);
	$tipos=array('A O RT','A O ON','E O RT','E O ON','A G RT','A G ON','E G RT','E G ON');
	foreach($tipos as $tipo):
		if(!isset($item{$tipo})||$item{$tipo}==0){$item{$tipo}='';}
	endforeach;
	echo $key.';'.$item['A O RT'].';'.$item['A O ON'].';'.$item['E O RT'].';'.$item['E O ON'].';'.$item['A G RT'].';'.$item['E G RT']."<br />";
endforeach;