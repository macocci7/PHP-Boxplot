<?php

namespace Macocci7\PhpBoxplot;

use Macocci7\PhpBoxplot\Analyzer;
use Macocci7\PhpBoxplot\Helpers\Config;
use Macocci7\PhpPlotter2d\Canvas;
use Macocci7\PhpPlotter2d\Plotter as Plotter2d;
use Macocci7\PhpPlotter2d\Transformer;

/**
 * class for analysis
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 */
class Plotter extends Analyzer
{
    use Traits\StyleTrait;
    use Traits\AttributeTrait;
    use Traits\VisibilityTrait;

    protected Canvas $canvas;
    protected Transformer $transformer;
    /**
     * @var array<string, int[]>    $viewport
     */
    protected array $viewport = [];
    /**
     * @var array{
     *  offset: int[],
     *  width:  int,
     *  height: int,
     *  backgroundColor:    string,
     *  placeAutomatically: bool,
     * }    $plotarea
     */
    protected array $plotarea;
    protected float $frameXRatio;
    protected float $frameYRatio;
    protected int $axisWidth;
    protected int $gridWidth;
    protected int|float|null $gridWidthPitch;
    protected int $gridMax;
    protected int $gridMin;
    protected int $boxCount;
    protected int $baseX;
    protected int $baseY;
    protected int $legendCount;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadConf();
    }

    /**
     * loads config.
     * @return  void
     */
    private function loadConf()
    {
        Config::load();
        foreach (array_keys(Config::get('validConfig')) as $prop) {
            $this->{$prop} = Config::get($prop);
        }
    }

    /**
     * sets properties
     * @return  self
     */
    private function setProperties()
    {
        $this->legendCount = count($this->dataSet);
        if (!$this->boxBackgroundColors) {
            $this->boxBackgroundColors = $this->colors;
        }
        $counts = [];
        foreach ($this->dataSet as $values) {
            $counts[] = count($values);
        }
        $this->boxCount = max($counts);
        $maxValues = [];
        foreach ($this->dataSet as $data) {
            foreach ($data as $key => $values) {
                $maxValues[] = max($values);
            }
        }
        $maxValue = max($maxValues);
        $minValues = [];
        foreach ($this->dataSet as $data) {
            foreach ($data as $key => $values) {
                $minValues[] = min($values);
            }
        }
        $minValue = min($minValues);
        if (isset($this->limitUpper)) {
            $this->gridMax = $this->limitUpper;
        } else {
            $this->gridMax = ((int) ($maxValue + ($maxValue - $minValue) * 0.1) * 10 ) / 10;
        }
        if (isset($this->limitLower)) {
            $this->gridMin = $this->limitLower;
        } else {
            $this->gridMin = ((int) ($minValue - ($maxValue - $minValue) * 0.1) * 10 ) / 10;
        }
        $gridHeightSpan = $this->gridMax - $this->gridMin;
        // Note:
        // - The Class Range affects the accuracy of the Mean Value.
        // - This value should be set appropriately: 10% of $gridHeightSpan in this case.
        $clsasRange = ((int) ($gridHeightSpan * 10)) / 100;
        $this->ft->setClassRange($clsasRange);

        $this->setDefaultPlotarea();
        $this->setDefaultViewport();
        $this->adjustGridHeightPitch($gridHeightSpan);
        $this->createCanvas();

        // Note:
        // - If $this->labels has values, those values takes precedence.
        // - The values of $this->labels may be set by the method labels().
        if (empty($this->labels)) {
            $this->labels = array_keys($this->dataSet[array_keys($this->dataSet)[0]]);
        }
        return $this;
    }

    /**
     * sets default viewport
     */
    private function setDefaultViewport(): void
    {
        $xMin = 0;
        $xMax = max(array_map(fn ($e) => count($e), $this->dataSet));
        $yMin = $this->gridMin;
        $yMax = $this->gridMax;

        $this->viewport = [
            'x' => [$xMin, $xMax],
            'y' => [$yMin, $yMax],
        ];
    }

    /**
     * sets default plotarea
     */
    private function setDefaultPlotarea(): void
    {
        $plotarea = $this->plotarea;
        if (!array_key_exists('offset', $plotarea)) {
            $plotarea['offset'] = [
                (int) round(
                    $this->canvasWidth * (1 - $this->frameXRatio) * 3 / 4
                ),
                (int) round(
                    $this->canvasHeight * (1 - $this->frameYRatio) / 2
                ),
            ];
        }
        if (!array_key_exists('width', $plotarea)) {
            $plotarea['width'] = (int) round(
                $this->canvasWidth * $this->frameXRatio
            );
        }
        if (!array_key_exists('height', $plotarea)) {
            $plotarea['height'] = (int) round(
                $this->canvasHeight * $this->frameYRatio
            );
        }
        if (!array_key_exists('placeAutomatically', $plotarea)) {
            $plotarea['placeAutomatically'] = false;
        }
        $this->plotarea = $plotarea;
    }

    /**
     * adjusts gridHeightPitch
     *
     * @param   float   $gridHeightSpan
     */
    private function adjustGridHeightPitch(float $gridHeightSpan): void
    {
        // Note:
        // - If $this->gridHeightPitch has a value, that value takes precedence.
        // - The value of $this->girdHeightPitch may be set by the funciton gridHeightPitch().
        if (!$this->gridHeightPitch) {
            $this->gridHeightPitch = 1;
            if ($this->gridHeightPitch < 0.125 * $gridHeightSpan) {
                $this->gridHeightPitch = ( (int) (0.125 * $gridHeightSpan * 10)) / 10;
            }
            if ($this->gridHeightPitch > 0.2 * $gridHeightSpan) {
                $this->gridHeightPitch = ( (int) (0.200 * $gridHeightSpan * 10)) / 10;
            }
        }
    }

    /**
     * creates canvas
     */
    private function createCanvas(): void
    {
        $this->canvas = Plotter2d::make(
            canvasSize: [
                'width' => $this->canvasWidth,
                'height' => $this->canvasHeight,
            ],
            viewport: $this->viewport,
            plotarea: $this->plotarea,
            backgroundColor: $this->canvasBackgroundColor,
        );
        $this->transformer = new Transformer(
            viewport: $this->viewport,
            plotarea: $this->plotarea,
        );
    }

    /**
     * plots axis
     * @return  self
     */
    private function plotAxis()
    {
        if (!$this->showAxis) {
            return $this;
        }
        [$offsetX, $offsetY] = $this->plotarea['offset'];
        $x1 = $offsetX;
        $y1 = $this->plotarea['height'] + $offsetY;
        $x2 = $x1;
        $y2 = $offsetY;
        $this->canvas->drawLine(
            x1: $x1,
            y1: $y1,
            x2: $x2,
            y2: $y2,
            width: $this->axisWidth,
            color: $this->axisColor,
        );
        // plot x-axis
        $x1 = $offsetX;
        $y1 = $this->plotarea['height'] + $offsetY;
        $x2 = $this->plotarea['width'] + $offsetX;
        $y2 = $y1;
        $this->canvas->drawLine(
            x1: $x1,
            y1: $y1,
            x2: $x2,
            y2: $y2,
            width: $this->axisWidth,
            color: $this->axisColor,
        );
        return $this;
    }

    /**
     * plots grids
     * @return  self
     */
    private function plotGrids()
    {
        $this->plotGridHorizontal();
        $this->plotGridVertical();
        return $this;
    }

    /**
     * plots horizontal grids
     * @return  self
     */
    private function plotGridHorizontal()
    {
        [$offsetX, $offsetY] = $this->plotarea['offset'];
        for ($y = $this->gridMin; $y <= $this->gridMax; $y += $this->gridHeightPitch) {
            [[$x1, $y1], [$x2, $y2]] = $this->transformer->getCoords([
                [0, $y],
                [$this->viewport['x'][1], $y],
            ]);
            $this->canvas->drawLine(
                x1: $x1 + $offsetX,
                y1: $y1 + $offsetY,
                x2: $x2 + $offsetX,
                y2: $y2 + $offsetY,
                width: $this->gridWidth,
                color: $this->gridColor,
            );
        }
        return $this;
    }

    /**
     * plots vertical grid
     * @return  self
     */
    private function plotGridVertical()
    {
        if (!$this->gridVertical) {
            return $this;
        }
        [$offsetX, $offsetY] = $this->plotarea['offset'];
        [$xMin, $xMax] = $this->viewport['x'];
        [$yMin, $yMax] = $this->viewport['y'];
        for ($x = $xMin; $x <= $xMax; $x++) {
            [[$x1, $y1], [$x2, $y2]] = $this->transformer->getCoords([
                [$x, $yMin],
                [$x, $yMax],
            ]);
            $this->canvas->drawLine(
                x1: $x1 + $offsetX,
                y1: $y1 + $offsetY,
                x2: $x2 + $offsetX,
                y2: $y2 + $offsetY,
                width: $this->gridWidth,
                color: $this->gridColor,
            );
        }
        return $this;
    }

    /**
     * plots grid values
     * @return  self
     */
    private function plotGridValues()
    {
        [$offsetX, $offsetY] = $this->plotarea['offset'];
        for ($y = $this->gridMin; $y <= $this->gridMax; $y += $this->gridHeightPitch) {
            [[$x1, $y1]] = $this->transformer->getCoords([[0, $y]]);
            $this->canvas->drawText(
                text: (string) $y,
                x: $x1 + $offsetX - 8,
                y: $y1 + $offsetY,
                fontSize: $this->fontSize,
                fontPath: $this->fontPath,
                fontColor: $this->fontColor,
                align: 'right',
                valign: 'middle',
            );
        }
        return $this;
    }

    /**
     * plots a box
     * @param   int $index
     * @param   int $legend
     * @return  self
     */
    private function plotBox(int $index, int $legend)
    {
        [$offsetX, $offsetY] = $this->plotarea['offset'];
        $rateX = $this->transformer->getRateX();
        $boxWidth = $this->boxWidth / $rateX;
        $legends = $this->legendCount;
        $offsetXL = ($legend + 0.5) / $legends - 0.5 * $boxWidth;
        [[$x1, $y1], [$x2, $y2]] = $this->transformer->getCoords([
            [$index + $offsetXL, $this->parsed['ThirdQuartile']],
            [$index + $offsetXL + $boxWidth, $this->parsed['FirstQuartile']],
        ]);
        $this->canvas->drawBox(
            x1: $x1 + $offsetX,
            y1: $y1 + $offsetY,
            x2: $x2 + $offsetX,
            y2: $y2 + $offsetY,
            backgroundColor: $this->boxBackgroundColors[$legend],
            borderWidth: $this->boxBorderWidth,
            borderColor: $this->boxBorderColor,
        );
        return $this;
    }

    /**
     * plots meadian
     * @param   int $index
     * @param   int $legend
     * @return  self
     */
    private function plotMedian(int $index, int $legend)
    {
        [$offsetX, $offsetY] = $this->plotarea['offset'];
        $legends = $this->legendCount;
        $rateX = $this->transformer->getRateX();
        $boxWidth = $this->boxWidth / $rateX;
        $offsetXL = ($legend + 0.5) / $legends - 0.5 * $boxWidth;
        [[$x1, $y1], [$x2, $y2]] = $this->transformer->getCoords([
            [$index + $offsetXL, $this->parsed['Median']],
            [$index + $offsetXL + $boxWidth, $this->parsed['Median']],
        ]);
        $this->canvas->drawLine(
            x1: $x1 + $offsetX,
            y1: $y1 + $offsetY,
            x2: $x2 + $offsetX,
            y2: $y2 + $offsetY,
            width: $this->boxBorderWidth,
            color: $this->boxBorderColor,
        );
        return $this;
    }

    /**
     * plots mean
     * @param   int $index
     * @param   int $legend
     * @return  self
     */
    private function plotMean(int $index, int $legend)
    {
        if (!$this->mean) {
            return $this;
        }
        [$offsetX, $offsetY] = $this->plotarea['offset'];
        $mean = $this->getMean($this->parsed['data']);
        $legends = $this->legendCount;
        $offsetXL = ($legend + 0.5) / $legends;
        ['x' => $x, 'y' => $y] = $this->transformer->getCoord(
            $index + $offsetXL,
            $mean,
        );
        $this->canvas->drawText(
            text: '+',
            x: $x + $offsetX,
            y: $y + $offsetY,
            fontSize: $this->fontSize,
            fontPath: $this->fontPath,
            fontColor: $this->meanColor,
            align: 'center',
            valign: 'middle',
        );
        return $this;
    }

    /**
     * plot an upper whisker
     * @param   int $index
     * @param   int $legend
     * @return  self
     */
    private function plotWhiskerUpper(int $index, int $legend)
    {
        // upper whisker
        [$offsetX, $offsetY] = $this->plotarea['offset'];
        $legends = $this->legendCount;
        $offsetXL = ($legend + 0.5) / $legends;
        $x1 = $index +  $offsetXL;
        if ($this->outlier) {
            $max = $this->parsed['Max'];
            $ucl = $this->getUcl();
            $max = ($ucl > $max) ? $max : $ucl;
            $y1 = $max;
        } else {
            $y1 = $this->parsed['Max'];
        }
        $x2 = $x1;
        $y2 = $this->parsed['ThirdQuartile'];
        [[$tx1, $ty1], [$tx2, $ty2]] = $this->transformer->getCoords([
            [$x1, $y1],
            [$x2, $y2],
        ]);
        $this->canvas->drawLine(
            x1: $tx1 + $offsetX,
            y1: $ty1 + $offsetY,
            x2: $tx2 + $offsetX,
            y2: $ty2 + $offsetY,
            width: $this->whiskerWidth,
            color: $this->whiskerColor,
        );
        // top bar
        $rateX = $this->transformer->getRateX();
        $boxWidth = $this->boxWidth / $rateX;
        $x1 = $x1 - $boxWidth / 4;
        $x2 = $x1 + $boxWidth / 2;
        $y2 = $y1;
        [[$tx1, $ty1], [$tx2, $ty2]] = $this->transformer->getCoords([
            [$x1, $y1],
            [$x2, $y2],
        ]);
        $this->canvas->drawLine(
            x1: $tx1 + $offsetX,
            y1: $ty1 + $offsetY,
            x2: $tx2 + $offsetX,
            y2: $ty2 + $offsetY,
            width: $this->whiskerWidth,
            color: $this->whiskerColor,
        );
        return $this;
    }

    /**
     * plots a lower whisker
     * @param   int $index
     * @param   int $legend
     * @return  self
     */
    private function plotWhiskerLower(int $index, int $legend)
    {
        // lower whisker
        [$offsetX, $offsetY] = $this->plotarea['offset'];
        $legends = $this->legendCount;
        $offsetXL = ($legend + 0.5) / $legends;
        $x1 = $index + $offsetXL;
        $y1 = $this->parsed['FirstQuartile'];
        $x2 = $x1;
        if ($this->outlier) {
            $min = $this->parsed['Min'];
            $lcl = $this->getLcl();
            $min = ($lcl < $min) ? $min : $lcl;
            $y2 = $min;
        } else {
            $y2 = $this->parsed['Min'];
        }
        [[$tx1, $ty1], [$tx2, $ty2]] = $this->transformer->getCoords([
            [$x1, $y1],
            [$x2, $y2],
        ]);
        $this->canvas->drawLine(
            x1: $tx1 + $offsetX,
            y1: $ty1 + $offsetY,
            x2: $tx2 + $offsetX,
            y2: $ty2 + $offsetY,
            width: $this->whiskerWidth,
            color: $this->whiskerColor,
        );
        // bottom bar
        $rateX = $this->transformer->getRateX();
        $boxWidth = $this->boxWidth / $rateX;
        $x1 = $x1 - $boxWidth / 4;
        $y1 = $y2;
        $x2 = $x1 + $boxWidth / 2;
        [[$tx1, $ty1], [$tx2, $ty2]] = $this->transformer->getCoords([
            [$x1, $y1],
            [$x2, $y2],
        ]);
        $this->canvas->drawLine(
            x1: $tx1 + $offsetX,
            y1: $ty1 + $offsetY,
            x2: $tx2 + $offsetX,
            y2: $ty2 + $offsetY,
            width: $this->whiskerWidth,
            color: $this->whiskerColor,
        );
        return $this;
    }

    /**
     * plots whiskers
     * @param   int $index
     * @param   int $legend
     * @return  self
     */
    private function plotWhisker(int $index, int $legend)
    {
        $this->plotWhiskerUpper($index, $legend);
        $this->plotWhiskerLower($index, $legend);
        return $this;
    }

    /**
     * plots outliers
     * @param   int $index
     * @param   int $legend
     * @return  self
     */
    private function plotOutliers(int $index, int $legend)
    {
        if (!$this->outlier) {
            return $this;
        }
        [$offsetX, $offsetY] = $this->plotarea['offset'];
        $legends = $this->legendCount;
        foreach ($this->getOutliers() as $outlier) {
            $offsetXL = ($legend + 0.5) / $legends;
            ['x' => $x, 'y' => $y] = $this->transformer->getCoord(
                $index + $offsetXL,
                $outlier,
            );
            $this->canvas->drawCircle(
                x: $x + $offsetX,
                y: $y + $offsetY,
                radius: (int) ($this->outlierDiameter / 2),
                backgroundColor: $this->outlierColor,
                borderWidth: 1,
                borderColor: $this->outlierColor,
            );
        }
        return $this;
    }

    /**
     * plots jitter
     * @param   int $index
     * @param   int $legend
     * @return  self
     */
    private function plotJitter(int $index, int $legend)
    {
        if (!$this->jitter) {
            return $this;
        }
        if (!array_key_exists('data', $this->parsed)) {
            return $this;
        }
        $data = $this->parsed['data'];
        if (empty($data)) {
            return $this;
        }
        [$offsetX, $offsetY] = $this->plotarea['offset'];
        $legends = $this->legendCount;
        $rateX = $this->transformer->getRateX();
        $boxWidth = $this->boxWidth / $rateX;
        $baseX = $index + ($legend + 0.5) / $legends - $boxWidth / 2;
        $pitchX = $boxWidth / count($data);
        foreach ($data as $key => $value) {
            ['x' => $x, 'y' => $y] = $this->transformer->getCoord(
                $baseX + $key * $pitchX,
                $value,
            );
            $this->canvas->drawCircle(
                x: $x + $offsetX,
                y: $y + $offsetY,
                radius: (int) ($this->jitterDiameter / 2),
                backgroundColor: $this->jitterColor,
                borderWidth: 1,
                borderColor: $this->jitterColor,
            );
        }
        return $this;
    }

    /**
     * plots labels
     * @return  self
     */
    private function plotLabels()
    {
        if (!is_array($this->labels)) {
            return $this;
        }
        $offset = $this->plotarea['offset'];
        $baseX = $offset[0];
        $baseY = $this->plotarea['height'] + $offset[1];
        $gridSpanX = $this->transformer->getSpanX(1);
        foreach ($this->labels as $index => $label) {
            if (!is_string($label) && !is_numeric($label)) {
                continue;
            }
            $x = (int) ($baseX + ($index + 0.5) * $gridSpanX);
            $y = (int) ($baseY + $this->fontSize * 1.2);
            $this->canvas->drawText(
                text: (string) $label,
                x: $x,
                y: $y,
                fontSize: $this->fontSize,
                fontPath: $this->fontPath,
                fontColor: $this->fontColor,
                align: 'center',
                valign: 'bottom',
            );
        }
        return $this;
    }

    /**
     * plots label of X
     * @return  self
     */
    private function plotLabelX()
    {
        $coord = $this->transformer->getCoord(0, 0);
        $offset = $this->plotarea['offset'];
        $baseY = $coord['y'] + $offset[1];
        $x = (int) ($this->canvasWidth / 2);
        $y = (int) ($baseY + (1 - $this->frameYRatio) * $this->canvasHeight / 3);
        $this->canvas->drawText(
            text: (string) $this->labelX,
            x: $x,
            y: $y,
            fontSize: $this->fontSize,
            fontPath: $this->fontPath,
            fontColor: $this->fontColor,
            align: 'center',
            valign: 'bottom',
        );
        return $this;
    }

    /**
     * plots label of Y
     * @return  self
     */
    private function plotLabelY()
    {
        $width = $this->canvasHeight;
        $height = (int) ($this->canvasWidth * (1 - $this->frameXRatio) / 3);
        $x = $width / 2;
        $y = ($height + $this->fontSize) / 2;
        $this->canvas->drawText(
            text: (string) $this->labelY,
            x: $x,
            y: $y,
            fontSize: $this->fontSize,
            fontPath: $this->fontPath,
            fontColor: $this->fontColor,
            align: 'center',
            valign: 'bottom',
            angle: 90.0,
            rotateAlign: 'left',
            rotateValign: 'bottom',
        );
        return $this;
    }

    /**
     * plots caption
     * @return  self
     */
    private function plotCaption()
    {
        $x = (int) ($this->canvasWidth / 2);
        $y = (int) ($this->canvasHeight * (1 - $this->frameYRatio) / 3);
        $this->canvas->drawText(
            text: (string) $this->caption,
            x: $x,
            y: $y,
            fontSize: $this->fontSize,
            fontPath: $this->fontPath,
            fontColor: $this->fontColor,
            align: 'center',
            valign: 'bottom',
        );
        return $this;
    }

    /**
     * plots a legend
     * @return  self
     */
    private function plotLegend()
    {
        if (!$this->legend) {
            return $this;
        }
        $baseX = (int) ($this->canvasWidth * (3 + $this->frameXRatio) / 4 - $this->legendWidth);
        $baseY = 10;
        $x1 = $baseX;
        $y1 = $baseY;
        $x2 = $x1 + $this->legendWidth;
        $y2 = (int) ($y1 + $this->legendFontSize * 1.2 * $this->legendCount + 8);
        $this->canvas->drawBox(
            x1: $x1,
            y1: $y1,
            x2: $x2,
            y2: $y2,
            backgroundColor: $this->legendBackgroundColor,
            borderWidth: $this->boxBorderWidth,
            borderColor: $this->boxBorderColor,
        );
        for ($i = 0; $i < $this->legendCount; $i++) {
            if (empty($this->legends[$i])) {
                $label = 'unknown ' . $i;
            } else {
                $label = $this->legends[$i];
            }
            $x1 = (int) ($baseX + 4);
            $y1 = (int) ($baseY + $i * $this->legendFontSize * 1.2 + 4);
            $x2 = (int) ($x1 + 20);
            $y2 = (int) ($y1 + $this->legendFontSize);
            $this->canvas->drawBox(
                x1: $x1,
                y1: $y1,
                x2: $x2,
                y2: $y2,
                backgroundColor: $this->boxBackgroundColors[$i],
                borderWidth: $this->boxBorderWidth,
                borderColor: $this->boxBorderColor,
            );
            $x = $x2 + 4;
            $y = $y1;
            $this->canvas->drawText(
                text: (string) $label,
                x: $x,
                y: $y,
                fontSize: $this->legendFontSize,
                fontPath: $this->fontPath,
                fontColor: $this->fontColor,
                align: 'left',
                valign: 'top',
            );
        }
        return $this;
    }

    /**
     * plots stuffs
     * @param   int $index
     * @param   int $legend
     * @return  self
     */
    private function plot(int $index, int $legend)
    {
        $this->plotBox($index, $legend);
        $this->plotMedian($index, $legend);
        $this->plotMean($index, $legend);
        $this->plotWhisker($index, $legend);
        $this->plotOutliers($index, $legend);
        $this->plotJitter($index, $legend);
        return $this;
    }

    /**
     * creates boxplots
     * @param   string  $filePath
     * @return  self
     * @thrown  \Exception
     */
    public function create(string $filePath)
    {
        if (strlen($filePath) === 0) {
            throw new \Exception("empty string specified for file path.");
        }
        if (!is_array($this->dataSet)) {
            throw new \Exception('Invalid type of property: Plotter::$data array expected.');
        }
        if (empty($this->dataSet)) {
            throw new \Exception("Empty data specified.");
        }
        $this->setProperties();
        $this->plotGrids();
        $this->plotGridValues();
        $this->plotAxis();
        $legend = 0;
        foreach ($this->dataSet as $data) {
            $index = 0;
            foreach ($data as $values) {
                $this->ft->setData($values);
                $this->parsed = $this->ft->parse();
                $this->plot($index, $legend);
                $index++;
            }
            $legend++;
        }
        $this->plotLabels();
        $this->plotLabelX();
        $this->plotLabelY();
        $this->plotCaption();
        $this->plotLegend();
        $this->canvas->save($filePath);
        return $this;
    }
}
