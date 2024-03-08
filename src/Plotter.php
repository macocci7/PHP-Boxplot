<?php

namespace Macocci7\PhpBoxplot;

use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Geometry\Factories\LineFactory;
use Intervention\Image\Typography\FontFactory;
use Intervention\Image\Geometry\Factories\RectangleFactory;
use Intervention\Image\Geometry\Factories\CircleFactory;
use Macocci7\PhpBoxplot\Analyzer;
use Macocci7\PhpBoxplot\Helpers\Config;

/**
 * class for analysis
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.ElseExpression)
 */
class Plotter extends Analyzer
{
    protected string $imageDriver;
    protected ImageManager $imageManager;
    protected ImageInterface $image;
    protected int $canvasWidth;
    protected int $canvasHeight;
    protected string|null $canvasBackgroundColor;
    protected float $frameXRatio;
    protected float $frameYRatio;
    protected string|null $axisColor;
    protected int $axisWidth;
    protected string|null $gridColor;
    protected int $gridWidth;
    protected int|null $gridHeightPitch;
    protected int|float $pixGridWidth;
    protected int $gridMax;
    protected int $gridMin;
    protected bool $gridVertical;
    protected int $boxCount;
    protected int $boxWidth;
    /**
     * @var string[]    $boxBackgroundColors
     */
    protected array $boxBackgroundColors;
    protected string|null $boxBorderColor;
    protected int $boxBorderWidth;
    protected int|float $pixHeightPitch;
    protected string|null $whiskerColor;
    protected int $whiskerWidth;
    protected string $fontPath;
    protected int|float $fontSize;
    protected string|null $fontColor;
    protected int $baseX;
    protected int $baseY;
    protected bool $outlier;
    protected int $outlierDiameter;
    protected string|null $outlierColor;
    protected bool $jitter;
    protected string|null $jitterColor;
    protected int $jitterDiameter;
    protected bool $mean;
    protected string|null $meanColor;
    /**
     * @var string[]    $labels
     */
    protected array $labels;
    protected string $labelX;
    protected string $labelY;
    protected string $caption;
    protected bool $legend;
    protected int $legendCount;
    protected string|null $legendBackgroundColor;
    protected int $legendWidth;
    protected int $legendFontSize;
    /**
     * @var string[]    $colors
     */
    protected array $colors;

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->loadConf();
        $this->imageManager = ImageManager::{$this->imageDriver}();
    }

    /**
     * loads config.
     * @return  void
     */
    private function loadConf()
    {
        Config::load();
        $props = [
            'imageDriver',
            'canvasWidth',
            'canvasHeight',
            'canvasBackgroundColor',
            'frameXRatio',
            'frameYRatio',
            'axisColor',
            'axisWidth',
            'gridColor',
            'gridWidth',
            'gridHeightPitch',
            'gridVertical',
            'boxWidth',
            'boxBackgroundColors',
            'boxBorderColor',
            'boxBorderWidth',
            'whiskerColor',
            'whiskerWidth',
            'fontPath',
            'fontSize',
            'fontColor',
            'outlier',
            'outlierDiameter',
            'outlierColor',
            'jitter',
            'jitterColor',
            'jitterDiameter',
            'mean',
            'meanColor',
            'labels',
            'labelX',
            'labelY',
            'caption',
            'legend',
            'legendBackgroundColor',
            'legends',
            'legendWidth',
            'legendFontSize',
            'colors',
        ];
        foreach ($props as $prop) {
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
        $this->baseX = (int) ($this->canvasWidth * (1 - $this->frameXRatio) * 3 / 4);
        $this->baseY = (int) ($this->canvasHeight * (1 + $this->frameYRatio) / 2);
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
        $this->pixHeightPitch = $this->canvasHeight * $this->frameYRatio / ($this->gridMax - $this->gridMin);
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
        $this->pixGridWidth = $this->canvasWidth * $this->frameXRatio / $this->boxCount;
        // Creating an instance of intervention/image.
        $this->image = $this->imageManager->create($this->canvasWidth, $this->canvasHeight);
        if ($this->isColorCode($this->canvasBackgroundColor)) {
            $this->image = $this->image->fill($this->canvasBackgroundColor);
        }
        // Note:
        // - If $this->labels has values, those values takes precedence.
        // - The values of $this->labels may be set by the function labels().
        if (empty($this->labels)) {
            $this->labels = array_keys($this->dataSet[array_keys($this->dataSet)[0]]);
        }
        return $this;
    }

    /**
     * plots axis
     * @return  self
     */
    public function plotAxis()
    {
        // Horizontal Axis
        $x1 = (int) $this->baseX;
        $y1 = (int) $this->baseY;
        $x2 = (int) ($this->canvasWidth * (3 + $this->frameXRatio) / 4);
        $y2 = (int) $this->baseY;
        $this->image->drawLine(
            function (LineFactory $line) use ($x1, $y1, $x2, $y2) {
                $line->from($x1, $y1);
                $line->to($x2, $y2);
                $line->color($this->axisColor);
                $line->width($this->axisWidth);
            }
        );
        // Vertical Axis
        $x1 = (int) $this->baseX;
        $y1 = (int) ($this->canvasHeight * (1 - $this->frameYRatio) / 2);
        $x2 = (int) $this->baseX;
        $y2 = (int) $this->baseY;
        $this->image->drawLine(
            function (LineFactory $line) use ($x1, $y1, $x2, $y2) {
                $line->from($x1, $y1);
                $line->to($x2, $y2);
                $line->color($this->axisColor);
                $line->width($this->axisWidth);
            }
        );
        return $this;
    }

    /**
     * plots grids
     * @return  self
     */
    public function plotGrids()
    {
        $this->plotGridHorizontal();
        $this->plotGridVertical();
        return $this;
    }

    /**
     * plots horizontal grids
     * @return  self
     */
    public function plotGridHorizontal()
    {
        for ($y = $this->gridMin; $y <= $this->gridMax; $y += $this->gridHeightPitch) {
            $x1 = (int) $this->baseX;
            $y1 = (int) ($this->baseY - ($y - $this->gridMin) * $this->pixHeightPitch);
            $x2 = (int) ($this->canvasWidth * (3 + $this->frameXRatio) / 4);
            $y2 = (int) $y1;
            $this->image->drawLine(
                function (LineFactory $line) use ($x1, $y1, $x2, $y2) {
                    $line->from($x1, $y1);
                    $line->to($x2, $y2);
                    $line->color($this->gridColor);
                    $line->width($this->gridWidth);
                }
            );
        }
        return $this;
    }

    /**
     * plots vertical grid
     * @return  self
     */
    public function plotGridVertical()
    {
        if (!$this->gridVertical) {
            return $this;
        }
        for ($i = 1; $i <= $this->boxCount; $i++) {
            $x1 = (int) ($this->baseX + $i * $this->pixGridWidth);
            $y1 = (int) ($this->canvasHeight * (1 - $this->frameYRatio) / 2);
            $x2 = (int) $x1;
            $y2 = (int) ($this->canvasHeight * (1 + $this->frameYRatio) / 2);
            $this->image->drawLine(
                function (LineFactory $line) use ($x1, $y1, $x2, $y2) {
                    $line->from($x1, $y1);
                    $line->to($x2, $y2);
                    $line->color($this->gridColor);
                    $line->width($this->gridWidth);
                }
            );
        }
        return $this;
    }

    /**
     * plots grid values
     * @return  self
     */
    public function plotGridValues()
    {
        for ($y = $this->gridMin; $y <= $this->gridMax; $y += $this->gridHeightPitch) {
            $x1 = (int) ($this->baseX - $this->fontSize * 1.1);
            $y1 = (int) ($this->baseY - ($y - $this->gridMin) * $this->pixHeightPitch + $this->fontSize * 0.4);
            $this->image->text(
                (string) $y,
                $x1,
                $y1,
                function (FontFactory $font) {
                    $font->filename($this->fontPath);
                    $font->size($this->fontSize);
                    $font->color($this->fontColor);
                    $font->align('center');
                    $font->valign('bottom');
                }
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
    public function plotBox(int $index, int $legend)
    {
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        $offsetX = ($index + ($legend + 0.5) / $legends) * $gridWidth - 0.5 * $this->boxWidth;
        $x1 = (int) ($this->baseX + $offsetX);
        $y1 = (int) ($this->baseY - ($this->parsed['ThirdQuartile'] - $this->gridMin) * $this->pixHeightPitch);
        $x2 = (int) ($x1 + $this->boxWidth);
        $y2 = (int) ($this->baseY - ($this->parsed['FirstQuartile'] - $this->gridMin) * $this->pixHeightPitch);
        $this->image->drawRectangle(
            $x1,
            $y1,
            function (RectangleFactory $rectangle) use ($legend, $x1, $y1, $x2, $y2) {
                $rectangle->size($x2 - $x1, $y2 - $y1);
                $rectangle->background($this->boxBackgroundColors[$legend]);
                $rectangle->border($this->boxBorderColor, $this->boxBorderWidth);
            }
        );
        return $this;
    }

    /**
     * plots meadian
     * @param   int $index
     * @param   int $legend
     * @return  self
     */
    public function plotMedian(int $index, int $legend)
    {
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        $offsetX = ($index + ($legend + 0.5) / $legends) * $gridWidth - 0.5 * $this->boxWidth;
        $x1 = (int) ($this->baseX + $offsetX);
        $y1 = (int) ($this->baseY - ($this->parsed['Median'] - $this->gridMin) * $this->pixHeightPitch);
        $x2 = (int) ($x1 + $this->boxWidth);
        $y2 = (int) $y1;
        $this->image->drawLine(
            function (LineFactory $line) use ($x1, $y1, $x2, $y2) {
                $line->from($x1, $y1);
                $line->to($x2, $y2);
                $line->color($this->boxBorderColor);
                $line->width($this->boxBorderWidth);
            }
        );
        return $this;
    }

    /**
     * plots mean
     * @param   int $index
     * @param   int $legend
     * @return  self
     */
    public function plotMean(int $index, int $legend)
    {
        if (!$this->mean) {
            return $this;
        }
        $mean = $this->getMean($this->parsed['data']);
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        $offsetX = ($index + ($legend + 0.5) / $legends) * $gridWidth;
        $x = (int) ($this->baseX + $offsetX);
        $y = (int) $this->baseY - ($mean - $this->gridMin) * $this->pixHeightPitch;
        $this->image->text(
            '+',
            $x,
            $y,
            function (FontFactory $font) {
                $font->filename($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->meanColor);
                $font->align('center');
                $font->valign('center');
            }
        );
        return $this;
    }

    /**
     * plot an upper whisker
     * @param   int $index
     * @param   int $legend
     * @return  self
     */
    public function plotWhiskerUpper(int $index, int $legend)
    {
        // upper whisker
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        $offsetX = ($index + ($legend + 0.5) / $legends) * $gridWidth;
        $x1 = (int) ($this->baseX + $offsetX);
        if ($this->outlier) {
            $max = $this->parsed['Max'];
            $ucl = $this->getUcl();
            $max = ($ucl > $max) ? $max : $ucl;
            $y1 = (int) ($this->baseY - ($max - $this->gridMin) * $this->pixHeightPitch);
        } else {
            $y1 = (int) ($this->baseY - ($this->parsed['Max'] - $this->gridMin) * $this->pixHeightPitch);
        }
        $x2 = (int) $x1;
        $y2 = (int) ($this->baseY - ($this->parsed['ThirdQuartile'] - $this->gridMin) * $this->pixHeightPitch);
        $this->image->drawLine(
            function (LineFactory $line) use ($x1, $y1, $x2, $y2) {
                $line->from($x1, $y1);
                $line->to($x2, $y2);
                $line->color($this->whiskerColor);
                $line->width($this->whiskerWidth);
            }
        );
        // top bar
        $x1 = (int) ($x1 - $this->boxWidth / 4);
        $x2 = (int) ($x1 + $this->boxWidth / 2);
        $y2 = (int) $y1;
        $this->image->drawLine(
            function (LineFactory $line) use ($x1, $y1, $x2, $y2) {
                $line->from($x1, $y1);
                $line->to($x2, $y2);
                $line->color($this->whiskerColor);
                $line->width($this->whiskerWidth);
            }
        );
        return $this;
    }

    /**
     * plots a lower whisker
     * @param   int $index
     * @param   int $legend
     * @return  self
     */
    public function plotWhiskerLower(int $index, int $legend)
    {
        // lower whisker
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        $offsetX = ($index + ($legend + 0.5) / $legends) * $gridWidth;
        $x1 = (int) ($this->baseX + $offsetX);
        $y1 = (int) ($this->baseY - ($this->parsed['FirstQuartile'] - $this->gridMin) * $this->pixHeightPitch);
        $x2 = (int) $x1;
        if ($this->outlier) {
            $min = $this->parsed['Min'];
            $lcl = $this->getLcl();
            $min = ($lcl < $min) ? $min : $lcl;
            $y2 = (int) ($this->baseY - ($min - $this->gridMin) * $this->pixHeightPitch);
        } else {
            $y2 = (int) ($this->baseY - ($this->parsed['Min'] - $this->gridMin) * $this->pixHeightPitch);
        }
        $this->image->drawLine(
            function (LineFactory $line) use ($x1, $y1, $x2, $y2) {
                $line->from($x1, $y1);
                $line->to($x2, $y2);
                $line->color($this->whiskerColor);
                $line->width($this->whiskerWidth);
            }
        );
        // bottom bar
        $x1 = (int) ($x1 - $this->boxWidth / 4);
        $y1 = (int) $y2;
        $x2 = (int) ($x1 + $this->boxWidth / 2);
        $this->image->drawLine(
            function (LineFactory $line) use ($x1, $y1, $x2, $y2) {
                $line->from($x1, $y1);
                $line->to($x2, $y2);
                $line->color($this->whiskerColor);
                $line->width($this->whiskerWidth);
            }
        );
        return $this;
    }

    /**
     * plots whiskers
     * @param   int $index
     * @param   int $legend
     * @return  self
     */
    public function plotWhisker(int $index, int $legend)
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
    public function plotOutliers(int $index, int $legend)
    {
        if (!$this->outlier) {
            return $this;
        }
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        foreach ($this->getOutliers() as $outlier) {
            $offsetX = ($index + ($legend + 0.5) / $legends) * $gridWidth;
            $x = (int) ($this->baseX + $offsetX);
            $y = (int) ($this->baseY - ($outlier - $this->gridMin) * $this->pixHeightPitch);
            $this->image->drawCircle(
                $x,
                $y,
                function (CircleFactory $circle) {
                    $circle->radius((int) ($this->outlierDiameter / 2));
                    $circle->background($this->outlierColor);
                    $circle->border($this->outlierColor, 1);
                }
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
    public function plotJitter(int $index, int $legend)
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
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        $baseX = $this->baseX + ($index + ($legend + 0.5) / $legends) * $gridWidth - $this->boxWidth / 2;
        $pitchX = $this->boxWidth / count($data);
        foreach ($data as $key => $value) {
            $x = (int) ($baseX + $key * $pitchX);
            $y = (int) ($this->baseY - ($value - $this->gridMin) * $this->pixHeightPitch);
            $this->image->drawCircle(
                $x,
                $y,
                function (CircleFactory $circle) {
                    $circle->radius((int) ($this->jitterDiameter / 2));
                    $circle->background($this->jitterColor);
                    $circle->border($this->jitterColor, 1);
                }
            );
        }
        return $this;
    }

    /**
     * plots labels
     * @return  self
     */
    public function plotLabels()
    {
        if (!is_array($this->labels)) {
            return $this;
        }
        foreach ($this->labels as $index => $label) {
            if (!is_string($label) && !is_numeric($label)) {
                continue;
            }
            $x = (int) ($this->baseX + ($index + 0.5) * $this->pixGridWidth);
            $y = (int) ($this->baseY + $this->fontSize * 1.2);
            $this->image->text(
                (string) $label,
                $x,
                $y,
                function (FontFactory $font) {
                    $font->filename($this->fontPath);
                    $font->size($this->fontSize);
                    $font->color($this->fontColor);
                    $font->align('center');
                    $font->valign('bottom');
                }
            );
        }
        return $this;
    }

    /**
     * plots label of X
     * @return  self
     */
    public function plotLabelX()
    {
        $x = (int) ($this->canvasWidth / 2);
        $y = (int) ($this->baseY + (1 - $this->frameYRatio) * $this->canvasHeight / 3);
        $this->image->text(
            (string) $this->labelX,
            $x,
            $y,
            function (FontFactory $font) {
                $font->filename($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            }
        );
        return $this;
    }

    /**
     * plots label of Y
     * @return  self
     */
    public function plotLabelY()
    {
        $width = $this->canvasHeight;
        $height = (int) ($this->canvasWidth * (1 - $this->frameXRatio) / 3);
        $image = $this->imageManager->create($width, $height);
        $x = $width / 2;
        $y = ($height + $this->fontSize) / 2;
        $image->text(
            (string) $this->labelY,
            $x,
            $y,
            function (FontFactory $font) {
                $font->filename($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            }
        );
        $image->rotate(90);
        $this->image->place($image, 'left');
        return $this;
    }

    /**
     * plots caption
     * @return  self
     */
    public function plotCaption()
    {
        $x = (int) ($this->canvasWidth / 2);
        $y = (int) ($this->canvasHeight * (1 - $this->frameYRatio) / 3);
        $this->image->text(
            (string) $this->caption,
            $x,
            $y,
            function (FontFactory $font) {
                $font->filename($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            }
        );
        return $this;
    }

    /**
     * plots a legend
     * @return  self
     */
    public function plotLegend()
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
        $this->image->drawRectangle(
            $x1,
            $y1,
            function (RectangleFactory $rectangle) use ($x1, $y1, $x2, $y2) {
                $rectangle->size($x2 - $x1, $y2 - $y1);
                $rectangle->background($this->legendBackgroundColor);
                $rectangle->border($this->boxBorderColor, $this->boxBorderWidth);
            }
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
            $this->image->drawRectangle(
                $x1,
                $y1,
                function (RectangleFactory $rectangle) use ($i, $x1, $y1, $x2, $y2) {
                    $rectangle->size($x2 - $x1, $y2 - $y1);
                    $rectangle->background($this->boxBackgroundColors[$i]);
                    $rectangle->border($this->boxBorderColor, $this->boxBorderWidth);
                }
            );
            $x = $x2 + 4;
            $y = $y1;
            $this->image->text(
                $label,
                $x,
                $y,
                function (FontFactory $font) {
                    $font->filename($this->fontPath);
                    $font->size($this->legendFontSize);
                    $font->color($this->fontColor);
                    $font->align('left');
                    $font->valign('top');
                }
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
    public function plot(int $index, int $legend)
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
        $this->image->save($filePath);
        return $this;
    }
}
