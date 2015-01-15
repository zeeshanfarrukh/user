<?php
include 'Gethours.php';
//
$value= Gethours::gettweets();
//
//print_r($value);





if (isset($_GET['vs']))
{
	ini_set('highlight.string',  '#999900');
	ini_set('highlight.comment', '#66cc00');
	ini_set('highlight.keyword', '#0000ff');
	ini_set('highlight.bg',      '#bcbcbc');
	ini_set('highlight.default', '#330066');
	ini_set('highlight.html',    '#8a8a8a');
	print "<html><head><title>Test Graph Histogramme -> source</title><head><body bgcolor=\"#ffffff\">";
	highlight_file(__FILE__);
	print "<br><br>";
	print "<a href=\"?\">Voir la page</a>";
	print "</body></html>";

	exit;
}

include("class.graph.histogram.php");

header("Pragma: no-cache"); 
header("Cache-Control: no-cache");

$document_titre = "Test Graph Histogramme"; // Titre de la page

if (isset($_POST['h_width'])) $h_width = $_POST['h_width']; else $h_width = 700;
if (isset($_POST['h_height'])) $h_height = $_POST['h_height']; else $h_height = 350;
if (isset($_POST['h_border'])) $h_border = $_POST['h_border']; else $h_border = 0;
if (isset($_POST['h_drawscale']))$h_drawscale = "checked"; else $h_drawscale = "";
if (isset($_POST['h_drawgradline']))$h_drawgradline = "checked"; else $h_drawgradline = "";
if (isset($_POST['h_showvalue']))$h_showvalue = "checked"; else $h_showvalue = "";
if (isset($_POST['h_xaxislabel'])) $h_xaxislabel = $_POST['h_xaxislabel']; else $h_xaxislabel = "Hours";
if (isset($_POST['h_yaxislabel'])) $h_yaxislabel = $_POST['h_yaxislabel']; else $h_yaxislabel = "Messages";
if (!isset($_POST['h_width']))
{
	$h_drawscale = "checked";
	$h_drawgradline = "";
	$h_showvalue = "";
}
//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

?> 
<html>
  <head>
    <title><?echo $document_titre?></title>
	<style>
	  body{overflow:auto;}
	</style>
  </head>
  <body bgcolor="#EFFFDD">
  <table width="100%" border="1">
  <tr>
  <td>
<?php

	
	
	
    $H = new Histogram($value);
    $H->width = $h_width;                        // 150 par d�faut
    $H->height = $h_height;                      // 150 par d�faut
    $H->bgcolor = "#EFFFDD";                     // #FFFFFF par d�faut
    $H->DrawScale = ($h_drawscale != "");        // true par d�faut
    $H->DrawGradLine = ($h_drawgradline != "");  // false par d�faut     
    $H->border=$h_border;                        // 0 par d�faut
    $H->ShowValue = ($h_showvalue != "");        // false par d�faut
    $H->XAxisLabel = $h_xaxislabel;              //
    $H->YAxisLabel = $h_yaxislabel;              //
    $H->XAxisLabelColor = "#6666FF";             // #000000 par d�faut
    $H->YAxisLabelColor = "#00CC66";             // #000000 par d�faut
	$H->Draw();	
	//$H->Export('c:\\histogramme.png');
	//$H->ExportAndDraw('c:\\histogramme.png');
?>
  </td>
  <td valign="top">
	<form method="post" action="">
	  <table width="100%">
	  <tr>
	  	<td>Width&nbsp;:</td>	  
	  	<td><input type="text" name="h_width" value="<?php echo $h_width?>"></td>
	  </tr>
	  <tr>
	  	<td>Height&nbsp;:</td>
	  	<td><input type="text" name="h_height" value="<?php echo $h_height?>"></td>
	  </tr>
	  <tr>
	  	<td>Border&nbsp;:</td>
	  	<td><input type="text" name="h_border" value="<?php echo $h_border?>"></td>
	  </tr>
	  <tr>
	  	<td colspan="2"><input type="checkbox" name="h_drawscale" value="1" <?php echo $h_drawscale ?>> Draw Scale</td>
	  </tr>
	  <tr>
	  	<td colspan="2"><input type="checkbox" name="h_drawgradline" value="1" <?php echo $h_drawgradline ?>> Draw grad line</td>
	  </tr>
	  <tr>
	  	<td colspan="2"><input type="checkbox" name="h_showvalue" value="1" <?php echo $h_showvalue ?>>Show value</td>
	  </tr>
	  <tr>
	  	<td>X Axis Label&nbsp;:</td>	  
	  	<td><input type="text" name="h_xaxislabel" value="<?php echo $h_xaxislabel?>"></td>
	  </tr>
	  <tr>
	  	<td>Y Axis Label&nbsp;:</td>
	  	<td><input type="text" name="h_yaxislabel" value="<?php echo $h_yaxislabel?>"></td>
	  </tr>
	  <tr>
	  	<td colspan="2"><hr></td>
	  </tr>
	  <tr>
	  	<td colspan="2" align="center"><input type="submit" value="OK"></td>
	  </tr>
	  </table>
	</form>
  </td>
  </tr>
<?php
  if ($H->ErrorMsg != "")
   print("<tr><td colspan=\"2\" bgcolor=\"#FF0000\" style=\"color: #FFFF33;\">".$H->ErrorMsg."</td></tr>");
?>
  </table>
  
  </body>
</html>

