<?php
$archivo = "fincarail2012.csv";

function trenes($tren){
	$valor['41']='I41';
	$valor['44']='I44';
	return $valor[$tren];
}

function estaciones($estacion){
	$valor['MAPI']='AGCAC';
	$valor['OLLA']='VSIOL';
	return $valor[$estacion];
}
$lines = file($archivo);
//print_r($lines);

foreach ($lines as $key => $line):
	$item=explode(';',$line);
	$newitems[$key]['numero']=trim($item[0]);
	$newitems[$key]['fecha']=trim($item[1]);/////
	$newitems[$key]['trenIda']=trenes(trim($item[3]));
	$newitems[$key]['estacionIda']=explode(' - ',$item[4]);
	$newitems[$key]['estacionIda']=estaciones(trim($newitems[$key]['estacionIda'][0]));
	$newitems[$key]['trenRetorno']=trenes(trim($item[5]));
	$newitems[$key]['estacionRetorno']=explode(' - ',$item[6]);
	$newitems[$key]['estacionRetorno']=estaciones(trim($newitems[$key]['estacionRetorno'][1]));
	$newitems[$key]['codigo']=trim($item[2]);
	$newitems[$key]['nombre']="Inca Rail";
	
endforeach;
$correlativo=2749;
foreach($newitems as $item):
	//$item['numero']=8;//para separar
	$correlativochar=str_pad($correlativo,7, 0, STR_PAD_LEFT); 
	$correlativo++;
	if(isset($item['numero'])){$numero=$item['numero'];}else{$numero=0;}
	if(isset($item['numeroguia'])){$numeroguia=$item['numeroguia'];}else{$numeroguia=0;}
		
	echo 'INSERT INTO BLOQUEO_TREN (NUM_BLOQUEO,ANO,FEC_SALIDA,COD_SERVICIO_SALIDA, COD_ESTACION_SALIDA,FEC_RETORNO,COD_SERVICIO_RETORNO,COD_ESTACION_RETORNO,CANTIDAD,SALDO_CANTIDAD,CANTIDAD_GUIA,SALDO_CANTIDAD_GUIA,OBSERVACIONES,ESTADO,USER_CREACION,FEC_CREACION)';
	echo "VALUES ('".$correlativochar."','2012',to_date('".$item['fecha']."', 'dd/mm/yyyy'),'".$item['trenIda']."','".$item['estacionIda']."',to_date('".$item['fecha']."', 'dd/mm/yyyy'),'".$item['trenRetorno']."','".$item['estacionRetorno']."',".$item['numero'].",".$item['numero'].",0,0,'".$item['codigo']." - ".$item['nombre']."',1,'JGOMEZ',to_date('".date('Y-m-d')."', 'yyyy-mm-dd'));<br />";

endforeach;

?>