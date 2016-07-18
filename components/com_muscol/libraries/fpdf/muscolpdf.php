<?php

require_once(JPATH_COMPONENT.DS.'libraries'.DS.'fpdf'.DS.'fpdf.php');

class MusColPDF extends FPDF{

	function short_string($cadena,$longitud_max){
	
		$longitud_punts_cadena = $this->GetStringWidth($cadena);
		$longitud_cadena = strlen($cadena);
		
		$caracters = $longitud_cadena - ceil($longitud_cadena * (($longitud_punts_cadena - $longitud_max)/$longitud_punts_cadena)) -3;
		
		return substr($cadena,0,$caracters)."...";
	
	}

	function FancyTable($header,$data){
	
		$alt=4;
		//Colores, ancho de línea y fuente en negrita
		$this->SetFillColor(255);
		$this->SetTextColor(0);
		$this->SetDrawColor(0);
		$this->SetLineWidth(.3);
		$this->SetFont('','B');
		//Cabecera
		
		$w=array(8,65,106.5,10.5);

		//for($i=0;$i<count($header);$i++)
		$this->Cell(0,7,$header,1,0,'C',1);
			
		$this->Ln();
		$this->Cell(0,$alt);
			$this->Ln();
		//Restauración de colores y fuentes
		$this->SetFillColor(241,245,250);
		$this->SetTextColor(0);
		$this->SetFont('');
		//Datos
		$fill=1;
		
		 $this->SetDrawColor(200);
		foreach($data as $row)
		{
		
			if($this->GetStringWidth($row[1]." ")>$w[1]){$row[1] = $this->short_string($row[1],$w[1]);}
			if($this->GetStringWidth($row[2]." ")>$w[2]){$row[2] = $this->short_string($row[2],$w[2]);}
				
			$this->Cell($w[0],$alt,$row[0],'R',0,'R',$fill);
			$this->Cell($w[1],$alt,$row[1],'LR',0,'L',$fill);
			$this->Cell($w[2],$alt,$row[2],'LR',0,'L',$fill);
			$this->Cell($w[3],$alt,$row[3],'LR',0,'R',$fill);
			$this->Ln();
			$fill=!$fill;
		}
		$this->Cell(array_sum($w),$alt);
		$this->Ln();
	}

	var $data = "";

	function posa_data(){
	   $new_time = time();
	   
	   $hora=date("H",$new_time);
	   $minut=date("i",$new_time);
	   $dia=date("j",$new_time);
	   $mes=date("n",$new_time);
	   $any=date("Y",$new_time);
	   
	   $this->data = $dia."/".$mes."/".$any."   ".$hora.":".$minut;
	}

	function Footer(){
	
		$this->SetY(-20);
	
		$this->SetFont('','',8);
	
		$this->SetTextColor(127);
		$this->Cell(100,10,$this->data,0,0,'L');
		$this->SetTextColor(0);
		$this->Cell(0,10,$this->PageNo(),0,0,'R');
	}
	
	/////// This function writes the list of albums
	
	function show_albums_list($albums){
		
		foreach($albums as $format){
			
			$header = $format->format_name;
			$data = array();
			$i = 0;

			foreach($format->albums as $album){
				$data[$i] = array($i+1,utf8_decode($album->artist_name),utf8_decode($album->name),$album->year);	
				$i++;
				
			}

			if(!empty($data)) $this->FancyTable($header,$data);
			
		}
		
	}
}

