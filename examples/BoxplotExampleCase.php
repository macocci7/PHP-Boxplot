<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Macocci7\PhpBoxplot\Boxplot;

$faker = Faker\Factory::create();
$bp = new Boxplot();
$filePath = __DIR__ . '/img/BoxplotExampleCase.png';

$keys = [
    '5/21',
    '5/22',
    '5/23',
    '5/24',
    '5/25',
];

$players = [
    'John',
    'Jake',
    'Hugo',
];

$dataset = [];

foreach ($players as $playre => $name) {
    $waightP = $faker->numberBetween(7, 13) / 10;
    $data = [];
    foreach ($keys as $index => $key) {
        $waightD = $faker->numberBetween(7, 13) / 10;
        $data[$index] = [];
        for ($i = 0; $i < $faker->numberBetween(50, 600); $i++) {
            $data[$index][] = $waightD * $waightP * $faker->numberBetween(600, 1100) / 100;
        }
    }
    $dataset[] = $data;
}

$bp->setDataset($dataset)
   ->resize(600, 400)
   ->bgcolor('#333399')
   ->fontColor('#cccccc')
   ->axisColor('#ff0000')
   ->gridColor('#ff9900')
   ->legendBgcolor('#666699')
   ->boxWidth(20)
   ->boxBorder(1, '#cccccc')
   ->whisker(1, '#cccccc')
   ->gridVerticalOn()
   ->outlierOn()
   ->jitterOff()
   ->meanOn()
   ->legendOn()
   ->gridHeightPitch(2)
   ->labels($keys)
   ->labelX('Index')
   ->labelY('Value')
   ->caption('Random Data')
   ->legends($players)
   ->create($filePath);
