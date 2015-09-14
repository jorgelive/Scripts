<?php

class ResumenForYear
{
    private $items;
	private $grupos=array(
        1=>"ICHIGATSU",
        2=>"NIGATSU",
        3=>"SANGATSU",
        4=>"SHIGATSU",
        5=>"GOGATSU",
        6=>"ROKUGATSU",
        7=>"SHICHIGATSU",
        8=>"HACHIGATSU",
        9=>"KUGATSU",
        10=>"JUGATSU",
        11=>"JUICHIGATSU",
        12=>"JUNIGATSU"
    );

	public function __construct($list) {
		$this->make($list);
		$this->processItems($list);
	}
	private function make($list){
		foreach($list as $item):
			$item=explode(';',$item);
			$this->items{$item[0]}['VDRT']=$item[1];
			$this->items{$item[0]}['VDON']=$item[2];
			$this->items{$item[0]}['EXRT']=$item[3];
			$this->items{$item[0]}['EXON']=$item[4];
		endforeach;
	}
	
	private function processItems(){
		foreach($this->items as $fecha => $contenido){
			foreach($contenido as $tipo => $total){
				$total=trim($total);
				$this->separarItem($fecha,$tipo,$total);
			}
		}
		
	}
	
	private function separarItem($fecha,$tipo,$total){
		while ($total>=45){
			$this->writeItem($fecha,$tipo,20);
			$total= $total - 20;
		}
		while ($total>=8){
			$this->writeItem($fecha,$tipo,8);
			$total= $total - 8;
		}
		if($total!=0){
			$this->writeItem($fecha,$tipo,$total);
		}
	}
	
	private function writeItem($fecha,$tipo,$numero){

		$fecha=explode('/',$fecha);
		$grupo=$this->grupos[trim((int)$fecha[1])].' '.trim($fecha[0]);
		$fecha=$fecha[1].'/'.$fecha[0].'/'.$fecha[2];
        $fechaFormat=strtotime($fecha);

		if ($tipo=='VDON'||$tipo=='EXON'){
			$fecha = date('d/m/Y',$fechaFormat);
            $fecharetorno = date('d/m/Y',strtotime("+1 day", $fechaFormat));
		}else{
            $fecha = date('d/m/Y',$fechaFormat);
			$fecharetorno=$fecha;
		}

        $trenes[0]=array('601O','304O','83O','504O');
        $trenes[1]=array('31P','32P','33P','34P');

        $fechaCorte=strtotime('20-03-2016');

        if($fechaFormat<=$fechaCorte){
            $periodo=0;
        }else{
            $periodo=1;
        }

		if($tipo=='VDON'||$tipo=='VDRT'){
			$servicioIda=$trenes[$periodo][0];
			$servicioRetorno=$trenes[$periodo][1];
		}else{
			$servicioIda=$trenes[$periodo][2];
			$servicioRetorno=$trenes[$periodo][3];
		}

		echo $grupo.';'.$fecha.';'.$servicioIda.';'.$numero.';'.$fecharetorno.';'.$servicioRetorno.';'.$numero.'<br>';
	}
}
$list = file("requerimiento2016.csv");
$items = new ResumenForYear($list);

