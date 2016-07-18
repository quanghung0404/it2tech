<?php

class MusColAlphabets{
	
	static function get_characters(){
		
		$params =JComponentHelper::getParams( 'com_muscol' );
		
		$language = $params->get('alphabet', 'english') ;
		
		$characters['internal'] = MusColAlphabets::internal_characters($language);
		$characters['external'] = MusColAlphabets::external_characters($language);
		
		return $characters ;
		
	}
	
	static function get_combined_array(){
		
		$characters = MusColAlphabets::get_characters();
		
		for($i = 0, $n = count($characters['internal']); $i < $n; $i++){
			$return[$characters['internal'][$i]] = $characters['external'][$i];
		}
		
		return $return ;
		
	}
	
	static function internal_characters($language){
		
		//if you add your own alphabet HERE, use ONLY english characters A-Z, and then start with numbers, from 1 to infinity. Do NOT use your alphabet's characters here, only on the other funcion, external_characters() (see below)
		
		$internal['english'] 	= array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","1");
		
		$internal['greek'] 		= array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","1");
		
		$internal['russian'] 	= array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","2","3","4","5","6","7","8","1");
		
		$internal['arabicltr'] 	= array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","2","3","1");
		
		$internal['arabicrtl']		= array_reverse($internal['arabicltr']);
		
		$internal['custom'] 	= array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","1");
		
		return $internal[$language] ;
		
	}
	
	static function external_characters($language){
		
		$external['english']	= array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","#");
		
		$external['greek']		= array("Α","Β","Γ","Δ","Ε","Ζ","Η","Θ","Ι","Κ","Λ","L","Μ","Ν","Ξ","Ο","Π","Ρ","Σ","Τ","Υ","Φ","Χ","Ψ","Ω","#");
		
		$external['russian']	= array("А","Б","В","Г","Д","Е","Ё","Ж","З","И","Й","К","Л","М","Н","О","П","Р","С","Т","У","Ф","Х","Ц","Ч","Ш","Щ","Ъ","Ы","Ь","Э","Ю","Я","#");
		
		$external['arabicltr']		= array("ا","ب","ت","ث","ج","ح","خ","د","ذ","ر","ز","س","ش","ص","ض","ط","ظ","ع","غ","ف","ق","ك‎","ل","م","ن","ه","و","ي","#");
		
		$external['arabicrtl']		= array_reverse($external['arabicltr']);
		
		$external['custom']	= array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z","#");
		
		return $external[$language] ; ;
		
	}
	
}


