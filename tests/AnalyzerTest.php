<?php

declare(strict_types=1);

namespace Macocci7\PhpBoxplot;

require('vendor/autoload.php');

use PHPUnit\Framework\TestCase;
use Macocci7\PhpBoxplot\Analyzer;

/**
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class AnalyzerTest extends TestCase
{
    public static function provide_limit_throw_exception_with_invalid_params(): array
    {
        return [
            ['lower' => 2, 'upper' => 1, ],
            ['lower' => 2, 'upper' => 1.2, ],
            ['lower' => 2.1, 'upper' => 1, ],
            ['lower' => 2.1, 'upper' => 1.2, ],
        ];
    }

    /**
     * @dataProvider provide_limit_throw_exception_with_invalid_params
     */
    public function test_limit_throw_exception_with_invalid_params(int|float $lower, int|float $upper): void
    {
        $a = new Analyzer();
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("lower limit must be less than upper limit.");
        $a->limit($lower, $upper);
    }

    public static function provide_limit_can_set_limit_correctly(): array
    {
        return [
            ['lower' => 1, 'upper' => 2, ],
            ['lower' => 1.2, 'upper' => 2, ],
            ['lower' => 1, 'upper' => 2.4, ],
            ['lower' => 1.2, 'upper' => 2.4, ],
        ];
    }

    /**
     * @dataProvider provide_limit_can_set_limit_correctly
     */
    public function test_limit_can_set_limit_correctly(int|float $lower, int|float $upper): void
    {
        $a = new Analyzer();
        $a->limit($lower, $upper);
        $this->assertSame($lower, $a->getProp('limitLower'));
        $this->assertSame($upper, $a->getProp('limitUpper'));
    }

    public static function provide_getMean_can_get_mean_correctly(): array
    {
        return [
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
    }

    /**
     * @dataProvider provide_getMean_can_get_mean_correctly
     */
    public function test_getMean_can_get_mean_correctly(array $data, int|float|null $expect): void
    {
        $bp = new Boxplot();
        $this->assertSame($expect, $bp->getMean($data));
    }

    public static function provide_getUcl_can_get_ucl_correctly(): array
    {
        return [
            ['data' => [1, 2, ], 'expect' => 3.5, ],
            ['data' => [1, 2, 3, ], 'expect' => 6.0, ],
            ['data' => [1, 2, 3, 4, ], 'expect' => 6.5, ],
            ['data' => [1, 2, 3, 4, 5, ], 'expect' => 9.0, ],
        ];
    }

    /**
     * @dataProvider provide_getUcl_can_get_ucl_correctly
     */
    public function test_getUcl_can_get_ucl_correctly(array $data, float|null $expect): void
    {
        $a = new Analyzer();
        $cr = (max($data) - min($data)) / 10;
        $a->ft->setClassRange($cr);
        $a->ft->setData($data);
        $a->parsed = $a->ft->parse($data);
        $this->assertSame($expect, $a->getUcl());
    }

    public static function provide_getLcl_can_get_lcl_correctly(): array
    {
        return [
            ['data' => [1, 2, ], 'expect' => -0.5, ],
            ['data' => [1, 2, 3, ], 'expect' => -2.0, ],
            ['data' => [1, 2, 3, 4, ], 'expect' => -1.5, ],
            ['data' => [1, 2, 3, 4, 5, ], 'expect' => -3.0, ],
        ];
    }

    /**
     * @dataProvider provide_getLcl_can_get_lcl_correctly
     */
    public function test_getLcl_can_get_lcl_correctly(array $data, float|null $expect): void
    {
        $a = new Analyzer();
        $cr = (max($data) - min($data)) / 10;
        $a->ft->setClassRange($cr);
        $a->ft->setData($data);
        $a->parsed = $a->ft->parse($data);
        $this->assertSame($expect, $a->getLcl());
    }

    public static function provide_getOutliers_can_get_outliers_correctly(): array
    {
        return [
            ['data' => [1, 2, ], 'expect' => [], ],
            ['data' => [1, 2, 3, ], 'expect' => [], ],
            ['data' => [1, 2, 3, 4, ], 'expect' => [], ],
            ['data' => [1, 2, 3, 4, 5, ], 'expect' => [], ],
            ['data' => [1, 2, 3, 4, 5, 1000, ], 'expect' => [1000], ],
            ['data' => [1, 2, 3, 4, 5, -1000], 'expect' => [-1000], ],
            ['data' => [1, 2, 3, 4, 5, 6, 7, -10000, 10000 ], 'expect' => [-10000, 10000], ],
        ];
    }

    /**
     * @dataProvider provide_getOutliers_can_get_outliers_correctly
     */
    public function test_getOutliers_can_get_outliers_correctly(array $data, array|null $expect): void
    {
        $a = new Analyzer();
        $cr = (max($data) - min($data)) / 10;
        $a->ft->setClassRange($cr);
        $a->ft->setData($data);
        $a->parsed = $a->ft->parse($data);
        $this->assertSame($expect, $a->getOutliers());
    }
}
