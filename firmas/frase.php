<?php
if (isset($_GET['idioma'])){
    if($_GET['idioma']=='en'){
        header ("Location: http://www.viajespacifico.com.pe/en");
    }elseif($_GET['idioma']=='pt'){
        header ("Location: http://www.viajespacifico.com.pe/es");
    }else{
        header ("Location: http://www.viajespacifico.com.pe/es");
    }
}else{
    header ("Location: http://www.viajespacifico.com.pe/es");
}