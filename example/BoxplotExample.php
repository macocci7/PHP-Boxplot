<?php
require('../vendor/autoload.php');

use Macocci7\PhpBoxplot\Boxplot;

$data = [
    '1st' => [75, 82, 96, 43, 78, 91, 84, 87, 93],
    '2nd' => [66, 74, 62, 100, 72, 68, 59, 76, 65],
    '3rd' => [56, 0, 45, 76, 58, 52, 13, 48, 54, 68],
    '4th' => [68, 32, 56, 92, 67, 72, 45, 76, 48, 73],
    '5th' => [70, 58, 62, 88, 62, 68, 56, 63, 64, 78],
];

$bp = new Boxplot();

$bp->setData($data)
   ->setLimit(0, 100)
   ->setGridHeightPitch(10)
   ->gridVerticalOn()
   ->outlierOn()
   ->meanOn()
   ->setLabelX('Examination')
   ->setLabelY('Score')
   ->setCaption('Results in 2022')
   ->create('img/BoxplotExample.png');
