<?php

namespace Macocci7;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/class/CsvUtil.php';

use Macocci7\PhpBoxplot\Boxplot;
use Macocci7\CsvUtil;

$bp = new Boxplot();
$csvUtil = new CsvUtil();
$csvFileName = __DIR__ . '/csv/690997_data.csv';
$dailyData = $csvUtil->getDailyData($csvFileName);
if (!$dailyData) {
    echo "Failed to load CSV data.\n\n";
}
$labels = [];
foreach (array_keys($dailyData) as $datestring) {
    $labels[] = preg_replace('/^\d+\-(\d+)\-(\d+)$/', '$1/$2', $datestring);
}

$filePath01 = __DIR__ . '/img/BoxplotNolanMcLean2025_01.png';
$filePath02 = __DIR__ . '/img/BoxplotNolanMcLean2025_02.png';

$bp
   ->setData($dailyData)
   ->labels($labels)
   ->labelX('Game Date')
   ->labelY('MPH')
   ->caption('Release Speed: Nolan McLean')
   ->meanOn()
   ->outlierOn()
   ->jitterOn()
   ->create($filePath01)
   ->outlierOff()
   ->jitterOff()
   ->create($filePath02);
