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
//Configuracion::escribir('dhcp.log', '/Volumes/Archivo/Desarrollo/Web/Scripts/dhcpUpdates/actualizacionLog.log');
Configuracion::escribir('dhcp.log', '/etc/dhcp/actualizacionDHCP.log');
Configuracion::escribir('dhcp.servidor', 'vipacfs3');
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
        $this->enviado['nombre']=preg_replace('/\s+/', '', $enviado[2]);
        $macArray=explode(':',$enviado[3]);
        foreach ($macArray as $macComponente):
            $macProcesado[]=str_pad($macComponente,2, 0, STR_PAD_LEFT);
        endforeach;
        $this->enviado['mac']=implode(':',$macProcesado);
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
            $this->query("INSERT INTO dhcp (mac, nombre, ip) VALUES (:mac,:nombre,:ip)", array(':mac'=>$this->enviado['mac'],':nombre'=>$this->enviado['nombre'],':ip'=>$this->enviado['ip']));
        }elseif($almacenado['ip']!=$this->enviado['ip']||$almacenado['nombre']!=$this->enviado['nombre']){
            $this->query("UPDATE dhcp SET nombre=:nombre, ip=:ip WHERE mac=:mac", array(':mac'=>$this->enviado['mac'],':nombre'=>$this->enviado['nombre'],':ip'=>$this->enviado['ip']));
        }

        $a=$this->obtenerA($this->enviado['nombre']);
        $coleccionIp[]=$this->enviado['ip'];
        if(!empty($almacenado)){
            $a2=$this->obtenerA($almacenado['nombre']);
            $a=array_map("unserialize", array_unique(array_map("serialize", array_merge($a,$a2))));
            $coleccionIp[]=$almacenado['ip'];
        }

        $aExiste=false;
        $ptrExiste=false;
        foreach ($a as $proceso):
            if($proceso['ip']==$this->enviado['ip']&&$proceso['nombre']==$this->enviado['nombre'].'.'.$this->dhcp['dominio']&&$this->enviado['accion']!='delete'){
                $aExiste=true;
                file_put_contents($this->dhcp['log'], "No se borra ni se agrega: ".$proceso['ip'].", del registro A, no cambió la infomación\n", FILE_APPEND | LOCK_EX);
            }else{
                if(isset($otro)&&$proceso['ip']!=$otro['ip']){
                    $this->escribirRegistroA('delete',$proceso['nombre'],$proceso['ip']);
                }else{
                    file_put_contents($this->dhcp['log'], "No se borra: ".$proceso['ip'].", pertenece a otra interface\n", FILE_APPEND | LOCK_EX);
                    file_put_contents($this->dhcp['log'], "No se adiciona a la búsqueda PTR, pertenece a otra interface\n", FILE_APPEND | LOCK_EX);
                }
                file_put_contents($this->dhcp['log'], "Se agrega: ".$proceso['ip']." a la búsqueda PTR\n", FILE_APPEND | LOCK_EX);
                $coleccionIp[]=$proceso['ip'];
            }
        endforeach;
        $coleccionIp=array_unique($coleccionIp);
        foreach ($coleccionIp as $ip):
            $ptr=$this->obtenerPtr($ip);
            foreach ($ptr as $proceso):
                //file_put_contents($this->dhcp['log'], "DEBUG proceso: ".$proceso['nombre']."\n", FILE_APPEND | LOCK_EX);
                //file_put_contents($this->dhcp['log'], "DEBUG enviado: ".$this->enviado['nombre'].'.'.$this->dhcp['dominio']."\n", FILE_APPEND | LOCK_EX);
                if($proceso['ip']==$this->enviado['ip']&&$proceso['nombre']==$this->enviado['nombre'].'.'.$this->dhcp['dominio']&&$this->enviado['accion']!='delete'){
                    $ptrExiste=true;
                    file_put_contents($this->dhcp['log'], "No se borra ni se agrega: ".$this->enviado['nombre'].", del registro PTR, no cambió la infomación\n", FILE_APPEND | LOCK_EX);
                }else{
                    if(isset($otro)&&$proceso['nombre']!=$otro['nombre'].'.'.$this->dhcp['dominio']){
                        //file_put_contents($this->dhcp['log'], "DEBUG proceso: ".$proceso['nombre']."\n", FILE_APPEND | LOCK_EX);
                        //file_put_contents($this->dhcp['log'], "DEBUG otro: ".$otro['nombre'].'.'.$this->dhcp['dominio']."\n", FILE_APPEND | LOCK_EX);
                        $this->escribirRegistroPTR('delete',$proceso['nombre'],$proceso['ip']);
                    }else{
                        file_put_contents($this->dhcp['log'], "No se borra: ".$proceso['nombre'].", pertenece a otra interface\n", FILE_APPEND | LOCK_EX);
                    }
                }
            endforeach;
        endforeach;

        if($aExiste===false&&$this->enviado['accion']!='delete'){
            $this->escribirRegistroA('add',$this->enviado['nombre'].'.'.$this->dhcp['dominio'],$this->enviado['ip']);
        }else{
            file_put_contents($this->dhcp['log'], "No se agrega, el registro A ya existe, o se esta en eliminación\n", FILE_APPEND | LOCK_EX);
        }

        if($ptrExiste===false&&$this->enviado['accion']!='delete'){
            $this->escribirRegistroPTR('add',$this->enviado['nombre'].'.'.$this->dhcp['dominio'],$this->enviado['ip']);
        }else{
            file_put_contents($this->dhcp['log'], "No se agrega, el registro PTR ya existe, o se esta en eliminación\n", FILE_APPEND | LOCK_EX);
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

if (!empty($argv[1])){
    $actualizacion = new actualizacion($argv[1]);
    $actualizacion->procesar();
}