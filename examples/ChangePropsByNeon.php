<?php

require_once('../vendor/autoload.php');

use Macocci7\PhpBoxplot\Boxplot;

$bp = new Boxplot();
$bp->config('ChangePropsByNeon.neon')
   ->create('img/ChangePropsByNeon.png');
