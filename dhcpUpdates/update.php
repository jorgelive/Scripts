<?php

class Configuracion
{
    static $confArray;

    public static function leer($nombre)
    {
        return self::$confArray[$nombre];
    }

    public static function escribir($nombre, $valor)
    {
        self::$confArray[$nombre] = $valor;
    }

}

Configuracion::escribir('conexion.servidor', 'wpad.vipac.com');
Configuracion::escribir('conexion.base', 'infraestructura');
Configuracion::escribir('conexion.usuario', 'vipacuser');
Configuracion::escribir('conexion.contra', 'welcom3');
Configuracion::escribir('dhcp.log', '/Volumes/Archivo/Desarrollo/Web/Scripts/dhcpUpdates/actualizacionLog.log');
//Configuracion::escribir('dhcp.log', '/etc/dhcp/actualizacionLog.log');
Configuracion::escribir('dhcp.servidor', 'vipacfs2');
Configuracion::escribir('dhcp.dominio', 'vipac.com');

class actualizacion
{
    private $dhcp;
    private $conexion;
    private $enviado=array();

    function __construct($enviado)
    {
        $enviado=explode('|', $enviado);
        $this->enviado['accion']=$enviado[0];
        $this->enviado['ip']=$enviado[1];
        $this->enviado['nombre']=$enviado[2];
        $this->enviado['mac']=$enviado[3];
        $this->conexion['servidor']=Configuracion::leer('conexion.servidor');
        $this->conexion['base']=Configuracion::leer('conexion.base');
        $this->conexion['usuario']=Configuracion::leer('conexion.usuario');
        $this->conexion['contra']=Configuracion::leer('conexion.contra');
        $this->dhcp['log']=Configuracion::leer('dhcp.log');
        $this->dhcp['servidor']=Configuracion::leer('dhcp.servidor');
        $this->dhcp['dominio']=Configuracion::leer('dhcp.dominio');
    }

    private function query($sql,$variables,$tipo="t"){
        try {
            $conexion = new PDO("mysql:host=".$this->conexion['servidor'].";dbname=".$this->conexion['base'],$this->conexion['usuario'],$this->conexion['contra']);
        } catch (PDOException $e) {
            return 'la Conexion fallo: ' . $e->getMessage();
        }
        $q = $conexion->prepare($sql);
        $q->execute($variables);
        if (strtoupper(substr($sql,0,6))=="SELECT"&&$tipo=='t'){
            return $q->fetchAll(PDO::FETCH_ASSOC);
        }
        if (strtoupper(substr($sql,0,6))=="SELECT"&&$tipo=='u'){
            return $q->fetch(PDO::FETCH_ASSOC);
        }
        return $q;
    }
    
    private function obtenerPtr($ip){
        $ip=explode('.',$ip);
        $ip=array_reverse($ip);
        $ip=implode('.',$ip);
        $dnss=dns_get_record($ip.".in-addr.arpa.", DNS_PTR);
        $result=array();
        foreach ($dnss as $nro => $dns):
            $zona=explode('.',$dns['host'],5);
            unset($zona[4]);
            $zona=array_reverse($zona);
            $result{$nro}['ip']=implode('.',$zona);
            $result{$nro}['nombre']=$dns['target'];
        endforeach;
        return $result;
    }
    
    private function obtenerA($nombre){
        $dnss=dns_get_record($nombre.'.'.$this->dhcp['dominio'], DNS_A);
        $result=array();
        foreach ($dnss as $nro => $dns):
            $result{$nro}['nombre']=$dns['host'];
            $result{$nro}['ip']=$dns['ip'];
        endforeach;
        return $result;
    }

    public function procesar(){

        file_put_contents($this->dhcp['log'], "\n------------------".date('Y-m-d H:i:s')." Comenzando proceso----------------\n", FILE_APPEND | LOCK_EX);
        file_put_contents($this->dhcp['log'], "Enviado: ".$this->enviado['mac'].'|'.$this->enviado['nombre'].'|'.$this->enviado['ip']."\n", FILE_APPEND | LOCK_EX);
        file_put_contents($this->dhcp['log'], "La accion es: ".$this->enviado['accion']."\n", FILE_APPEND | LOCK_EX);
        $almacenado=$this->query("SELECT mac,nombre,ip FROM dhcp WHERE mac = :mac",array(':mac'=>$this->enviado['mac']),'u');
        if (!empty($almacenado)){
            file_put_contents($this->dhcp['log'], 'En base de datos: '.implode('|',$almacenado)."\n", FILE_APPEND | LOCK_EX);
        }else{
            file_put_contents($this->dhcp['log'], "No existe registro para la interface en la base de datos\n", FILE_APPEND | LOCK_EX);
        }
		 $otro=$this->query("SELECT mac,nombre,ip FROM dhcp WHERE mac <> :mac AND nombre = :nombre" ,array(':mac'=>$this->enviado['mac'],':nombre'=>$this->enviado['nombre']),'u');
        if (!empty($otro)){
            file_put_contents($this->dhcp['log'], 'En base de datos, de otra interface: '.implode('|',$otro)."\n", FILE_APPEND | LOCK_EX);
        }else{
            file_put_contents($this->dhcp['log'], "No existe registro para otra interface en la base de datos\n", FILE_APPEND | LOCK_EX);
        }
        if(empty($almacenado)){
            $insert=$this->query("INSERT INTO dhcp (mac, nombre, ip) VALUES (:mac,:nombre,:ip)", array(':mac'=>$this->enviado['mac'],':nombre'=>$this->enviado['nombre'],':ip'=>$this->enviado['ip']));
        }elseif($almacenado['ip']!=$this->enviado['ip']||$almacenado['nombre']!=$this->enviado['nombre']){
			 $update=$this->query("UPDATE dhcp SET nombre=:nombre, ip=:ip WHERE mac=:mac", array(':mac'=>$this->enviado['mac'],':nombre'=>$this->enviado['nombre'],':ip'=>$this->enviado['ip']));
        }
        
        if(!empty($almacenado)){ // modificacion o eliminacion
            if($almacenado['ip']!=$this->enviado['ip']||$almacenado['nombre']!=$this->enviado['nombre']){
                $a=$this->obtenerA($almacenado['nombre']);
                foreach ($a as $proceso):
                    if($proceso['ip']!=$otro['ip']){
                        $this->escribirRegistroA('delete',$proceso['nombre'],$proceso['ip']);
                    }else{
                        file_put_contents($this->dhcp['log'], "No se borra: ".$proceso['ip'].", pertenece a otra interface\n", FILE_APPEND | LOCK_EX);    
                    }
                endforeach;
                $ptr=$this->obtenerPtr($almacenado['ip']);
                foreach ($ptr as $proceso):
                    $this->escribirRegistroPTR('delete',$proceso['nombre'],$proceso['ip']);
                endforeach;
                $this->escribirRegistroA('add',$this->enviado['nombre'].'.'.$this->dhcp['dominio'],$this->enviado['ip']);
                $this->escribirRegistroPTR('add',$this->enviado['nombre'].'.'.$this->dhcp['dominio'],$this->enviado['ip']);
            }
            else{
                if ($this->enviado['accion']=='delete'){
                    $a=$this->obtenerA($this->enviado['nombre']);
                    foreach ($a as $proceso):
                        if($proceso['ip']!=$otro['ip']){
                            $this->escribirRegistroA('delete',$proceso['nombre'],$proceso['ip']);
                        }else{
                            file_put_contents($this->dhcp['log'], "No se borra: ".$proceso['ip'].", pertenece a otra interface\n", FILE_APPEND | LOCK_EX);
                        }
                    endforeach;
                    $ptr=$this->obtenerPtr($this->enviado['ip']);
                    foreach ($ptr as $proceso):
                        $this->escribirRegistroPTR('delete',$proceso['nombre'],$proceso['ip']);
                    endforeach;
                       
                }else{
                    file_put_contents($this->dhcp['log'], "Nada que hacer, no cambio la información\n", FILE_APPEND | LOCK_EX);
                }
            }
        }else{// agregado o eliminacion o no existente en la base de datos
            $a=$this->obtenerA($this->enviado['nombre']);
            $aExiste=false;
            $ptrExiste=false;
            foreach ($a as $proceso):
                if($proceso['ip']==$this->enviado['ip']&&$proceso['nombre']==$this->enviado['nombre'].'.'.$this->dhcp['dominio']&&$this->enviado['accion']!='delete'){
                    $aExiste=true;    
                }else{
                    if($proceso['ip']!=$otro['ip']){
                        $this->escribirRegistroA('delete',$proceso['nombre'],$proceso['ip']);
                    }{
                        file_put_contents($this->dhcp['log'], "No se borra: ".$proceso['ip'].", pertenece a otra interface\n", FILE_APPEND | LOCK_EX);    
                    }
                }
            endforeach;
            $ptr=$this->obtenerPtr($this->enviado['ip']);
            foreach ($ptr as $proceso):
                if($proceso['ip']==$this->enviado['ip']&&$proceso['nombre']==$this->enviado['nombre'].'.'.$this->dhcp['dominio']&&$this->enviado['accion']!='delete'){
                    $ptrExiste=true;    
                }else{
                    $this->escribirRegistroPTR('delete',$proceso['nombre'],$proceso['ip']);
                }
            endforeach;
            
            if($aExiste===false&&$this->enviado['accion']!='delete'){
                $this->escribirRegistroA('add',$this->enviado['nombre'].'.'.$this->dhcp['dominio'],$this->enviado['ip']);
            }
            
            if($ptrExiste===false&&$this->enviado['accion']!='delete'){
                $this->escribirRegistroPTR('add',$this->enviado['nombre'].'.'.$this->dhcp['dominio'],$this->enviado['ip']);
            }
        }      
        if($this->enviado['accion']=='delete'){
            $this->query("DELETE FROM dhcp WHERE mac=:mac", array(':mac'=>$this->enviado['mac']));
        }
        file_put_contents($this->dhcp['log'], "------------------".date('Y-m-d H:i:s')." Fin de proceso----------------\n", FILE_APPEND | LOCK_EX);

    }
    private function escribirRegistroA($accion,$nombre,$ip){

        $nombre=explode('.',$nombre,2);
        $texto = "samba-tool dns ".$accion." ".$this->dhcp['servidor'].".".$this->dhcp['dominio']." ".$nombre[1]." ".$nombre[0]." A ".$ip." -k yes";
        file_put_contents($this->dhcp['log'], $texto."\n", FILE_APPEND | LOCK_EX);
        exec($texto, $datos, $retorno);
        if ($retorno == 0) {
            foreach ($datos as $linea) {
                file_put_contents($this->dhcp['log'], $linea."\n", FILE_APPEND | LOCK_EX);
            }
        } else {
            file_put_contents($this->dhcp['log'], "Error de Ejecución\n", FILE_APPEND | LOCK_EX);
        }
    }

    private function escribirRegistroPTR($accion,$nombre,$ip){

        $ip=explode('.',$ip);
        $oct4=$ip[3];
        unset($ip[3]);
        $ip=array_reverse($ip);
        $ip=implode('.',$ip).".in-addr.arpa";
        $texto = "samba-tool dns ".$accion." ".$this->dhcp['servidor'].".".$this->dhcp['dominio']." ".$ip." ".$oct4." PTR ".$nombre." -k yes";
        file_put_contents($this->dhcp['log'], $texto."\n", FILE_APPEND | LOCK_EX);
        exec($texto, $datos, $retorno);
        if ($retorno == 0) {
            foreach ($datos as $linea) {
                file_put_contents($this->dhcp['log'], $linea."\n", FILE_APPEND | LOCK_EX);
            }
        } else {
            file_put_contents($this->dhcp['log'], "Error de Ejecución\n", FILE_APPEND | LOCK_EX);
        }
        
        

    }
}




$actualizacion = new actualizacion('add|10.10.12.80|jgomez-mp1|gs:33:ss:ss');
$actualizacion->procesar();