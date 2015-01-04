<?php
$archivo = "fperurail2015.csv";

function trenes($tipo){
    $valor['31P']='AV31';//ok
    $valor['203P']='AV203';//ok
    $valor['32P']='AV32';//ok
    $valor['604P']='AV604';//ok
    $valor['33P']='EX33';//ok
    $valor['34P']='EX34';//ok
    $valor['601O']='AV601';//ok
    $valor['501O']='AV501';//ok
    $valor['301O']='AV301';//ok
    $valor['304O']='AV304';//ok
    $valor['604O']='AV604';//ok
    $valor['606O']='AV606';//ok
    $valor['83O']='EX83';
    $valor['504O']='EX504';
    return $valor[$tipo];
}

function estaciones($tipo){
    $valor['31P']='CUZPO';
    $valor['203P']='CUZPO';
    $valor['32P']='CUZPO';
    $valor['604P']='CUZPO';
    $valor['33P']='CUZPO';
    $valor['34P']='CUZPO';
    $valor['601O']='VSIOL';
    $valor['501O']='VSIOL';
    $valor['301O']='VSIOL';
    $valor['304O']='VSIOL';
    $valor['604O']='VSIOL';
    $valor['606O']='VSIOL';
    $valor['83O']='VSIOL';
    $valor['504O']='VSIOL';
    return $valor[$tipo];
}

$lines = file($archivo);

foreach ($lines as $key=>$line):
    $item=explode(';',$line);
        $newItems[$key]['nombre']=trim($item[0]);
        $newItems[$key]['fechaIda']=trim(str_replace('2012','2013', $item[1]));
        $newItems[$key]['estacionIda']=estaciones(trim($item[2]));
        $newItems[$key]['trenIda']=trenes(trim($item[2]));
        $newItems[$key]['numeroPaxIda']=trim($item[3]);
        $newItems[$key]['fechaRetorno']=trim(str_replace('2012','2013', $item[4]));
        $newItems[$key]['estacionRetorno']=estaciones(trim($item[5]));
        $newItems[$key]['trenRetorno']=trenes(trim($item[5]));
        $newItems[$key]['numeroPaxRetorno']=trim($item[6]);
        $newItems[$key]['contacto']=trim($item[7]);
        $newItems[$key]['codigo']=trim($item[8]);
        $newItems[$key]['tipoPaxIda']=trim($item[9]);
endforeach;

$items=array();
$item=array();
foreach ($newItems as  $item): ///primero pax
    if ($item['tipoPaxIda']=='P'){
        $items[$item['codigo']][]=$item;
    }
endforeach;
foreach ($newItems as  $item): ///despues guia
    if ($item['tipoPaxIda']=='G'){
        $items[$item['codigo']][]=$item;
    }
endforeach;


$newItems=array();
$itemxcodigo=array();
$item=array();
foreach ($items as $key=>$itemxcodigo):
    foreach ($itemxcodigo as  $item):
        if ($item['tipoPaxIda']=='P'){
                $newItems[$key][]=$item;
        }
    endforeach;
    foreach ($itemxcodigo as  $newkey=>$item):
        if ($item['tipoPaxIda']=='G'){
            $encontrado=0;
            if (isset ($newItems[$key])&&$newItems[$key][0]['tipoPaxIda']=='P'){
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
        echo 'INSERT INTO BLOQUEO_TREN (NUM_BLOQUEO,ANO,FEC_SALIDA,COD_SERVICIO_SALIDA, COD_ESTACION_SALIDA,FEC_RETORNO,COD_SERVICIO_RETORNO,COD_ESTACION_RETORNO,CANTIDAD,SALDO_CANTIDAD,CANTIDAD_GUIA,SALDO_CANTIDAD_GUIA,OBSERVACIONES,ESTADO,USER_CREACION,FEC_CREACION) ';
        echo "VALUES ('".$correlativochar."','2015',to_date('".$item['fechaIda']."', 'dd/mm/yyyy'),'".$item['trenIda']."','".$item['estacionIda']."',to_date('".$item['fechaRetorno']."', 'dd/mm/yyyy'),'".$item['trenRetorno']."','".$item['estacionRetorno']."',".$item['numeroPaxIda'].",".$item['numeroPaxIda'].",".$item['numeroGuia'].",".$item['numeroGuia'].",'".$item['codigo']." - ".$item['nombre']."',1,'JGOMEZ',to_date('".date('Y/m/d')."', 'yyyy/mm/dd'));<br />";
    endforeach;
endforeach;