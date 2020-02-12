<?php
// 2020.02.08 Iovine
// Represents the Minefield as an array of integer.
// The class accepts a bidimensional boolean array. The bit set represent the position 
// Of each mine.
include 'BitEncoder.php';
class Minefield {
	// stores the column count of the minefield.
	private $colCount;

	// Stores the row count of the minefield.
	private $rowCount;

	// reference to a boolean bidimensional array where the true bits are the mines.
	private $mineBitmap;

	// reference to a boolean bidimensional array where the true bits are the cells that have been flagged.
	private $flaggedBitmap;

	// reference to a boolean bidimensional array where the true bits are the cells have been explored
	private $exploredBitmap;

	// Bidimensional array of integers where each integer at (x,y) counts the number of 
	// mines in the direct neighbourhood. The direct neighbourhood of the square composed by
	// 8 cells in direct contact with (x,y).
	// Consider the following minefield
	//   012
	// 0 M--
	// 1 M*M
	// 2 ---
	// This is the direct neightbourhood of cell (1,1) shown by an asterisk.
	// So the $mineField[1,1] will contain 3 as there are three mines (M) around
	// the pas
	private $mineField;
	
	// Builds the mineField 
	private function ComputeField()
	{
		$this->mineField = array();
		for ($row=0; $row<$this->rowCount; $row++) {
			$this->mineField[$row] = array();
			for ($col=0; $col<$this->colCount; $col++) {
				$this->mineField[$row][$col] = $this->CountMinesNeighbourhood($col, $row);
			}
		}
	}
	
	// Counts the nummber of mines in the immediate neighbourhood of the passed
	// cell at $col,$row.
	private function CountMinesNeighbourhood($col, $row) {
		$result = 0;
		for ($c=$col-1; $c <= $col+1; $c++) {
			if ($c < 0 || $c >= $this->colCount) continue;
			for ($r=$row-1; $r<=$row+1; $r++) {
				if ($r < 0 || $r >= $this->rowCount) continue;
				if ($c == $col && $r==$row) continue;
				if ($this->mineBitmap[$r][$c]) $result++;
			}
		}
		return $result;
	}
	
	// Returns the count of the mines in the direct neighbourhood of the passed
	// cell. This piece of information is retrieved from the computed bitmap array
	public function GetMineCount($col,$row) {
		return $this->mineField[$row][$col];
	}
	
	// Returns true if the passed cell at $col,$row contains a mine
	public function IsMine($col,$row) {
		return $this->mineBitmap[$row][$col];
	}
	
	// Gets the number of rows of the minefield
	public function GetRowCount() {
		return $this->rowCount;
	}
	
	// Gets the number of columns in the minefield
	public function GetColCount() {
		return $this->colCount;
	}
	
	public function HasWon()
	{
		for ($row=0; $row<$this->rowCount; $row++) {
			for ($col=0; $col<$this->colCount; $col++) {
				if (!$this->exploredBitmap[$row][$col] && !$this->mineBitmap[$row][$col]) {
					return false;
				}
			}
		}
		return true;
	}
	
	// Simulate the click at the passed cell.
	// $col is the column where it has been clicked
	// $row is the row where it has been clicked
	// $clickType : if true, it is an exploration click, if flag click
	// An exploration click is a cell touched with the aim of decouvring what it conatins
	// A flag click, is a cell touched by the player when he or she thinks the cell
	// contains a mine.
	// Returns true if a mine has been touched
	public function ClickAt($col, $row, $clickType) {
		if ($this->exploredBitmap[$row][$col]) {
			// Clicking on an explored bitmap does not change anything
			return false;
		}
		if ($clickType) {
			// exploration click. 
			if ($this->IsMine($col, $row)) {
				$this->exploredBitmap[$row][$col] = true;
				return true;
			}
			
			$this->exploredBitmap[$row][$col] = true;
			return false;
		} else {
			// flag click
			$this->flaggedBitmap[$row][$col] = true;
		}
	}
	
	// Returns the status of the mine field.
	// If the cell has been explored, it show the minecount or white if the minecount
	// is 0. F if the cell has been flaggedand, a M if the cell contains a mine or a 
	// dot if the cell has not been explored.
	public function GetCell($col, $row) {
		if ($this->flaggedBitmap[$row][$col]) {
			return "F";
		}
		if (!$this->exploredBitmap[$row][$col]) {
			return ".";
		}
		if ($this->IsMine($col,$row)) {
			return "M";
		}
		$mineCount = $this->mineField[$row][$col];
		if ($mineCount == 0) {
			return " ";
		} else {
			return sprintf("%d", $mineCount);
		}
	}
	
	public function ShowField($rowDelimiter="\n") {
		$result = "";
		for ($row=0; $row<$this->rowCount; $row++) {
			for ($col=0; $col<$this->colCount; $col++) {
				$result .= $this->GetCell($col,$row);
			}
			$result .= $rowDelimiter;
		}
		$result .= $rowDelimiter;
		return $result;
	}
	
	// Helper function that creates a bidimensional boolean array $colCount x $rowCount
	public function CreateBooleanArray($colCount, $rowCount, $fill=false) {
		$result = array();
		for ($row = 0; $row<$rowCount; $row++) {
			$result[$row] = array();
			for ($col =0; $col < $colCount; $col++) {
				$result[$row][$col] = $fill;
			}
		}
		return $result;
	}
	
	public function GetMineBitmapEncoded() {
		$encoder = new BitEncoder($this->mineBitmap);
		return $encoder->Encode();
	}
	
	public function GetFlaggedBitmapEncoded() {
		$encoder = new BitEncoder($this->flaggedBitmap);
		return $encoder->Encode();
	}
	
	public function GetExploredBitmapEncoded() {
		$encoder = new BitEncoder($this->exploredBitmap);
		return $encoder->Encode();
	}
	
	public function MineFound() {
		$exploredBitmap = $this->CreateBooleanArray($this->colCount, $this->rowCount, true);
		$this->exploredBitmap = $exploredBitmap;
	}

	// Creates a minefield with the mines saved in tbe $mineBitmap parameter.
	// The bitmap of flagged and explored cell is stored in the $flaggedBitap
	// and $exploredBitmap respectively. If these parameters are not set, i.e. the
	// correspondent formal parameters are set to null, both of these bitmaps are
	// created with all values to false of the same dimension of the minefield.
	public function __construct($mineBitmap, $flaggedBitmap=null, $exploredBitmap=null) {
		$this->mineBitmap = $mineBitmap;
		$this->rowCount = count($mineBitmap);
		$this->colCount = count($mineBitmap[0]);
		$this->ComputeField();
		if ($flaggedBitmap == null) {
			$flaggedBitmap = $this->CreateBooleanArray($this->colCount, $this->rowCount);
		}
		if ($exploredBitmap == null) {
			$exploredBitmap = $this->CreateBooleanArray($this->colCount, $this->rowCount);
		}
		
		$this->flaggedBitmap = $flaggedBitmap;
		$this->exploredBitmap = $exploredBitmap;
	}
}
?>