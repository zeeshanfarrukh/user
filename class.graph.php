<?php
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
// CLASS: Graph - base class (do not call directly)
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
// $Id: class.graph.php,v 1.2 2004/03/03 17:48:31 pascalz Exp $ 
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

define('defaultWidth', 150);
define('defaultHeigth', 150);

// Renvois une couleur correspondant à l'index
// Les couleur sont construites avec les valeurs de couleurs vives
// mélangées pour ne pas avoir de couleur de même teinte cote à cote
function GetPortionColor($nIndex)
{
	$color_values = Array(224,32,0,192,64,255,160,96,128);
	$nineoffset = intval($nIndex / 9);
	
	$color['red']   = $color_values[(($nIndex % 9) + $nineoffset) % 9];
	$color['green'] = $color_values[(abs(($nIndex % 9)-8) + $nineoffset) % 9];
	$color['blue']  = $color_values[(abs(($nIndex % 9) - abs(($nIndex % 9)-8)) + $nineoffset) % 9];

	return $color;
}

class Graph_Portion 
{
	var $index;
	var $label;
	var $value;

	// Constructeur
	function Graph_Portion($index,$label,$value)
	{
		$this->index = $index;
		$this->label = $label;
		$this->value = $value;
	}

}

class Graph
{
	var $portions = Array();
	var $draw3D;
	var $_type_graph;
	var $width;
	var $height;
	var $bgcolor;
	var $border;
	var $title;
	var $legend;
	var $ErrorMsg;
	

	// Private
	var $_exportname;

	// Constructeur
	function Graph($values,$type = "")
	{
		if ($type == "")
		{
			$this->Graph_Error("Ceci est la classe de base,<br>elle ne doit pas etre appelée directement !",true);
		}

		// Initialisation des valeurs par défaut
		$this->_type_graph	= $type;
		$this->width		= defaultWidth;
		$this->height		= defaultHeigth;
		$this->bgcolor		= "FFFFFF";
		$this->border		= 0;
		$this->title		= "";
		$this->legend		= "";
		
		$this->_exportname  = "";

		$this->ErrorMsg		= "";

		$idx = 0;
		foreach($values as $lbl => $val)
		{
			$idx = array_push($this->portions, new Graph_Portion($idx,$lbl,$val));
		}
	}

	// Gestion des erreurs
	function Graph_Error($msg,$stop = false)
	{
		$this->ErrorMsg = $msg;
		print("<font color=#FF0000>".$this->ErrorMsg."</font>");
		error_reporting(E_ALL);
		if ($stop) exit;
	}	

	// On affiche l'image du graphe à l'écran
	function Draw()
	{
		print("<img src=\"".$this->GetGraphLink()."\" border=\"".$this->border."\">\n");
	}

	// On sauvegarde l'image dans un fichier
	function Export($filename)
	{
		if ($filename)
		{
			$this->_exportname = $filename;
			include('http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'].$this->GetGraphLink());			
		}
	}

	// On affiche et on sauvegarde
	function ExportAndDraw($filename)
	{
		$this->Draw();
		$this->Export($filename);		
	}

	// On construit la base du GET commun à tous les graphes
	function GetBaseGraphLink()
	{
		$getword = "?draw=".$this->_type_graph."&width=".$this->width."&height=".$this->height;

		if ($this->bgcolor[0] == "#")
				$this->bgcolor = substr($this->bgcolor,1);

		if ($this->bgcolor != "FFFFFF")	// #FFFFFF étant la couleur par défaut, inutile de poster !
		{
			$getword .= "&bgcolor=".$this->bgcolor;
		}

		if ($this->_exportname)
			$getword .= "&export=".urlencode($this->_exportname);

		foreach($this->portions as $portion)
		{
			$getword .= "&".$portion->index."=".$portion->value;
			if (!($portion->label === $portion->index))
				$getword .= "_".urlencode($portion->label);
		}

		return $getword;
	}
}

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
?>