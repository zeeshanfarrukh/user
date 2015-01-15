<?php
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// CLASS: Graph.Histogram - Draw a Histogram
//
// Copyright (C) 1987-2004 Pascal Toussaint <pascal@pascalz.com>
//
// This program is free software; you can redistribute it and/or modify it
// under the terms of the GNU General Public License as published by the
// Free Software Foundation; either version 2 of the License, or any later 
// version.
//
// This program is distributed in the hope that it will be useful, but
// WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
// or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for
// more details.
//
// You should have received a copy of the GNU General Public License along
// with this program; if not, write to the Free Software Foundation, Inc.,
// 59 Temple Place, Suite 330, Boston, MA 02111-1307, USA.
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// $Id: class.graph.histogram.php,v 1.3 2004/03/07 13:59:26 pascalz Exp $ 
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

require_once("class.graph.php");

class Histogram extends Graph
{
	var $DrawScale;
	var $DrawGradLine;
	var $ShowValue;
	var $XAxisLabel;
	var $XAxisLabelColor;
	var $YAxisLabel;
	var $YAxisLabelColor;
	
	function Histogram($values)
	{
		$this->Graph($values,"HISTOGRAM");
		$this->DrawScale	= true;
		$this->DrawGradLine	= false;
		$this->ShowValue	= false;
		$this->XAxisLabel	= "";
	    $this->YAxisLabel	= "";

		$this->XAxisLabelColor	= "#000000";
		$this->YAxisLabelColor	= "#000000";
	}

	function GetGraphLink()
	{
		$getword = $this->GetBaseGraphLink();

		if (!$this->DrawScale)
			$getword .= "&noscale=1";
		else if ($this->DrawGradLine)
			$getword .= "&gradline=1";

		if ((!$this->DrawScale) && ($this->DrawGradLine))
			$this->ErrorMsg = 'Unable to draw gradlines without scale !';
	
		if ($this->ShowValue)
			$getword .= "&ShowVals=1";

		if ($this->XAxisLabel)
		{
			$getword .= "&xlbl=".urlencode($this->XAxisLabel);
			if ($this->XAxisLabelColor)
			{
				if ($this->XAxisLabelColor[0] == "#")
					$this->XAxisLabelColor = substr($this->XAxisLabelColor,1);
				$getword .= "&xlblcol=".$this->XAxisLabelColor;
			}
		}

		if ($this->YAxisLabel)
		{
			$getword .= "&ylbl=".urlencode($this->YAxisLabel);
			if ($this->YAxisLabelColor)
			{
				if ($this->YAxisLabelColor[0] == "#")
					$this->YAxisLabelColor = substr($this->YAxisLabelColor,1);
				$getword .= "&ylblcol=".$this->YAxisLabelColor;
			}
		}

		return $getword;
	}
}

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// G�n�ration du graphique
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
if ((isset($_GET['draw'])) && ($_GET['draw'] == "HISTOGRAM"))
{
	function GetPortionHeight($index,$portions,$maxheight)
	{
		global $rapport;
		global $scalemarginbottom;
		global $scalemargintop;
		
		if ($portions[0][$index] == 0) return 0;

		return ($portions[0][$index] * $rapport) + $scalemarginbottom + $scalemargintop;
	}
	
	$img_w = $_GET['width'];
	$img_h = $_GET['height'];

	if (isset($_GET['xlbl']))
		$XAxisLabel = urldecode($_GET['xlbl']);

	if (isset($_GET['ylbl']))
		$YAxisLabel = urldecode($_GET['ylbl']);

	$im = imagecreatetruecolor($img_w,$img_h);

	$black = imagecolorallocate($im,0,0,0);

	// Si une couleur de fond est d�finie, on la met sinon
	// on met un fond blanc.
	if (isset($_GET['bgcolor']))
		$bgcolor = imagecolorallocate($im,
						hexdec(substr($_GET['bgcolor'],0,2)),
						hexdec(substr($_GET['bgcolor'],2,2)),
						hexdec(substr($_GET['bgcolor'],4,2)));
	else $bgcolor = imagecolorallocate ($im, 255, 255, 255);	

	if (isset($_GET['xlblcol']))
	{
		$XAxisLabelColor = imagecolorallocate($im,
						hexdec(substr($_GET['xlblcol'],0,2)),
						hexdec(substr($_GET['xlblcol'],2,2)),
						hexdec(substr($_GET['xlblcol'],4,2)));
	}

	if (isset($_GET['ylblcol']))
	{
		$YAxisLabelColor = imagecolorallocate($im,
						hexdec(substr($_GET['ylblcol'],0,2)),
						hexdec(substr($_GET['ylblcol'],2,2)),
						hexdec(substr($_GET['ylblcol'],4,2)));
	}

	$portions = Array();
	$portions[0] = Array();
	$portions[1] = Array();
	$i = 0;
	$HasLabel = false;

	while (isset($_GET[$i]))
	{
		if (stristr($_GET[$i],'_'))
		{
			$HasLabel = true;
			array_push($portions[0],substr($_GET[$i],0,strpos($_GET[$i],'_')));
			array_push($portions[1],substr($_GET[$i],strpos($_GET[$i],'_') + 1));
		} else {
			array_push($portions[0],$_GET[$i]);
			array_push($portions[1],'');
		}

		$i++;
	}

	$maxval = max($portions[0]);

	imagefill($im,1,1,$bgcolor);
	
	if (isset($_GET['noscale']))
	{
		$scalewidth = 0;
		$scalemargintop = 0;
		$scalemarginbottom = 0;
	}
	else 
	{
		$font = 3;
		$scaleUnitWidth = 10;
		$scaleGradWidth = 15;
		$scalewidth = (imagefontwidth($font) * strlen($maxval)) + $scaleGradWidth + 5;
		if (strlen($YAxisLabel) > 0)  $scalewidth += imagefontheight($font);
	}

	$scaleleft = $scalewidth - 5;
	$scalemargintop = 5;
	$scalemarginbottom = 5;			

	if ($HasLabel)
	   $scalemarginbottom += 10;

	if (isset($XAxisLabel))
	   $scalemarginbottom += 10;

	if (isset($YAxisLabel))
	   $scalemargintop += 10;
	

	$portionmaxheight = $img_h - ($scalemargintop + $scalemarginbottom) + 1;
	$rapport = floor($portionmaxheight / $maxval);
	
	/**************************************************************************
	 * On dessine l'echelle
	 **************************************************************************/
	if (!isset($_GET['noscale']))
	{
		imageline($im, $scaleleft, $scalemargintop, $scaleleft, $img_h - $scalemarginbottom, $black);
		
		//imagestring($im,$font, 0, 0, $rapport, $black);
		
		$grad = 0;
		
		for ($k = $img_h - $scalemarginbottom ; $k > $scalemargintop ; $k--)
		{

			if ((floor($portionmaxheight - $k) % $rapport) == 0)
			{
				if ((($grad % 10) == 0) || ($grad == 0)) 
				{
					$gx = $scalewidth - $scaleGradWidth; 
					imagestring($im,$font, $gx - (imagefontwidth($font) * strlen($grad)) - 2, $k - (imagefontheight($font) / 2), $grad, $black);
					
					/**********************************************************
					 * On dessine la ligne des dizaines
					 **********************************************************/
					if (isset($_GET['gradline']))
					{
						$style = array ($black, $black, $black, $black, $black, $bgcolor, $bgcolor, $bgcolor, $bgcolor, $bgcolor);
						imagesetstyle ($im, $style);
						imageline ($im, $scalewidth, $k, $img_w, $k, IMG_COLOR_STYLED);
					}
					/**********************************************************/
				} else $gx =  $scalewidth - $scaleUnitWidth;
				
				imageline($im, $gx, $k, $scaleleft, $k, $black);
				$grad++;
			}
		}
	}
	/**************************************************************************/

	$portion_width = (($img_w - $scalewidth) / count($portions[0]));
	
	for ($i=0;$i<count($portions[0]);$i++)
	{
		$portion_height = GetPortionHeight($i, $portions, $portionmaxheight) - $scalemargintop;
		$color = GetPortionColor($i);
		$portion_color = imagecolorallocate($im,$color['red'],$color['green'],$color['blue']);
		
		if ($portions[0][$i] > 0)
		{
			imagerectangle($im, ($i * $portion_width) + $scalewidth + 1, $img_h - $scalemarginbottom, (($i * $portion_width) + $scalewidth) + $portion_width, $img_h - $portion_height, $portion_color);
		
			imagefill($im, (($i * $portion_width) + $scalewidth) + 2, $img_h - 2 - $scalemarginbottom, $portion_color);
		} else imageline($im,($i * $portion_width) + $scalewidth + 1, $img_h - 1 - $scalemarginbottom,(($i * $portion_width) + $scalewidth) + $portion_width,$img_h - 1 - $scalemarginbottom,$portion_color);

		if ($portions[1][$i])
			imagestringup($im,2,(($i * $portion_width) + $scalewidth) + ((int)( $portion_width/2)-(int) (imagefontheight(2)/2)) ,$img_h - $scalemarginbottom - 2,$portions[1][$i],$black);

		if (isset($_GET['ShowVals']))
			imagestringup($im,2,(($i * $portion_width) + $scalewidth) + ((int)( $portion_width/2)-(int) (imagefontheight(2)/2)) ,($img_h - $portion_height) + ((imagefontwidth(2) * strlen($portions[0][$i]))),$portions[0][$i],$black);
	}

	if (isset($XAxisLabel))
	{
		$lbl_width = imagefontwidth(3) * strlen($XAxisLabel) + 5;
		imageline($im,$scaleleft,$img_h-(imagefontheight(3)+2),$img_w,$img_h-(imagefontheight(3)+2),$black);
		imageline($im,$scaleleft,$img_h-(imagefontheight(3)+2),$scaleleft,(int)$img_h/2,$black);
		for ($i=0;$i<count($portions[0]);$i++)
		{
			imageline($im,(($i * $portion_width) + $scalewidth) + (int) ($portion_width / 2) ,$img_h - $scalemarginbottom,(($i * $portion_width) + $scalewidth) + (int) ($portion_width / 2),$img_h-(imagefontheight(3)+2),$black);
		}
		imagestring($im,3, $img_w - $lbl_width, $img_h - imagefontheight(3), $XAxisLabel, $XAxisLabelColor);
	}

	if (isset($YAxisLabel))
	{
		//imagestringup($im,3, 0 , (imagefontwidth(3) * strlen($YAxisLabel)), $YAxisLabel, $YAxisLabelColor);
		imagestring($im, 3, 0, 0, $YAxisLabel, $YAxisLabelColor);
	}

	// On envoie l'image...
	header ("Content-type: image/png");

	if (isset($_GET['export']))		
		imagepng($im,urldecode($_GET['export']));	// ...dans un fichier
	else imagepng($im);								// ...� l'�cran

	imagedestroy($im);
	exit;
}
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
?>