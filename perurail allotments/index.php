<?php 
$correlativo=3222;
$dias=275;


function trenes($tipo){
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

function printSQL($correlativo,$dias,$numero,$numeroGuia,$trenIda,$trenRetorno){

	for ($i=0;$i<$dias;$i++){
	
		$item['numeroPaxIda']=$numero;
		$item['numeroGuia']=$numeroGuia;
		$item['trenIda']=trenes($trenIda);
		$item['estacionIda']=estaciones($trenIda);
		$item['trenRetorno']=trenes($trenRetorno);
		$item['estacionRetorno']=estaciones($trenRetorno);
		
		$item['fechaIda'] = date("d/m/Y", strtotime("2013-04-01+".$i."day"));
		$item['fechaRetorno'] = $item['fechaIda'];
		$correlativochar=str_pad($correlativo,7, 0, STR_PAD_LEFT); 
	
		echo 'INSERT INTO BLOQUEO_TREN (NUM_BLOQUEO,ANO,FEC_SALIDA,COD_SERVICIO_SALIDA, COD_ESTACION_SALIDA,FEC_RETORNO,COD_SERVICIO_RETORNO,COD_ESTACION_RETORNO,CANTIDAD,SALDO_CANTIDAD,CANTIDAD_GUIA,SALDO_CANTIDAD_GUIA,OBSERVACIONES,ESTADO,USER_CREACION,FEC_CREACION)';
			echo "VALUES ('".$correlativochar."','2013',to_date('".$item['fechaIda']."', 'dd/mm/yyyy'),'".$item['trenIda']."','".$item['estacionIda']."',to_date('".$item['fechaRetorno']."', 'dd/mm/yyyy'),'".$item['trenRetorno']."','".$item['estacionRetorno']."',".$item['numeroPaxIda'].",".$item['numeroPaxIda'].",".$item['numeroGuia'].",".$item['numeroGuia'].",'Allotment - ".$item['fechaIda']."',1,'JGOMEZ',to_date('".date('Y/m/d')."', 'yyyy/mm/dd'));<br />";
		$correlativo++;	
			
	}
	return $correlativo;
}

$correlativo=printSQL($correlativo,$dias,18,0,'AV 31','AV 32');
$correlativo=printSQL($correlativo,$dias,10,0,'EX 33','EX 34');