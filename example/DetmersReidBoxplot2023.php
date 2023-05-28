<?php
require('../vendor/autoload.php');
require('./class/CsvUtil.php');

use Macocci7\PhpBoxplot\Boxplot;
use Macocci7\CsvUtil;

$bp = new Boxplot();
$csvUtil = new CsvUtil();
$csvFileName = 'csv/672282_data.csv';
$dailyData = $csvUtil->getDailyData($csvFileName);
if (!$dailyData) {
    echo "Failed to load CSV data.\n\n";
}
$labels = [];
foreach (array_keys($dailyData) as $datestring) {
    $labels[] = preg_replace('/^\d+\-(\d+)\-(\d+)$/', '$1/$2', $datestring);
}

$filePath01 = 'img/BoxplotDetmersReid2023_01.png';
$filePath02 = 'img/BoxplotDetmersReid2023_02.png';

$bp
   ->setData($dailyData)
   ->setLabels($labels)
   ->setLabelX('Game Date')
   ->setLabelY('MPH')
   ->setCaption('Release Speed: Detmers, Reid')
   ->meanOn()
   ->outlierOn()
   ->jitterOn()
   ->create()
   ->save($filePath01)
   ->outlierOff()
   ->jitterOff()
   ->create()
   ->save($filePath02);
