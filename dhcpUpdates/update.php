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
Configuracion::escribir('conexion.puerto', '5432');
Configuracion::escribir('conexion.base', 'infraestructura');
Configuracion::escribir('conexion.usuario', 'vipacuser');
Configuracion::escribir('conexion.contra', 'welcom3');
Configuracion::escribir('dhcp.log', '/etc/dhcp/actualizacionLog.log');
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

    private function query($sql,$variables){
        try {
            $conexion = new PDO("mysql:host=$this->conexion['servidor'];dbname=$this->conexion['base']",$this->conexion['usuario'],$this->conexion['contra']);
        } catch (PDOException $e) {
            return 'la Conexion fallo: ' . $e->getMessage();
        }
        $q = $conexion->prepare($sql);
        $q->execute($variables);
        if (strtoupper(substr($sql,0,6))=="SELECT"){
            return $q->fetchAll();

        }
        return $q;
    }

    public function procesar(){

        $almacenado=$this->query("SELECT mac,host,ip FROM dhcp WHERE mac = ':mac'",array(':mac'=>$this->enviado['mac']));
        print_r($almacenado);
        $otro=$this->query("SELECT mac,host,ip FROM dhcp WHERE mac <> ':mac' AND host = ':host'" ,array(':mac'=>$this->enviado['mac'],':host'=>$this->enviado['host']));
        if(empty($almacenado)){
            $this->query("INSERT INTO dhcp (mac, host, host) VALUES (':mac',':host',':ip')", array(':mac'=>$this->enviado['mac'],':host'=>$this->enviado['host'],':ip'=>$this->enviado['ip']));
        }elseif($almacenado['ip']!=$this->enviado['ip']||$almacenado['ip']!=$this->enviado['ip']){
            $this->query("UPDATE dhcp SET (host, host) VALUES (':host',':ip') WHERE mac=':mac'", array(':mac'=>$this->enviado['mac'],':host'=>$this->enviado['host'],':ip'=>$this->enviado['ip']));


        }

        $this->query("INSERT INTO dhcp (mac, host, host) VALUES (:mac,:host,:ip)", array(':mac'=>$this->enviado['mac'],':host'=>$this->enviado['host'],':ip'=>$this->enviado['ip']));

    }
    private function registroA($accion,$nombre,$ip){

        $nombre=explode('.',$$nombre,2);
        echo "samba-tool dns ".$accion." ".$this->dhcp['servidor'].".".$this->dhcp['dominio']." ".$nombre[1]." ".$nombre[0]." A ".$ip." -k yes\n";
        $text = "samba-tool dns ".$accion." ".$this->dhcp['servidor'].".".$this->dhcp['dominio']." ".$nombre[1]." ".$nombre[0]." A ".$ip." -k yes\n";
        file_put_contents($this->dhcp['logs'], $text, FILE_APPEND | LOCK_EX);
    }

    private function registroPTR($accion,$nombre,$ip){

        $ip=explode('.',$ip);
        $oct4=$ip[3];
        unset($ip[3]);
        $ip=array_reverse($ip);
        $ip=implode('.',$ip).".in-addr.arpa";
        echo "samba-tool dns ".$accion." ".$this->dhcp['servidor'].".".$this->dhcp['dominio']." ".$ip." ".$oct4." PTR ".$nombre." -k yes\n";
        $text = "samba-tool dns ".$accion." ".$this->dhcp['servidor'].".".$this->dhcp['dominio']." ".$ip." ".$oct4." PTR ".$nombre." -k yes\n";
        file_put_contents($this->dhcp['logs'], $text, FILE_APPEND | LOCK_EX);


    }
}




$link = mysql_connect('wpad.vipac.com', 'vipacuser', 'welcom3') or die('No se puede conectar: ' . mysql_error());
mysql_select_db('infraestructura') or die('No se pudo seleccionar la base de datos');

$query = 'SELECT * FROM dhcp WHERE mac="'.$mac.'"';
$resultQuery = mysql_query($query) or die('Consulta fallida: ' . mysql_error());
if (mysql_num_rows($resultQuery)==1){
    $row = mysql_fetch_array($resultQuery);
    echo "Host: " . $row['host'];
    echo "IP: " . $row['ip'];

} elseif (mysql_num_rows($resultQuery)>1) {
    echo "Hay mas de un registro";
} else {
    echo "no se encontraton registros";
}
$forwardArray=dns_get_record($hname.".".$domain, DNS_A);
print_r($forwardArray);
$ipArray=explode('.',$ip);
$reverseZone=$ipArray[2].".".$ipArray[1].".".$ipArray[0].".in-addr.arpa.";
$oct4=$ipArray[3];
$reverseArray = dns_get_record($oct4.".".$reverseZone, DNS_PTR);
print_r($reverseArray);

