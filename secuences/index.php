
<?php

$tablename[]="activos";
$tablename[]="activos_usuarios";
$tablename[]="activosestados";
$tablename[]="activosspecs";
$tablename[]="activosspecsitems";
$tablename[]="activostipos";
$tablename[]="areas";
$tablename[]="cuentas";
$tablename[]="cuentas_usuarios";
$tablename[]="oficinas";
$tablename[]="proveedores";
$tablename[]="requerimientos";
$tablename[]="requerimientos_usuarios";
$tablename[]="requerimientosestados";
$tablename[]="requerimientositems";
$tablename[]="requerimientostipos";
$tablename[]="roles";
$tablename[]="roles_usuarios";
$tablename[]="servicios";
$tablename[]="serviciosacciones";
$tablename[]="serviciosestados";
$tablename[]="serviciostipo";
$tablename[]="usuarios";

foreach ($tablename as $table){

echo 'create sequence '.$table.'_sq</br>';
echo '&nbsp;&nbsp;start with 1 </br>';
echo '&nbsp;&nbsp;increment by 1 </br>';
echo '&nbsp;&nbsp;nomaxvalue; </br>';
echo '</br>';
echo 'create trigger '.$table.'_tr </br>';
echo '&nbsp;&nbsp;before insert on "'.$table.'" </br>';
echo '&nbsp;&nbsp;for each row </br>';
echo '&nbsp;&nbsp;begin </br>';
echo '&nbsp;&nbsp;select '.$table.'_sq.nextval into :new."id" from dual; </br>';
echo '&nbsp;&nbsp;end; </br>';
echo '/';
echo '</br>';
echo '</br>';
}