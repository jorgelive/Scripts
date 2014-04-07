<?php

$archivo = "datos.csv";

$lines = file($archivo);

function encontrarCodDestinoBiblia($ruta){

    $rutaDestino['OLL-MAPI']='SAL MP';
    $rutaDestino['POROY-MAPI']='SAL MP';
    $rutaDestino['CUS-MAPI']='SAL MP';
    $rutaDestino['MAPI-CUS']='RET MP';
    $rutaDestino['MAPI-OLL']='RET MP';
    $rutaDestino['MAPI-POROY']='RET MP';
    if (isset($rutaDestino[str_replace(' ', '', $ruta)])){
        return $rutaDestino[str_replace(' ', '', $ruta)];

    }else {
        return 'SAL MP';
    }
}


foreach ($lines as $nro => $line):
    $tmp=explode(';',$line);
    $newitems[$nro]['cod_contacto']=trim('CUZPER');
    $newitems[$nro]['periodo']=trim($tmp[0]);
    $newitems[$nro]['numero_ticket']=trim($tmp[1]);
    $newitems[$nro]['monto']=trim($tmp[2]);
    $newitems[$nro]['documento']=trim($tmp[3]);
    $newitems[$nro]['detalle']=trim($tmp[4]);
    $newitems[$nro]['fecha_emision']=trim($tmp[5]);

    if (substr($newitems[$nro]['detalle'], 0,2 )=='20'){
        if (substr($newitems[$nro]['detalle'],4,1)==' '){
            $tempProcessFileDetalle=explode (' ',$newitems[$nro]['detalle'],4);
            $tempProcessFileDetalle=$tempProcessFileDetalle[0].$tempProcessFileDetalle[1].filter_var($tempProcessFileDetalle[2], FILTER_SANITIZE_NUMBER_INT);
            $tempProcessFileDetalle=explode (' ',$newitems[$nro]['detalle'],2);

        }else{
            $tempProcessFileDetalle=explode (' ',$newitems[$nro]['detalle'],2);


        }

        $tempProcessFile=explode ('-',$tempProcessFileDetalle[0],2);

    }

    if (substr($newitems[$nro]['detalle'], 0,2 )!='20'||!isset($tempProcessFile[1])){
        $tempProcessFile=explode ('-',$tmp[6],2);

    }
    //if (!isset($tempProcessFile[1])){
    //    echo $tempProcessFileDetalle;

//    }

    $newitems[$nro]['ano_fisico']=trim($tempProcessFile[0]);
    $newitems[$nro]['num_file_fisico']=trim(str_pad(substr($tempProcessFile[1], -5),10, 0, STR_PAD_LEFT));

    if (!isset($tempProcessFile[1])){
        print_r ($tempProcessFile);

    }
    $newitems[$nro]['ruta']=trim($tmp[7]);
    $newitems[$nro]['fecha_viaje']=trim($tmp[8]);
    $newitems[$nro]['cod_destino_biblia']=encontrarCodDestinoBiblia(trim($tmp[7]));
endforeach;

//print_r ($newitems);

foreach ($newitems as $key => $item):
//print_r($item);
		//echo $key.';'.$item['A O RT'].';'.$item['A O ON'].';'.$item['E O RT'].';'.$item['E O ON'].';'.$item['A G RT'].';'.$item['E G RT']."<br />";
?>
insert
    into upload_tickets
    (
        COD_CONTACTO,
        NUMERO_TICKET,
        FECHA_EMISION,
        DETALLE,
        MONTO,
        RUTA,
        FECHA_VIAJE,
        COD_DESTINO_BIBLIA,
        ANO_FISICO,
        NUM_FILE_FISICO,
        DOCUMENTO,
        PERIODO
    )
    values (
        <?php echo "'".$item["cod_contacto"]."'";?>,
        <?php echo $item["numero_ticket"];?>,
        <?php echo "to_date('".$item["fecha_emision"]."','DD/MM/YY')";?>,
        <?php echo "'".$item["detalle"]."'";?>,
        <?php echo $item["monto"];?>,
        <?php echo "'".$item["ruta"]."'";?>,
        <?php echo "to_date('".$item["fecha_viaje"]."','DD/MM/YY')";?>,
        <?php echo "'".$item["cod_destino_biblia"]."'";?>,
        <?php echo $item["ano_fisico"];?>,
        <?php echo "'".$item["num_file_fisico"]."'";?>,
        <?php echo "'".$item["documento"]."'";?>,
        <?php echo "'".$item["periodo"]."'";?>
    );
    <BR>
<?php
endforeach;