<?php
// 2020.02.11 Iovine
// Encodes a bidimensional boolean array into a hex string coding the contents of this
// array
class BitEncoder {
	private $rowCount;
	private $colCount;
	private $mask = 0x8;
	private $currentDigit = 0;
	private $encoded;
	private $bitArray;
	
	private function AppendBit($bit)
	{
		if ($bit) {
			$this->currentDigit |= $this->mask;
		} 
		$this->mask >>= 1;
		if ($this->mask==0) {
			$this->encoded.= sprintf("%X", $this->currentDigit);
			$this->currentDigit = 0;
			$this->mask = 8;
		}
	}
	
	function Encode() {
		$this->encoded = "";
		$this->currentDigit = 0;
		$this->mask = 0x8;
		for ($r=0; $r<$this->rowCount; $r++) {
			for ($c=0; $c<$this->colCount; $c++) {
				$this->AppendBit($this->bitArray[$r][$c]);
			}
		}
		if ($this->mask != 8) {
			$this->encoded.= sprintf("%X", $this->currentDigit);
		}
		return $this->encoded;
	}
	
	function __construct($bitArray) {
		$this->rowCount = count($bitArray);
		$this->colCount = count($bitArray[0]);
		$this->bitArray = $bitArray;
	}
}
?>