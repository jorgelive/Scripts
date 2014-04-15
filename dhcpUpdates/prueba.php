<?php

function obtenerA($nombre){
    $dnss=dns_get_record($nombre.'.vipac.com', DNS_A);
    $result=array();
    foreach ($dnss as $nro => $dns):
    $result{$nro}['nombre']=$dns['host'];
    $result{$nro}['ip']=$dns['ip'];
    endforeach;
    return $result;
}

$a = obtenerA('mloayza-lp2');
$b = obtenerA('jgomez-mp1');

$a=array_map("unserialize", array_unique(array_map("serialize", array_merge($a,$b))));
print_r($a);
