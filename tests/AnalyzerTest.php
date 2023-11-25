<?php

declare(strict_types=1);

namespace Macocci7\PhpBoxplot;

require('vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use Macocci7\PhpBoxplot\Analyzer;

final class AnalyzerTest extends TestCase
{
    public function test_limit_throw_exception_with_invalid_params(): void
    {
        $cases = [
            ['lower' => null, 'upper' => null, ],
            ['lower' => true, 'upper' => null, ],
            ['lower' => false, 'upper' => null, ],
            ['lower' => '1', 'upper' => null, ],
            ['lower' => [1], 'upper' => null, ],
            ['lower' => 1, 'upper' => null, ],
            ['lower' => 1.2, 'upper' => null, ],
            ['lower' => 1, 'upper' => true, ],
            ['lower' => 1, 'upper' => false, ],
            ['lower' => 1, 'upper' => '2', ],
            ['lower' => 1, 'upper' => [2], ],
            ['lower' => 2, 'upper' => 1, ],
        ];
        foreach ($cases as $case) {
            $a = new Analyzer();
            $this->expectException(\Exception::class);
            $a->limit($case['lower'], $case['upper']);
        }
    }

    public function test_limit_can_set_limit_correctly(): void
    {
        $cases = [
            ['lower' => 1, 'upper' => 2, ],
            ['lower' => 1.2, 'upper' => 2, ],
            ['lower' => 1, 'upper' => 2.4, ],
            ['lower' => 1.2, 'upper' => 2.4, ],
        ];
        foreach ($cases as $case) {
            $a = new Analyzer();
            $a->limit($case['lower'], $case['upper']);
            $this->assertSame($case['lower'], $a->limitLower);
            $this->assertSame($case['upper'], $a->limitUpper);
        }
    }

    public function test_isColorCode_can_judge_correctly(): void
    {
        $cases = [
            ['param' => '', 'expect' => false, ],
            ['param' => 'red', 'expect' => false, ],
            ['param' => 'green', 'expect' => false, ],
            ['param' => 'blue', 'expect' => false, ],
            ['param' => 'black', 'expect' => false, ],
            ['param' => 'white', 'expect' => false, ],
            ['param' => 'orange', 'expect' => false, ],
            ['param' => 'yellow', 'expect' => false, ],
            ['param' => 'purple', 'expect' => false, ],
            ['param' => '000', 'expect' => false, ],
            ['param' => '#000', 'expect' => true, ],
            ['param' => '#999', 'expect' => true, ],
            ['param' => '#aaa', 'expect' => true, ],
            ['param' => '#fff', 'expect' => true, ],
            ['param' => '#ggg', 'expect' => false, ],
            ['param' => '#0000', 'expect' => false, ],
            ['param' => '#00000', 'expect' => false, ],
            ['param' => '000000', 'expect' => false, ],
            ['param' => '#000000', 'expect' => true, ],
            ['param' => '#999999', 'expect' => true, ],
            ['param' => '#aaaaaa', 'expect' => true, ],
            ['param' => '#ffffff', 'expect' => true, ],
            ['param' => '#gggggg', 'expect' => false, ],
            ['param' => '#0000000', 'expect' => false, ],
        ];
        $a = new Analyzer();
        foreach ($cases as $case) {
            $this->assertSame($case['expect'], $a->isColorCode($case['param']));
        }
    }

    public function test_isColorCodeAll_can_judge_correctly(): void
    {
        $cases = [
            ['params' => [], 'expect' => false, ],
            ['params' => ['', '', '', ], 'expect' => false, ],
            ['params' => ['red', 'green', 'blue', ], 'expect' => false, ],
            ['params' => ['#000', '#999', '#fff', ], 'expect' => true, ],
            ['params' => ['#000', '#999', 'fff', ], 'expect' => false, ],
            ['params' => ['#0000', '#9999', '#ffff', ], 'expect' => false, ],
            ['params' => ['#00000', '#99999', '#fffff', ], 'expect' => false, ],
            ['params' => ['#000000', '#999999', '#ffffff', ], 'expect' => true, ],
            ['params' => ['#000000', '#999999', 'ffffff', ], 'expect' => false, ],
            ['params' => ['#0000000', '#9999999', '#fffffff', ], 'expect' => false, ],
        ];
        $a = new Analyzer();
        foreach ($cases as $case) {
            $this->assertSame($case['expect'], $a->isColorCodeAll($case['params']));
        }
    }

    public function test_getMean_can_get_mean_correctly(): void
    {
        $cases = [
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
            if ($case['expect'] !== $bp->getMean($case['data'])) {
                var_dump($case['expect'], $bp->getMean['data']);
            }
            $this->assertSame($case['expect'], $bp->getMean($case['data']));
        }
    }

    public function test_getUcl_can_get_ucl_correctly(): void
    {
        $cases = [
            ['data' => [1], 'expect' => null, ],
            ['data' => [1, 2, ], 'expect' => 3.5, ],
            ['data' => [1, 2, 3, ], 'expect' => 6.0, ],
            ['data' => [1, 2, 3, 4, ], 'expect' => 6.5, ],
            ['data' => [1, 2, 3, 4, 5, ], 'expect' => 9.0, ],
        ];
        foreach ($cases as $case) {
            $a = new Analyzer();
            $cr = (max($case['data']) - min($case['data'])) / 10;
            $a->ft->setClassRange($cr);
            $a->ft->setData($case['data']);
            $a->parsed = $a->ft->parse($case['data']);
            $this->assertSame($case['expect'], $a->getUcl());
        }
    }

    public function test_getLcl_can_get_lcl_correctly(): void
    {
        $cases = [
            ['data' => [1], 'expect' => null, ],
            ['data' => [1, 2, ], 'expect' => -0.5, ],
            ['data' => [1, 2, 3, ], 'expect' => -2.0, ],
            ['data' => [1, 2, 3, 4, ], 'expect' => -1.5, ],
            ['data' => [1, 2, 3, 4, 5, ], 'expect' => -3.0, ],
        ];
        foreach ($cases as $case) {
            $a = new Analyzer();
            $cr = (max($case['data']) - min($case['data'])) / 10;
            $a->ft->setClassRange($cr);
            $a->ft->setData($case['data']);
            $a->parsed = $a->ft->parse($case['data']);
            $this->assertSame($case['expect'], $a->getLcl());
        }
    }

    public function test_getOutliers_can_get_outliers_correctly(): void
    {
        $cases = [
            ['data' => [1], 'expect' => null, ],
            ['data' => [1, 2, ], 'expect' => [], ],
            ['data' => [1, 2, 3, ], 'expect' => [], ],
            ['data' => [1, 2, 3, 4, ], 'expect' => [], ],
            ['data' => [1, 2, 3, 4, 5, ], 'expect' => [], ],
            ['data' => [1, 2, 3, 4, 5, 1000, ], 'expect' => [1000], ],
            ['data' => [1, 2, 3, 4, 5, -1000], 'expect' => [-1000], ],
            ['data' => [1, 2, 3, 4, 5, 6, 7, -10000, 10000 ], 'expect' => [-10000, 10000], ],
        ];
        foreach ($cases as $case) {
            $a = new Analyzer();
            $cr = (max($case['data']) - min($case['data'])) / 10;
            $a->ft->setClassRange($cr);
            $a->ft->setData($case['data']);
            $a->parsed = $a->ft->parse($case['data']);
            $this->assertSame($case['expect'], $a->getOutliers());
        }
    }
}
