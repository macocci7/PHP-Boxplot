<?php

require_once('../vendor/autoload.php');

use Macocci7\PhpBoxplot\Boxplot;

$bp = new Boxplot();
$bp->config('AdjustDisplayByNeon.neon')
   ->create('img/AdjustDisplayByNeon.png');
