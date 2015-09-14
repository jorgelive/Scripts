<?php

$archivo = "fuente.csv";

$lines = file($archivo);


foreach ($lines as $nroLine => $line):
	$items[$nroLine]=explode(',',$line);
    if ($nroLine > 360){
        //break;
    }
endforeach;

//print_r ($newitems);

foreach ($items as $key => $item):
    if(!isset($newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['num_pax'])){
        $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['num_pax'] = 0;
    }
    if(!isset($newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['num_chd'])){
        $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['num_chd'] = 0;
    }
    $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['num_pax'] = $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['num_pax'] + $item[1];
    $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['num_chd'] = $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['num_chd'] + $item[2];
    $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['fecha'] = $item[4];
    $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['hora'] = $item[5];
    $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['files'][] = $item[0];
    $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['servicios'][] = $item[3];
    $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['ciudad'] = $item[6];
    $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['cod_unidad'] = $item[7];
    $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['des_unidad'] = $item[8];
    $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['placa'] = $item[9];
    $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['tipo_unidad'] = $item[10];
    $newitem[$item[4] . ' ' . $item[5] . ' ' . $item[11] . ' ' . $item[7]]['tipo_servicio'] = $item[11];


	//echo $key.';'.$item['A O RT'].';'.$item['A O ON'].';'.$item['E O RT'].';'.$item['E O ON'].';'.$item['A G RT'].';'.$item['E G RT']."<br />";
endforeach;

foreach ($newitem as $key => $value):
    echo $value['ciudad'] . ',' . $value['fecha'] . ',' . $value['hora'] . ',' . count($value['files']) . ','. $value['num_pax'] . ',' . $value['num_chd'] . ',' . implode(' | ', $value['files']) . ',' . implode(' | ', array_unique($value['servicios'])) . ',' . $value['cod_unidad'] . ',' . $value['des_unidad'] . ',' . $value['placa'] . ',' . $value['tipo_unidad'] . ',' . $value['tipo_servicio'] . "<br />"  ;
endforeach;