<?php

require_once('../vendor/autoload.php');

use Macocci7\PhpBoxplot\Boxplot;

$dataSet = [
    'John' => [
        '1st' => [75, 82, 96, 43, 78, 91, 84, 87, 93, ],
        '2nd' => [66, 74, 62, 100, 72, 68, 59, 76, 65, ],
        '3rd' => [56, 0, 45, 76, 58, 52, 13, 48, 54, 68, ],
        '4th' => [68, 32, 56, 92, 67, 72, 45, 76, 48, 73, ],
        '5th' => [70, 58, 62, 88, 62, 68, 56, 63, 64, 78, ],
    ],
    'Jake' => [
        'test#1' => [62, 35, 48, 43, 56, 78, 32, 24, 29, ],
        'test#2' => [37, 92, 56, 36, 14, 86, 41, 58, 47, ],
        'test#3' => [49, 83, 0, 48, 64, 73, 50, 46, 38, 92, ],
        'test#4' => [53, 44, 34, 51, 74, 68, 53, 86, 24, 66, ],
        'test#5' => [83, 61, 55, 96, 87, 46, 21, 19, 88, 68, ],
    ],
    'Hugo' => [
        'test01' => [73, 36, 0, 11, 40, 76, 24, 46, 83, ],
        'test02' => [69, 42, 76, 8, 92, 84, 45, 34, 67, ],
        'test03' => [100, 46, 34, 77, 85, 47, 91, 85, 66, 79, ],
        'test04' => [0, 14, 32, 24, 54, 44, 56, 32, 59, 38, ],
        'test05' => [69, 84, 65, 42, 33, 80, 74, 54, 75, 56, ],
    ],
];

$bp = new Boxplot();
$bp->setDataset($dataSet)
   ->limit(0, 100)
   ->gridHeightPitch(20)
   ->gridVerticalOn()
   ->legendOn()
   ->meanOn()
   ->labelX('Achievement Test')
   ->labelY('Score')
   ->caption('Achievement Test Results in 2023')
   ->create('img/MultipleDataSet.png');
