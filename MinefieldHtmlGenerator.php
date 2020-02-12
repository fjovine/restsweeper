<?php
	include 'Minefield.php';
	include 'BitDecoder.php';
	$baseUrl = "http://localhost:8080/MinefieldHtmlGenerator.php";
	$phase = $_GET["phase"];
	$click = $_GET["click"];
	if ($phase=='') {
		$phase = 0;
	}
	if ($phase==0) {
		$decoder = new BitDecoder("0100002231000000");
		$minedfield = $decoder->GetBitmapArray(8,8);
		$minefield = new Minefield($minedfield);
	} else {
		$mines =  $_GET["mines"];
		$flagged =  $_GET["flagged"];
		$uncovered =  $_GET["uncovered"];
		$action = $_GET["action"];

		$decoder = new BitDecoder($mines);
		$minedfield = $decoder->GetBitmapArray(8,8);
		$decoder = new BitDecoder($flagged);
		$flagField = $decoder->GetBitmapArray(8,8);
		$decoder = new BitDecoder($uncovered);
		$uncoveredField = $decoder->GetBitmapArray(8,8);
		$x = hexdec(substr($action,0,1));
		$y = hexdec(substr($action,1,1));
		
		$minefield = new Minefield($minedfield, $flagField, $uncoveredField);
		if ($minefield->ClickAt($x,$y, $click=="E")) {
			$minefield->MineFound();
		} else {
			if ($minefield->HasWon()) {
				echo("WON");
				$minefield->MineFound();
			}
		}
	}
	switch ($phase) {
	case 0 : $phase = 1; break;
	case 1 : $phase = 2; break;
	case 2 : $phase = 2; break;
	}
	
	function GetLink($x,$y,$state) {
		global $phase, $minefield, $click, $baseUrl;
		return $baseUrl.
		sprintf("?phase=%d&mines=%s&flagged=%s&uncovered=%s&click=%s&action=%x%x%d",
			$phase, 
			$minefield->GetMineBitmapEncoded(),
			$minefield->GetFlaggedBitmapEncoded(),
			$minefield->GetExploredBitmapEncoded(),
			$click=="E" ? "E"  : "F",
			$x,$y,$state);
	}
	
	function GetLinkExploration($exploration) {
		global $phase, $minefield, $baseUrl;
		return $baseUrl.
		sprintf("?phase=%d&mines=%s&flagged=%s&uncovered=%s&click=%s",
			$phase, 
			$minefield->GetMineBitmapEncoded(),
			$minefield->GetFlaggedBitmapEncoded(),
			$minefield->GetExploredBitmapEncoded(),
			$exploration);
		echo ($exploration."\n".$return);
	}
	
	function ExploreClass() {
		global $click;
		echo (($click=="E") ? "class='selected'" : "");
	}
	function FlagClass() {
		global $click;
		echo (($click=="F") ? "class='selected'" : "");
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>A REST Minesweeper game.</title>
	<style>
		body {
		  text-align: center;
		}
		table.clickmenu a:link {
		  color: black;
		  text-decoration: none;
		}

		table.clickmenu a:visited {
		  color: black;
		  text-decoration: none;
		}

		table.clickmenu a:hover {
		  color: black;
		  text-decoration: none;
		}

		table.clickmenu a:active {
		  color: black;
		  text-decoration: none;
		}

		table.clickmenu {
		  margin: 0 auto;
		  border-collapse: collapse; 
		  border: 0px solid black;
		}

		table.clickmenu tr {
		  border-collapse: collapse; 
		  border: 0px solid black;
		}

		table.clickmenu td.selected {
		  border-collapse: collapse; 
		  border: 0px solid black;
		  color: #000000;
		  background-color: #EEEEEE;
		  width: 3cm;
		}
		
		table.clickmenu td {
		  border-collapse: collapse; 
		  border: 0px solid black;
		  width: 3cm;
		  text-align: center;
		}

		table.minefield a {
			text-decoration: none;
		}
		table.minefield {
		  margin: 0 auto;
		  border-collapse: collapse; 
		  border: 1px solid black;
		  text-align: center;
		}
		table.minefield tr {
		  height: 2cm;
		  border: 1px solid black;
		  vertical-align: center; 
		  text-align: center;
		}

		table.minefield tr.selected {
		  height: 2cm;
		  border: 1px solid black;
		  background-color: #FFFFFF;
		  vertical-align: center; 
		  text-align: center;
		  font-size: 28pt;
		}
		table.minefield td.mined {
		  width: 2cm;
		  color: #FFFFFF;
		  background-color:#000000;
		  border: 1px solid black;
		  text-align: center;
		  font-size: 28pt;
		}
		table.minefield td.toexplore, td.flagged {
		  width: 2cm;
		  background-color: #EEEEEE;
		  border: 1px solid black;
		  text-align: center;
		  font-size: 28pt;
		}
		table.minefield td.explored {
		  width: 2cm;
		  background-color: #FFFFFF;
		  border: 1px solid black;
		  text-align: center;
		  font-size: 28pt;
		}
	</style>
</head>

<body>

<h1>RestSweeper</h1>

<table class="clickmenu">
<tr>
	<td class="selected"><a href='<?php echo($baseUrl."?state=0&click=E") ?>'>New</a></td> 
	<td <?php ExploreClass() ?>><a href='<?php echo(GetLinkExploration("E")) ?>'>Explore</a></td> 
	<td <?php FlagClass() ?>><a href='<?php echo(GetLinkExploration("F")) ?>'>Flag</a></td>
</tr>
</table>
<hr/>
<table class="minefield">
<?php
	for ($r=0;$r<8;$r++) {
		echo("<tr>");
		for ($c=0;$c<8;$c++) {
			//$minefield->ClickAt($c,$r,true);
			$cellClass = "normal";
			$cellContent = "&nbsp;";
			if ($minefield != null) {
				switch($minefield->GetCell($c,$r)) {
				case 'F': $cellClass = "flagged"; break;
				case 'M': $cellClass = "mined"; break;
				case '.': $cellClass = "toexplore"; break;
				default : $cellClass = "explored"; break;
				}
				$cellContent = $minefield->GetCell($c,$r);
				if ($cellContent == '.') {
					$cellContent = '&#x2B24;';
				}
			} else {
				$cellClass = "toexplore";
			}
			echo(sprintf("<td class='%s'><a href='%s'>%s</a></td>\n", 
				$cellClass, 
				GetLink($c, $r, $click == "E" ? 1 : 2),
				$cellContent));
		}
		echo("</tr>");
	}

?>
</table>
</body>
</html>