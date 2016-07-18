<?php

/** 
 * @version		1.0.1
 * @package		muscol
 * @copyright	2009 JoomlaMusicSolutions.com
 * @license		GPLv2
 */

jimport( 'joomla.application.component.view');

require_once(JPATH_COMPONENT.DS.'libraries'.DS.'fpdf'.DS.'muscolpdf.php');

class ArtistsViewArtist extends JViewLegacy
{
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		
		$document = JFactory::getDocument();
		$document->setMimeEncoding('application/pdf');
		
		$artist		= $this->get( 'Data');
		$albums		= $this->get( 'AlbumsData');
		
		$pdf = new MusColPDF();

		$pdf->SetTitle($artist->artist_name);
		$pdf->SetFont('arial','',10);
		$pdf->AddPage();
		
		$pdf->posa_data();
		
		$pdf->SetFillColor(255);
		$pdf->SetTextColor(0);
		$pdf->SetDrawColor(0);
		$pdf->SetLineWidth(.3);
		
		//print the albums
		$pdf->show_albums_list($albums);

      	$pdf->Output($album->artist_name . ".pdf", "I");
	  
	  	exit();		

	}
	
}
?>
