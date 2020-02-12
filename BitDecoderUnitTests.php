<?php
	include 'BitDecoder.php';
	class BitDecoderUnitTests {
		private function ShowBitmap($b) {
			$result = "";
			for ($i=0; $i<sizeof($b); $i++) {
				$result .= $b[$i] ? "1" : "0";
			}
			return $result;
		}

		private function GetNextBit_WorksWell ($s, $b) {
			$decoder = new BitDecoder($s);
			$result = false;

			for ($i=0; $i<strlen($s) * 4; $i++) {
				if ($decoder->GetNextBit() != $b[$i]) {
					$differ = $i;
					$result = true;
					break;
				}
			}
			echo(sprintf('[%s] [%s] ', $s, $this->ShowBitmap($b)));
			if ($result) {
				echo(sprintf(" FAILS AT %d\n", $differ));
			} else {
				echo("OK\n");
			}
			return $result;
		}
		
		function GetNextBit_WorksWell_TestCases() {
			echo(__FUNCTION__."  <<<<<<<<<<<<<<<\n");
			$this->GetNextBit_WorksWell("0",array(false,false,false,false));
			$this->GetNextBit_WorksWell("1",array(false,false,false,true ));
			$this->GetNextBit_WorksWell("2",array(false,false,true ,false));
			$this->GetNextBit_WorksWell("3",array(false,false,true ,true ));
			$this->GetNextBit_WorksWell("4",array(false,true ,false,false));
			$this->GetNextBit_WorksWell("5",array(false,true ,false,true ));
			$this->GetNextBit_WorksWell("6",array(false,true ,true ,false));
			$this->GetNextBit_WorksWell("7",array(false,true ,true ,true ));
			$this->GetNextBit_WorksWell("8",array(true ,false,false,false));
			$this->GetNextBit_WorksWell("9",array(true ,false,false,true ));
			$this->GetNextBit_WorksWell("A",array(true ,false,true ,false));
			$this->GetNextBit_WorksWell("B",array(true ,false,true ,true ));
			$this->GetNextBit_WorksWell("C",array(true ,true ,false,false));
			$this->GetNextBit_WorksWell("D",array(true ,true ,false,true ));
			$this->GetNextBit_WorksWell("E",array(true ,true ,true ,false));
			$this->GetNextBit_WorksWell("F",array(true ,true ,true ,true ));
			$this->GetNextBit_WorksWell("10",array(false,false,false,true, false,false,false,false));
		}
		
		private function ShowBitmapArray($b, $rowCount, $colCount) {
			$result = "(";
			for ($row = 0; $row<$rowCount; $row++) {
				$result .= "(";
				for ($col = 0; $col<$colCount; $col++) {
					$result.= $b[$row][$col] ? "X" : "_";
				}
				$result .= ")";
			}
			$result .= ")";
			return $result;
		}
		
		function GetBimapArray_WorksWell($s, $rowCount, $colCount, $expectedArray) {
			$result = false;
			$decoder = new BitDecoder($s);
			$computedArray = $decoder->GetBitmapArray($rowCount, $colCount);
			for ($row = 0; $row<$rowCount; $row++) {
				for ($col = 0; $col<$colCount; $col++) {
					if ($expectedArray[$row][$col] != $computedArray[$row][$col]) {
						$differAt = sprintf("(%d,%d)", $col, $row);
						$result = true;
						break;
					}
				}
			}
			
			echo(sprintf('[%s] [%s] ', $s, $this->ShowBitmapArray($computedArray, $rowCount, $colCount)));
			if ($result) {
				echo(sprintf(" FAILS AT %s\n", $differAt));
			} else {
				echo("OK\n");
			}
		}
		
		function GetBitmapArray_WorksWell_TestCases() {
			echo(__FUNCTION__."  <<<<<<<<<<<<<<<\n");
			$this->GetBimapArray_WorksWell("8",1,1,array(array(true)));
			$this->GetBimapArray_WorksWell("3",2,2,array(array(false,false), array(true,true)));
			$this->GetBimapArray_WorksWell("0F",4,2,array(array(false,false), array(false,false), array(true,true), array(true,true)));
			$this->GetBimapArray_WorksWell("0F11",4,4,array(array(false,false,false,false), array(true, true, true, true), array(false, false, false, true), array(false, false, false, true)));
		}
	}
	
	$test = new BitDecoderUnitTests();
	$test->GetNextBit_WorksWell_TestCases();
	$test->GetBitmapArray_WorksWell_TestCases();
?>
