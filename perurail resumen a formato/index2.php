<?php

class ResumenForYear
{
    private $items;
	private $grupos=array(1=>"YIYUE",2=>"ARYUE",3=>"SANYUE",4=>"SEYUE",5=>"BUYUE",6=>"LIUYUE",7=>"CHIYUE",8=>"PAYUE",9=>"ECHIOYUE",10=>"SHEYUE",11=>"SHEYIYUE",12=>"SHEARYUE");
	
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
			$this->items{$item[0]}['VDGU']=$item[5];
			$this->items{$item[0]}['EXGU']=$item[6];
			
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
		if ($tipo=='VDON'||$tipo=='EXON'){
			$fecharetorno = strtotime($fecha);
			$fecharetorno = date('d/m/Y',strtotime("+1 day", $fecharetorno));
			$fecha = date('d/m/Y',strtotime($fecha));
		}else{
			$fecha = date('d/m/Y',strtotime($fecha));
			$fecharetorno=$fecha;
		}
		if($tipo=='VDON'||$tipo=='VDRT'||$tipo=='VDGU'){
			$servicioIda='31P';
			$servicioRetorno='32P';
		}else{
			$servicioIda='33P';
			$servicioRetorno='34P';
		}
		if($tipo=='VDGU'||$tipo=='EXGU'){
			$tipoPaxIda='3GI';
			$tipoPaxRetorno='3GR';
		}else{
			$tipoPaxIda='3OI';
			$tipoPaxRetorno='3OR';
		}
		
		//echo $grupo.';'.$fecha.';'.$servicioIda.';'.$tipoPaxIda.';'.$numero.';'.$fecharetorno.';'.$servicioRetorno.';'.$tipoPaxRetorno.';'.$numero.'<br>';
		
		echo $grupo.';'.$fecha.';'.$servicioIda.';'.$numero.';'.$fecharetorno.';'.$servicioRetorno.';'.$numero.'<br>';		
	}
}
$list = file("requerimiento2014.csv");
$items = new ResumenForYear($list);