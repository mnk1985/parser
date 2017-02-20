<?php 

require 'vendor/autoload.php';

use App\Parser;
use App\ExcelExporter;

$countryData = (new Parser)->getDataByCountry('IN', 'RU');
//var_dump($countryData);

(new ExcelExporter)->exportTo('prices.xlsx', $countryData);

echo "results saved";



