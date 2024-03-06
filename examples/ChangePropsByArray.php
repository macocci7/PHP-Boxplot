<?php

require_once('../vendor/autoload.php');

use Macocci7\PhpBoxplot\Boxplot;

$conf = [
    'dataSet' => [
        [
            '1st' => [75, 82, 96, 43, 78, 91, 84, 87, 93, ],
            '2nd' => [66, 74, 62, 100, 72, 68, 59, 76, 65, ],
            '3rd' => [56, 0, 45, 76, 58, 52, 13, 48, 54, 68, ],
            '4th' => [68, 32, 56, 92, 67, 72, 45, 76, 48, 73, ],
            '5th' => [70, 58, 62, 88, 62, 68, 56, 63, 64, 78, ],
        ],
        [
            '1st' => [62, 35, 48, 43, 56, 78, 32, 24, 29, ],
            '2nd' => [37, 92, 56, 36, 14, 86, 41, 58, 47, ],
            '3rd' => [49, 83, 0, 48, 64, 73, 50, 46, 38, 92, ],
            '4th' => [53, 44, 34, 51, 74, 68, 53, 86, 24, 66, ],
            '5th' => [83, 61, 55, 96, 87, 46, 21, 19, 88, 68, ],
        ],
        [
            '1st' => [73, 36, 0, 11, 40, 76, 24, 46, 83, ],
            '2nd' => [69, 42, 76, 8, 92, 84, 45, 34, 67, ],
            '3rd' => [100, 46, 34, 77, 85, 47, 91, 85, 66, 79, ],
            '4th' => [0, 14, 32, 24, 54, 44, 56, 32, 59, 38, ],
            '5th' => [69, 84, 65, 42, 33, 80, 74, 54, 75, 56, ],
        ],
    ],
    'limitUpper' => 100,
    'limitLower' => 0,
    'canvasBackgroundColor' => '#333399',
    'axisColor' => '#999999',
    'axisWidth' => 2,
    'gridHeightPitch' => 10,
    'gridVertical' => true,
    'whiskerColor' => '#ffff00',
    'fontColor' => '#cccccc',
    'outlier' => true,
    'mean' => true,
    'labelX' => 'Examination',
    'labelY' => 'Score',
    'caption' => 'Results in 2023',
    'legend' => true,
    'legendBackgroundColor' => '#666666',
    'legends' => [ 'Jhon', 'Jake', 'Hugo', ],
    'legendWidth' => 100,
    'legendFontSize' => 10,
];

$bp = new Boxplot();
$bp->config($conf)
   ->create('img/ChangePropsByArray.png');
