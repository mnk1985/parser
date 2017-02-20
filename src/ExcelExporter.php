<?php 

namespace App;

class ExcelExporter
{

	public $spreadsheet;
	public $path = __DIR__.'/../results';

	public function __construct()
	{
		$this->spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

		$this->spreadsheet->getProperties()
		    ->setCreator("Alexandr Dychka")
		    ->setLastModifiedBy("Alexandr Dychka")
		    ->setTitle("results")
		    ->setSubject("subject")
		    ->setDescription("skype prices");
	}

	public function exportTo(string $fileName, array $destinations)
	{
		$index = 1;
		foreach($destinations as $tabName => $groupDestinations){
			// Create a new worksheet
			$this->spreadsheet->createSheet();
			// Add some data to the second sheet, resembling some different data types
			$this->spreadsheet->setActiveSheetIndex($index);
			$row = 1;

			foreach($groupDestinations as $destination){
				$this->spreadsheet->getActiveSheet()->setCellValue('A'.$row, $destination['name']);
				$this->spreadsheet->getActiveSheet()->setCellValue('B'.$row, $destination['priceFormatted']);
				$row++;
			}

			// Rename sheet
			$this->spreadsheet->getActiveSheet()->setTitle($tabName);
			$index++;
		}

		$this->spreadsheet->setActiveSheetIndex(0);

		// remove default sheet
		$this->spreadsheet->removeSheetByIndex(0);

		

		$this->saveToDir($fileName);
	}

	protected function saveToDir($fileName)
	{
		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, 'Excel5');
		$writer->save($this->path.'/'.$fileName);
	}

	protected function saveInBrowser($fileName)
	{
		//Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="01simple.xls"');
		header('Cache-Control: max-age=0');
		// If you're serving to IE 9, then the following may be needed
		header('Cache-Control: max-age=1');

		// If you're serving to IE over SSL, then the following may be needed
		header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
		header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
		header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
		header('Pragma: public'); // HTTP/1.0

		$writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, 'Excel5');
		$writer->save('php://output');

	}

}