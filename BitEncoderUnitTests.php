<?php
	include 'BitDecoder.php';
	include 'BitEncoder.php';
	class BitEncoderUnitTests {
		function Encode_WorksWell($expected, $rowCount, $colCount) {
			$decoder = new BitDecoder($expected, $rowCount, $colCount);
			$array = $decoder->GetBitmapArray($rowCount, $colCount);
			$encoder = new BitEncoder($array);
			$computed = $encoder->Encode();
			if ($expected != $computed) {
				echo ("[".$expected."] differs from [".$computed."]\n");
			}
		}
		
		function Encode_WorksWell_TestCases() {
			echo(__FUNCTION__."  <<<<<<<<<<<<<<<\n");
			$this->Encode_WorksWell("3",2,2);
			$this->Encode_WorksWell("0F",4,2);
			$this->Encode_WorksWell("0F11",4,4);
		}
	}
	
	$test = new BitEncoderUnitTests();
	$test->Encode_WorksWell_TestCases();
?>