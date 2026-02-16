<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PhpBoxplot\Boxplot;

$bp = new Boxplot();
$bp->config(__DIR__ . '/AdjustDisplayByNeon.neon')
   ->create(__DIR__ . '/img/AdjustDisplayByNeon.png');
