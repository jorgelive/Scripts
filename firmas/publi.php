<?php
if (isset($_GET['idioma'])){
    if($_GET['idioma']=='en'){
        header ("Location: http://www.viajespacifico.com.pe/mail/2014/diferenciales_cusco2014/Diferenciales_Cusco_Vipac_2014_ENG.pdf");
    }elseif($_GET['idioma']=='pt'){
        header ("Location: http://www.viajespacifico.com.pe/mail/2014/diferenciales_cusco2014/Diferenciales_Cusco_Vipac_2014_POR.pdf");
    }else{
        header ("Location: http://www.viajespacifico.com.pe/mail/2014/diferenciales_cusco2014/Diferenciales_Cusco_Vipac_2014_ESP.pdf");
    }
}else{
    header ("Location: http://www.viajespacifico.com.pe/mail/2014/diferenciales_cusco2014/Diferenciales_Cusco_Vipac_2014_ESP.pdf");
}