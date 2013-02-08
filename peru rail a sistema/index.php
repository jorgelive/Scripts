<?php
$archivo = "fperurail2013.csv";

function trenes($tipo){

	/*$valor['AV 31']='VD1';/*31
	$valor['EX 33']='BP';/*33
	$valor['AV 301']='V02';/*301
	$valor['AV 501']='V04';/*501
	$valor['AV 203']='V06';/*203
	$valor['AV 303']='V09';/*303
	$valor['AV 603']='V10';/*603
	$valor['EX 71']='B01';/*71
	$valor['AV 601']='V05';/*601
	$valor['EX 83']='B03';/*83
	$valor['EX 73']='B04';/*73
	$valor['EX 75']='B05';/*75
	$valor['EX 51']='B06';/*51
        $valor['AV 32']='VD3';/*32
	$valor['EX 34']='BP2';/*34
	$valor['AV 302']='V14';/*302
	$valor['AV 204']='V16';/*204
	$valor['AV 304']='V17';/*304
	$valor['AV 504']='V19';/*504
	$valor['AV 606']='V22';/*606
	$valor['EV 50']='B07';/*50
	$valor['EX 72']='B08';/*72
	$valor['EX 74']='B10';/*74
	$valor['AV 604']='V20';/*604
	$valor['EX 84']='B11';/*84
	$valor['EX 76']='B12';/*76
        $valor['AV 604']='';/*504*/
        
        $valor['AV 31']='AV31';/*31*/
	$valor['EX 33']='EX33';/*33*/
        $valor['AV 32']='AV32';/*32*/
	$valor['EX 34']='EX34';/*34*/
        $valor['AV 304']='AV304';/*304*/
        $valor['AV 504']='AV504';/*504*/
        $valor['AV 604']='AV604';/*604*/
        
	return $valor[$tipo];
}

function estaciones($tipo){
        $valor['AV 31']='CUZPO';/*31*/
	$valor['EX 33']='CUZPO';/*33*/
        $valor['AV 32']='CUZPO';/*32*/
	$valor['EX 34']='CUZPO';/*34*/
        $valor['AV 304']='VSIOL';/*304*/
        $valor['AV 504']='VSIOL';/*504*/
        $valor['AV 604']='VSIOL';/*604*/
	return $valor[$tipo];
}

$lines = file($archivo);

foreach ($lines as $key=>$line):
	$item=explode(';',$line);
		$newItems[$key]['nombre']=trim($item[0]);
		$newItems[$key]['fechaIda']=trim(str_replace('2012','2013', $item[1]));
                $newItems[$key]['estacionIda']=estaciones(trim($item[2]));
                $newItems[$key]['trenIda']=trenes(trim($item[2]));
                $newItems[$key]['tipoPaxIda']=trim($item[3]);
                $newItems[$key]['numeroPaxIda']=trim($item[4]);
                
		$newItems[$key]['fechaRetorno']=trim(str_replace('2012','2013', $item[5]));
                $newItems[$key]['estacionRetorno']=estaciones(trim($item[6]));
                $newItems[$key]['trenRetorno']=trenes(trim($item[6]));
                $newItems[$key]['tipoPaxRetorno']=trim($item[7]);
                $newItems[$key]['numeroPaxRetorno']=trim($item[8]);
                
                $newItems[$key]['codigo']=trim($item[10]);
                
                
endforeach;

$items=array();
$item=array();
foreach ($newItems as  $item): ///primero pax
	if ($item['tipoPaxIda']=='3OI'){
		$items[$item['codigo']][]=$item;
	}
endforeach;
foreach ($newItems as  $item): ///despues guia
	if ($item['tipoPaxIda']=='3GI'){
		$items[$item['codigo']][]=$item;
	}
endforeach;


$newItems=array();
$itemxcodigo=array();
$item=array();
foreach ($items as $key=>$itemxcodigo):
	foreach ($itemxcodigo as  $item):
		if ($item['tipoPaxIda']=='3OI'){
				$newItems[$key][]=$item;
		}
	endforeach;
	foreach ($itemxcodigo as  $newkey=>$item):
		if ($item['tipoPaxIda']=='3GI'){
			$encontrado=0;
			if (isset ($newItems[$key])&&$newItems[$key][0]['tipoPaxIda']=='3OI'){
				foreach ($newItems[$key] as $keycomparado=>$itemacomparar):
					if($itemacomparar['trenIda']==$item['trenIda']&&$itemacomparar['trenRetorno']==$item['trenRetorno']&&!isset($itemacomparar['numeroGuia'])){
					   $newItems[$key][$keycomparado]['numeroGuia']=$item['numeroPaxIda'];
					   unset ($itemxcodigo[$newkey]);
					   $encontrado=1;
					   break;
					}
				endforeach;
			}
			if ($encontrado==0){
				$item['numeroGuia']=$item['numeroPaxIda'];
				$item['numeroPaxIda']=0;
                                $item['numeroPaxRetorno']=0;
				$newItems[$key][]=$item;
			}
		}
	endforeach;
endforeach;

$itemxcodigo=array();
$item=array();
$correlativo=1;
foreach($newItems as $itemxcodigo):
	foreach ($itemxcodigo as $item):
		$correlativochar=str_pad($correlativo,7, 0, STR_PAD_LEFT); 
		$correlativo++;
		if(!isset($item['numeroGuia'])){$item['numeroGuia']=0;}
		echo 'INSERT INTO BLOQUEO_TREN (NUM_BLOQUEO,ANO,FEC_SALIDA,COD_SERVICIO_SALIDA, COD_ESTACION_SALIDA,FEC_RETORNO,COD_SERVICIO_RETORNO,COD_ESTACION_RETORNO,CANTIDAD,SALDO_CANTIDAD,CANTIDAD_GUIA,SALDO_CANTIDAD_GUIA,OBSERVACIONES,ESTADO,USER_CREACION,FEC_CREACION)';
		echo "VALUES ('".$correlativochar."','2013',to_date('".$item['fechaIda']."', 'dd/mm/yyyy'),'".$item['trenIda']."','".$item['estacionIda']."',to_date('".$item['fechaRetorno']."', 'dd/mm/yyyy'),'".$item['trenRetorno']."','".$item['estacionRetorno']."',".$item['numeroPaxIda'].",".$item['numeroPaxIda'].",".$item['numeroGuia'].",".$item['numeroGuia'].",'".$item['codigo']." - ".$item['nombre']."',1,'JGOMEZ',to_date('".date('Y/m/d')."', 'yyyy/mm/dd'));<br />";
	endforeach;
endforeach;