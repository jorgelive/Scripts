<?php 
$correlativo=5211;

function trenes($tipo){
    $valor['EX 33']='EX33';/*33*/
    $valor['EX 34']='EX34';/*34*/
    $valor['EX 83']='EX83';/*33*/
    $valor['EX 504']='EX504';/*34*/
    $valor['AV 31']='AV31';/*31*/
    $valor['AV 32']='AV32';/*32*/
    $valor['AV 304']='AV304';/*304*/
    $valor['AV 504']='AV504';/*504*/
    $valor['AV 601']='AV601';/*604*/
    $valor['AV 604']='AV604';/*604*/
	return $valor[$tipo];
}

function estaciones($tipo){
    $valor['EX 33']='CUZPO';/*33*/
    $valor['EX 34']='CUZPO';/*34*/
    $valor['EX 83']='VSIOL';/*33*/
    $valor['EX 504']='VSIOL';/*34*/
    $valor['AV 31']='CUZPO';/*31*/
    $valor['AV 32']='CUZPO';/*32*/
    $valor['AV 304']='VSIOL';/*304*/
    $valor['AV 504']='VSIOL';/*504*/
    $valor['AV 601']='VSIOL';/*604*/
    $valor['AV 604']='VSIOL';/*604*/
	return $valor[$tipo];
}

function printSQLFechas($correlativo,$fechaInicio,$fechaFin,$numero,$numeroGuia,$trenIda,$trenRetorno){
    $hoy = new DateTime();
    $hoyString = $hoy->format('d/m/Y');
    $fecha1=new DateTime($fechaInicio);
    $fecha2=new DateTime($fechaFin);
    $diferencia=$fecha1->diff($fecha2);
    for ($i=0;$i<$diferencia->days + 1;$i++){

        $item['numeroPaxIda']=$numero;
        $item['numeroGuia']=$numeroGuia;
        $item['trenIda']=trenes($trenIda);
        $item['estacionIda']=estaciones($trenIda);
        $item['trenRetorno']=trenes($trenRetorno);
        $item['estacionRetorno']=estaciones($trenRetorno);

        $item['fechaIda'] = $fecha1->format('d/m/Y');
        $item['fechaRetorno'] = $item['fechaIda'];
        $correlativochar=str_pad($correlativo,7, 0, STR_PAD_LEFT);

        echo 'INSERT INTO BLOQUEO_TREN (NUM_BLOQUEO,ANO,FEC_SALIDA,COD_SERVICIO_SALIDA, COD_ESTACION_SALIDA,FEC_RETORNO,COD_SERVICIO_RETORNO,COD_ESTACION_RETORNO,CANTIDAD,SALDO_CANTIDAD,CANTIDAD_GUIA,SALDO_CANTIDAD_GUIA,OBSERVACIONES,ESTADO,USER_CREACION,FEC_CREACION) ';
        echo "VALUES ('".$correlativochar."','2015',to_date('".$item['fechaIda']."', 'dd/mm/yyyy'),'".$item['trenIda']."','".$item['estacionIda']."',to_date('".$item['fechaRetorno']."', 'dd/mm/yyyy'),'".$item['trenRetorno']."','".$item['estacionRetorno']."',".$item['numeroPaxIda'].",".$item['numeroPaxIda'].",".$item['numeroGuia'].",".$item['numeroGuia'].",'Allotment - ".$item['fechaIda']."',1,'JGOMEZ',to_date('".$hoyString."', 'dd/mm/yyyy'));<br />";
        $correlativo++;
        $fecha1->add(new DateInterval('P1D'));

    }
    return $correlativo;
}

$correlativo=printSQLFechas($correlativo,'2015-03-01','2015-04-30',10,0,'AV 31','AV 32');
$correlativo=printSQLFechas($correlativo,'2015-05-01','2015-12-31',18,0,'AV 31','AV 32');
$correlativo=printSQLFechas($correlativo,'2015-03-01','2015-04-30',8,0,'EX 33','EX 34');
$correlativo=printSQLFechas($correlativo,'2015-05-01','2015-12-31',10,0,'EX 33','EX 34');
$correlativo=printSQLFechas($correlativo,'2015-03-01','2015-12-31',10,0,'AV 601','AV 304');
$correlativo=printSQLFechas($correlativo,'2015-03-01','2015-12-31',10,0,'EX 83','EX 504');
//$correlativo=printSQLFechas($correlativo,$dias,10,0,'EX 33','EX 34');