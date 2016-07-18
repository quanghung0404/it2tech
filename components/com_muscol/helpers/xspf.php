<?php


// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();


class JDocumentRendererXSPF extends JDocumentRenderer
{
	/**
	 * Renderer mime type
	 *
	 * @var		string
	 * @access	private
	 */
	var $_mime = "application/xml";

	/**
	 * Render the feed
	 *
	 * @access public
	 * @return	string
	 */
	function render($name, $params = NULL, $content = NULL)
	{
		$data	= $this->_doc;
		
		$feed = '<playlist version="1" xmlns="http://xspf.org/ns/0/">
					<title>XSPF playlist</title>
					<trackList>
						';
						

		for ($i=0; $i<count($data->items); $i++)
		{
			if ((strpos($data->items[$i]->link, 'http://') === false) and (strpos($data->items[$i]->link, 'https://') === false)) {
				$data->items[$i]->link = $data->items[$i]->link;
			}
			$feed.= "		<track>\n";
			$feed.= "			<title>".htmlspecialchars(strip_tags($data->items[$i]->title), ENT_COMPAT, 'UTF-8')."</title>\n";
			$feed.= "			<link>".$data->items[$i]->link."</link>\n";
			$feed.= "			<annotation><![CDATA[".$this->_relToAbs($data->items[$i]->annotation)."]]></annotation>\n";

			if ($data->items[$i]->creator!="") {
				$feed.= "			<creator>".htmlspecialchars($data->items[$i]->creator, ENT_COMPAT, 'UTF-8')."</creator>\n";
			}

			if ($data->items[$i]->location!="") {
				$feed.= "			<location>".htmlspecialchars($data->items[$i]->location, ENT_COMPAT, 'UTF-8')."</location>\n";
			}
			
			if (isset($data->items[$i]->image)) {
				$feed.= "			<image>".htmlspecialchars($data->items[$i]->image, ENT_COMPAT, 'UTF-8')."</image>\n";
			}
			
			if (isset($data->items[$i]->duration)) {
				$feed.= "			<duration>".htmlspecialchars($data->items[$i]->duration, ENT_COMPAT, 'UTF-8')."</duration>\n";
			}

			$feed.= "		</track>\n";
		}
		$feed .= '</trackList>
					</playlist>' ;
		return $feed;
	}

	/**
	 * Convert links in a text from relative to absolute
	 *
	 * @access public
	 * @return	string
	 */
	function _relToAbs($text)
	{
		$base = JURI::base();
  		$text = preg_replace("/(href|src)=\"(?!http|ftp|https)([^\"]*)\"/", "$1=\"$base\$2\"", $text);

		return $text;
	}
}
