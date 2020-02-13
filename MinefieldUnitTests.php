<?php
	include 'Minefield.php';
	include 'BitDecoder.php';
	class MinefieldUnitTests {
		public function ShowMinefield($minefield, $separator ="\n") {
			$result = "";
			for ($row=0; $row<$minefield->GetRowCount(); $row++) {
				for ($col=0; $col<$minefield->GetColCount(); $col++) {
					if ($minefield->IsMine($col, $row)) {
						$result .="*";
					} else {
						$result .=sprintf("%d", $minefield->GetMineCount($col, $row));
					}
				}
				$result.=$separator;
			}
			
			return $result;
		}
		
		public function GetMineCount_WorksWell($rowCount, $colCount, $hexMinefield, $expected) {
			$decoder = new BitDecoder($hexMinefield);
			$mineMap = $decoder->GetBitmapArray($rowCount, $colCount);
			$minefield = new Minefield($mineMap, null, null);
			//echo($this->ShowMinefield($minefield, "-")."\n");
			$computed = $this->ShowMinefield($minefield, "-");
			if ($computed == $expected) {
				echo("OK\n");
			} else {
				echo("Error case ".$expected." found ".$computed."\n");
			}
		}
		
		public function GetMineCount_WorksWell_TestCases() {
			echo(__FUNCTION__."  <<<<<<<<<<<<<<<\n");
			$test = new MinefieldUnitTests();
			// One mine
			$test->GetMineCount_WorksWell(3,4,"800", "*100-1100-0000-");
			$test->GetMineCount_WorksWell(3,4,"400", "1*10-1110-0000-");
			$test->GetMineCount_WorksWell(3,4,"200", "01*1-0111-0000-");
			$test->GetMineCount_WorksWell(3,4,"100", "001*-0011-0000-");
			$test->GetMineCount_WorksWell(3,4,"080", "1100-*100-1100-");
			$test->GetMineCount_WorksWell(3,4,"040", "1110-1*10-1110-");
			$test->GetMineCount_WorksWell(3,4,"020", "0111-01*1-0111-");
			$test->GetMineCount_WorksWell(3,4,"010", "0011-001*-0011-");
			$test->GetMineCount_WorksWell(3,4,"008", "0000-1100-*100-");
			$test->GetMineCount_WorksWell(3,4,"004", "0000-1110-1*10-");
			$test->GetMineCount_WorksWell(3,4,"002", "0000-0111-01*1-");
			$test->GetMineCount_WorksWell(3,4,"001", "0000-0011-001*-");
			
			// Two mines
			$test->GetMineCount_WorksWell(3,4,"C00", "**10-2210-0000-");
			$test->GetMineCount_WorksWell(3,4,"600", "1**1-1221-0000-");
			$test->GetMineCount_WorksWell(3,4,"300", "01**-0122-0000-");
			$test->GetMineCount_WorksWell(3,4,"0C0", "2210-**10-2210-");
			$test->GetMineCount_WorksWell(3,4,"060", "1221-1**1-1221-");
			$test->GetMineCount_WorksWell(3,4,"030", "0122-01**-0122-");
			$test->GetMineCount_WorksWell(3,4,"00C", "0000-2210-**10-");
			$test->GetMineCount_WorksWell(3,4,"006", "0000-1221-1**1-");
			$test->GetMineCount_WorksWell(3,4,"003", "0000-0122-01**-");
			
			// Four mines
			$test->GetMineCount_WorksWell(3,4,"CC0", "**20-**20-2210-");
			$test->GetMineCount_WorksWell(3,4,"660", "2**2-2**2-1221-");
			$test->GetMineCount_WorksWell(3,4,"330", "02**-02**-0122-");
			$test->GetMineCount_WorksWell(3,4,"0CC", "2210-**20-**20-");
			$test->GetMineCount_WorksWell(3,4,"066", "1221-2**2-2**2-");
			$test->GetMineCount_WorksWell(3,4,"033", "0122-02**-02**-");

			// Eight mines
			$test->GetMineCount_WorksWell(3,4,"EBE", "***3-*8**-***3-");
		}
		
		public function FullMatch_WorksWell() {
			$decoder = new BitDecoder("080");
			$minemap = $decoder->GetBitmapArray(3,3);
			$minefield = new Minefield($minemap);
			echo($minefield->ShowField());
			for ($r=0;$r<3;$r++) {
				for ($c=0;$c<3;$c++) {
					$minefield->ClickAt($c,$r,true);
					echo($minefield->ShowField());
				}
			}
		}
		
		public function GetMineBitmapEncoded_WorksWell() {
			$decoder = new BitDecoder("080");
			$minemap = $decoder->GetBitmapArray(3,3);
			$minefield = new Minefield($minemap);
			echo($minefield->GetMineBitmapEncoded()."\n");
			
			$decoder = new BitDecoder("0100002231000000");
			$minedfield = $decoder->GetBitmapArray(8,8);
			$minefield = new Minefield($minedfield);
			echo($minefield->GetMineBitmapEncoded()."\n");
			echo($minefield->GetFlaggedBitmapEncoded()."\n");
			echo($minefield->GetExploredBitmapEncoded()."\n");
		}
		
		public function CreateBooleanArray_WorksWell() {
			$decoder = new BitDecoder("080");
			$minemap = $decoder->GetBitmapArray(3,3);
			$minefield = new Minefield($minemap);
			$bitmap = $minefield->CreateBooleanArray(3,3);
			$encoder = new BitEncoder($bitmap);
			echo( $encoder->Encode());

		}

		public function GenerateRandomMinefield_WorksWell() {
			$decoder = new BitDecoder("0");
			$minemap = $decoder->GetBitmapArray(8,8);
			$minefield = new Minefield($minemap);
			echo($minefield->ShowField()."\n------------------\n");
			$minefield->GenerateRandomMinefield(0,0);
			$minefield->MineFound();
			echo($minefield->ShowField()."\n------------------\n");
			$minefield->GenerateRandomMinefield(1,1);
			$minefield->MineFound();
			echo($minefield->ShowField()."\n------------------\n");
			$minefield->GenerateRandomMinefield(2,2);
			$minefield->MineFound();
			echo($minefield->ShowField()."\n------------------\n");
			$minefield->GenerateRandomMinefield(3,3);
			echo($minefield->ShowField()."\n------------------\n");
			$minefield->MineFound();
		}
	}
	
	$test = new MinefieldUnitTests();
	//$test->CreateBooleanArray_WorksWell();
	//$test->FullMatch_WorksWell();
	//$test->GetMineBitmapEncoded_WorksWell();
	$test->GenerateRandomMinefield_WorksWell();
?>
