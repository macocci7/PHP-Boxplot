<?php   // phpcs:ignore

declare(strict_types=1);

namespace Macocci7\PhpBoxplot\Traits;

use PHPUnit\Framework\TestCase;
use Macocci7\PhpBoxplot\Traits\JudgeTrait;
use Nette\Neon\Neon;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CamelCaseMethodName)
 */
final class JudgeTraitTest extends TestCase
{
    use JudgeTrait;

    // phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
    // phpcs:disable Generic.Files.LineLength.TooLong

    public static function provide_isIntAll_can_judge_correctly(): array
    {
        return [
            [ 'items' => [], 'expect' => false, ],
            [ 'items' => [null], 'expect' => false, ],
            [ 'items' => [true], 'expect' => false, ],
            [ 'items' => [false], 'expect' => false, ],
            [ 'items' => [[]], 'expect' => false, ],
            [ 'items' => ['1'], 'expect' => false, ],
            [ 'items' => [1.2], 'expect' => false, ],
            [ 'items' => [0], 'expect' => true, ],
            [ 'items' => [-1], 'expect' => true, ],
            [ 'items' => [1], 'expect' => true, ],
            [ 'items' => [ 1, 2, ], 'expect' => true, ],
            [ 'items' => [ -1, 0, 1, ], 'expect' => true, ],
            [ 'items' => [ 1, '2', 3, ], 'expect' => false, ],
            [ 'items' => [ 1, 2.0, 3, ], 'expect' => false, ],
        ];
    }

    /**
     * @dataProvider provide_isIntAll_can_judge_correctly
     */
    public function test_isIntAll_can_judge_correctly(array $items, bool $expect): void
    {
        $this->assertSame($expect, self::isIntAll($items));
    }

    public static function provide_isNumber_can_judge_correctly(): array
    {
        return [
            [ 'item' => null, 'expect' => false, ],
            [ 'item' => true, 'expect' => false, ],
            [ 'item' => false, 'expect' => false, ],
            [ 'item' => '', 'expect' => false, ],
            [ 'item' => [], 'expect' => false, ],
            [ 'item' => 0, 'expect' => true, ],
            [ 'item' => -100, 'expect' => true, ],
            [ 'item' => 100, 'expect' => true, ],
            [ 'item' => 0.0, 'expect' => true, ],
            [ 'item' => -100.5, 'expect' => true, ],
            [ 'item' => 100.5, 'expect' => true, ],
            [ 'item' => '0', 'expect' => false, ],
            [ 'item' => '-100', 'expect' => false, ],
            [ 'item' => '100', 'expect' => false, ],
            [ 'item' => '0.0', 'expect' => false, ],
            [ 'item' => '-100.5', 'expect' => false, ],
            [ 'item' => '100.5', 'expect' => false, ],
        ];
    }

    /**
     * @dataProvider provide_isNumber_can_judge_correctly
     */
    public function test_isNumber_can_judge_correctly(mixed $item, bool $expect): void
    {
        $this->assertSame($expect, self::isNumber($item));
    }

    public static function provide_isNumbersAll_can_judge_correctly(): array
    {
        return [
            [ 'items' => null, 'expect' => false, ],
            [ 'items' => true, 'expect' => false, ],
            [ 'items' => false, 'expect' => false, ],
            [ 'items' => '1', 'expect' => false, ],
            [ 'items' => 1, 'expect' => false, ],
            [ 'items' => 2.3, 'expect' => false, ],
            [ 'items' => [], 'expect' => false, ],
            [ 'items' => [null], 'expect' => false, ],
            [ 'items' => [true], 'expect' => false, ],
            [ 'items' => [false], 'expect' => false, ],
            [ 'items' => ['1'], 'expect' => false, ],
            [ 'items' => [1], 'expect' => true, ],
            [ 'items' => [2.3], 'expect' => true, ],
            [ 'items' => [[1]], 'expect' => false, ],
            [ 'items' => [ 1, 2, ], 'expect' => true, ],
            [ 'items' => [ 1, 2.3, ], 'expect' => true, ],
            [ 'items' => [ 1, 2, '3', ], 'expect' => false, ],
        ];
    }

    /**
     * @dataProvider provide_isNumbersAll_can_judge_correctly
     */
    public function test_isNumbersAll_can_judge_correctly(mixed $items, bool $expect): void
    {
        $this->assertSame($expect, self::isNumbersAll($items));
    }

    public static function provide_isColorCode_can_judge_correctly(): array
    {
        return [
            ['color' => '', 'expect' => false, ],
            ['color' => 'red', 'expect' => false, ],
            ['color' => 'ffffff', 'expect' => false, ],
            ['color' => '#ff', 'expect' => false, ],
            ['color' => '#00', 'expect' => false, ],
            ['color' => '#ffg', 'expect' => false, ],
            ['color' => '#fff', 'expect' => true, ],
            ['color' => '#000', 'expect' => true, ],
            ['color' => '#ffff', 'expect' => false, ],
            ['color' => '#0000', 'expect' => false, ],
            ['color' => '#fffff', 'expect' => false, ],
            ['color' => '#00000', 'expect' => false, ],
            ['color' => '#fffffg', 'expect' => false, ],
            ['color' => '#ffffff', 'expect' => true, ],
            ['color' => '#000000', 'expect' => true, ],
            ['color' => '#f0f0f0', 'expect' => true, ],
            ['color' => '#0f0f0f', 'expect' => true, ],
            ['color' => '#fffffff', 'expect' => false, ],
            ['color' => '#0000000', 'expect' => false, ],
        ];
    }

    /**
     * @dataProvider provide_isColorCode_can_judge_correctly
     */
    public function test_isColorCode_can_judge_correctly(string $color, bool $expect): void
    {
        $this->assertSame($expect, self::isColorCode($color));
    }

    public static function provide_isColorCodeAll_can_judge_correctly(): array
    {
        return [
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
    }

    /**
     * @dataProvider provide_isColorCodeAll_can_judge_correctly
     */
    public function test_isColorCodeAll_can_judge_correctly(array $params, bool $expect): void
    {
        $this->assertSame($expect, self::isColorCodeAll($params));
    }

    public static function provide_isValidType_can_judge_correctly(): array
    {
        return [
            [ 'input' => null, 'def' => 'int', 'expect' => false, ],
            [ 'input' => true, 'def' => 'int', 'expect' => false, ],
            [ 'input' => false, 'def' => 'int', 'expect' => false, ],
            [ 'input' => [], 'def' => 'int', 'expect' => false, ],
            [ 'input' => '1', 'def' => 'int', 'expect' => false, ],
            [ 'input' => 1, 'def' => 'int', 'expect' => true, ],
            [ 'input' => 1.5, 'def' => 'int', 'expect' => false, ],
            [ 'input' => null, 'def' => 'float', 'expect' => false, ],
            [ 'input' => true, 'def' => 'float', 'expect' => false, ],
            [ 'input' => false, 'def' => 'float', 'expect' => false, ],
            [ 'input' => [], 'def' => 'float', 'expect' => false, ],
            [ 'input' => '1.5', 'def' => 'float', 'expect' => false, ],
            [ 'input' => 1, 'def' => 'float', 'expect' => false, ],
            [ 'input' => 1.5, 'def' => 'float', 'expect' => true, ],
            [ 'input' => null, 'def' => 'bool', 'expect' => false, ],
            [ 'input' => true, 'def' => 'bool', 'expect' => true, ],
            [ 'input' => false, 'def' => 'bool', 'expect' => true, ],
            [ 'input' => [], 'def' => 'bool', 'expect' => false, ],
            [ 'input' => 'true', 'def' => 'bool', 'expect' => false, ],
            [ 'input' => 1, 'def' => 'bool', 'expect' => false, ],
            [ 'input' => 1.5, 'def' => 'bool', 'expect' => false, ],
            [ 'input' => null, 'def' => 'string', 'expect' => false, ],
            [ 'input' => true, 'def' => 'string', 'expect' => false, ],
            [ 'input' => false, 'def' => 'string', 'expect' => false, ],
            [ 'input' => [], 'def' => 'string', 'expect' => false, ],
            [ 'input' => '', 'def' => 'string', 'expect' => true, ],
            [ 'input' => 1, 'def' => 'string', 'expect' => false, ],
            [ 'input' => 1.5, 'def' => 'string', 'expect' => false, ],
            [ 'input' => null, 'def' => 'array', 'expect' => false, ],
            [ 'input' => true, 'def' => 'array', 'expect' => false, ],
            [ 'input' => false, 'def' => 'array', 'expect' => false, ],
            [ 'input' => [], 'def' => 'array', 'expect' => true, ],
            [ 'input' => 1, 'def' => 'array', 'expect' => false, ],
            [ 'input' => 1.5, 'def' => 'array', 'expect' => false, ],
            [ 'input' => null, 'def' => 'number', 'expect' => false, ],
            [ 'input' => true, 'def' => 'number', 'expect' => false, ],
            [ 'input' => false, 'def' => 'number', 'expect' => false, ],
            [ 'input' => [], 'def' => 'number', 'expect' => false, ],
            [ 'input' => '1', 'def' => 'number', 'expect' => false, ],
            [ 'input' => 1, 'def' => 'number', 'expect' => true, ],
            [ 'input' => 1.5, 'def' => 'number', 'expect' => true, ],
            [ 'input' => null, 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => true, 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => false, 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => [], 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => '', 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => 1, 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => 1.5, 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => 'fff', 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => 'ffffff', 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => '#fff', 'def' => 'colorCode', 'expect' => true, ],
            [ 'input' => '#000', 'def' => 'colorCode', 'expect' => true, ],
            [ 'input' => '#0f0', 'def' => 'colorCode', 'expect' => true, ],
            [ 'input' => '#ggg', 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => '#0fg', 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => '#fffff', 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => '#ffffff', 'def' => 'colorCode', 'expect' => true, ],
            [ 'input' => '#fffffff', 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => '#000000', 'def' => 'colorCode', 'expect' => true, ],
            [ 'input' => '#0f0f0f', 'def' => 'colorCode', 'expect' => true, ],
            [ 'input' => '#0f0f0g', 'def' => 'colorCode', 'expect' => false, ],
            [ 'input' => 1, 'def' => 'int|null', 'expect' => true, ],
            [ 'input' => true, 'def' => 'int|null', 'expect' => false, ],
            [ 'input' => false, 'def' => 'int|null', 'expect' => false, ],
            [ 'input' => null, 'def' => 'int|null', 'expect' => true, ],
            [ 'input' => 1.5, 'def' => 'int|null', 'expect' => false, ],
            [ 'input' => '1', 'def' => 'int|null', 'expect' => false, ],
            [ 'input' => [], 'def' => 'int|null', 'expect' => false, ],
            [ 'input' => true, 'def' => 'int|null|string', 'expect' => false, ],
            [ 'input' => false, 'def' => 'int|null|string', 'expect' => false, ],
            [ 'input' => 1, 'def' => 'int|null|string', 'expect' => true, ],
            [ 'input' => 1.5, 'def' => 'int|null|string', 'expect' => false, ],
            [ 'input' => [], 'def' => 'int|null|string', 'expect' => false, ],
            [ 'input' => null, 'def' => 'int|null|string', 'expect' => true, ],
            [ 'input' => 'hoge', 'def' => 'int|null|string', 'expect' => true, ],
        ];
    }

    /**
     * @dataProvider provide_isValidType_can_judge_correctly
     */
    public function test_isValidType_can_judge_correctly(mixed $input, string $def, bool $expect): void
    {
        $this->assertSame($expect, self::isValidType($input, $def));
    }

    public static function provide_isValidData_can_judge_correctly(): array
    {
        return [
            [ 'data' => null, 'expect' => false, ],
            [ 'data' => true, 'expect' => false, ],
            [ 'data' => false, 'expect' => false, ],
            [ 'data' => 1, 'expect' => false, ],
            [ 'data' => 2.3, 'expect' => false, ],
            [ 'data' => '1', 'expect' => false, ],
            [ 'data' => [], 'expect' => false, ],
            [ 'data' => [null], 'expect' => false, ],
            [ 'data' => [true], 'expect' => false, ],
            [ 'data' => [false], 'expect' => false, ],
            [ 'data' => [1], 'expect' => false, ],
            [ 'data' => [2.3], 'expect' => false, ],
            [ 'data' => ['1'], 'expect' => false, ],
            [ 'data' => [[]], 'expect' => false, ],
            [ 'data' => [[null]], 'expect' => false, ],
            [ 'data' => [[true]], 'expect' => false, ],
            [ 'data' => [[false]], 'expect' => false, ],
            [ 'data' => [['1']], 'expect' => false, ],
            [ 'data' => [[[1]]], 'expect' => false, ],
            [ 'data' => [[1]], 'expect' => true, ],
            [ 'data' => [[2.3]], 'expect' => true, ],
            [ 'data' => [[ 1, 2, ]], 'expect' => true, ],
            [ 'data' => [[ 1, 2.3, ]], 'expect' => true, ],
            [ 'data' => [[ 1, 2.3, true ]], 'expect' => false, ],
        ];
    }

    /**
     * @dataProvider provide_isValidData_can_judge_correctly
     */
    public function test_isValidData_can_judge_correctly(mixed $data, bool $expect): void
    {
        $this->assertSame($expect, self::isValidData($data));
    }

    public static function provide_isValidDataset_can_judge_correctly(): array
    {
        return [
            [ 'dataset' => null, 'expect' => false, ],
            [ 'dataset' => true, 'expect' => false, ],
            [ 'dataset' => false, 'expect' => false, ],
            [ 'dataset' => 1, 'expect' => false, ],
            [ 'dataset' => 2.3, 'expect' => false, ],
            [ 'dataset' => '1', 'expect' => false, ],
            [ 'dataset' => [], 'expect' => false, ],
            [ 'dataset' => [null], 'expect' => false, ],
            [ 'dataset' => [true], 'expect' => false, ],
            [ 'dataset' => [false], 'expect' => false, ],
            [ 'dataset' => [1], 'expect' => false, ],
            [ 'dataset' => [2.3], 'expect' => false, ],
            [ 'dataset' => ['1'], 'expect' => false, ],
            [ 'dataset' => [[]], 'expect' => false, ],
            [ 'dataset' => [[null]], 'expect' => false, ],
            [ 'dataset' => [[true]], 'expect' => false, ],
            [ 'dataset' => [[false]], 'expect' => false, ],
            [ 'dataset' => [['1']], 'expect' => false, ],
            [ 'dataset' => [[[1]]], 'expect' => true, ],
            [ 'dataset' => [[1]], 'expect' => false, ],
            [ 'dataset' => [[2.3]], 'expect' => false, ],
            [ 'dataset' => [[ 1, 2, ]], 'expect' => false, ],
            [ 'dataset' => [[ 1, 2.3, ]], 'expect' => false, ],
            [ 'dataset' => [[ 1, 2.3, true ]], 'expect' => false, ],
            [
                'dataset' => [
                    'John' => [
                        [ 1, 2.3, ],
                    ],
                ],
                'expect' => true,
            ],
            [
                'dataset' => [
                    'John' => [
                        [ 1, 2.3, ],
                        [ 4, 5.6, ],
                    ],
                    'Jake' => [
                        [ 1, 2.3, ],
                        [ 4, 5.6, ],
                        [ 7, 8.9, ],
                    ],
                ],
                'expect' => true,
            ],
            [
                'dataset' => [
                    'John' => [
                        [ 1, 2.3, ],
                        [ 4, 5.6, ],
                    ],
                    'Jake' => [
                        [ 1, 2.3, ],
                        [ 4, 5.6, ],
                        [ 7, 8.9, ],
                    ],
                    'Hugo' => [
                        [ 1, 2.3, ],
                        [ 4, 5.6, ],
                        [ 7, 8.9, ],
                        [ null ],
                    ],
                ],
                'expect' => false,
            ],
        ];
    }

    /**
     * @dataProvider provide_isValidDataset_can_judge_correctly
     */
    public function test_isValidDataset_can_judge_correctly(mixed $dataset, bool $expect): void
    {
        $this->assertSame($expect, self::isValidDataset($dataset));
    }
}
