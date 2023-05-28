<?php declare(strict_types=1);

require('vendor/autoload.php');
require('src/Boxplot.php');

use PHPUnit\Framework\TestCase;
use Macocci7\PhpBoxplot\Boxplot;

final class BoxplotTest extends TestCase
{
    public function test_getMean_can_get_mean_correctly(): void
    {
        $cases = [
            ['data' => null, 'expect' => null, ],
            ['data' => true, 'expect' => null, ],
            ['data' => false, 'expect' => null, ],
            ['data' => 0, 'expect' => null, ],
            ['data' => 1.2, 'expect' => null, ],
            ['data' => '0', 'expect' => null, ],
            ['data' => [], 'expect' => null, ],
            ['data' => [null], 'expect' => null, ],
            ['data' => [true], 'expect' => null, ],
            ['data' => [false], 'expect' => null, ],
            ['data' => [0], 'expect' => 0, ],
            ['data' => [1.2], 'expect' => 1.2, ],
            ['data' => ['0'], 'expect' => null, ],
            ['data' => ['1.2'], 'expect' => null, ],
            ['data' => [[]], 'expect' => null, ],
            ['data' => [[0]], 'expect' => null, ],
            ['data' => [1,2], 'expect' => 1.5, ],
            ['data' => [-3, 4], 'expect' => 0.5, ],
        ];
        $bp = new Boxplot();
        foreach ($cases as $index => $case) {
            if ($case['expect'] !== $bp->getMean($case['data']))
                var_dump($case['expect'], $bp->getMean['data']);
            $this->assertSame($case['expect'], $bp->getMean($case['data']));
        }
    }
}
