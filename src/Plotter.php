<?php

namespace Macocci7\PhpBoxplot;

use Intervention\Image\ImageManagerStatic as Image;
use Macocci7\PhpBoxplot\Analyzer;

class Plotter extends Analyzer
{
    public $image;
    public $canvasWidth = 600;
    public $canvasHeight = 500;
    public $canvasBackgroundColor = '#ffffff';
    public $frameXRatio = 0.8;
    public $frameYRatio = 0.7;
    public $axisColor = '#666666';
    public $axisWidth = 1;
    public $gridColor = '#999999';
    public $gridWidth = 1;
    public $gridHeightPitch;
    public $pixGridWidth;
    public $gridMax;
    public $gridMin;
    public $gridVertical = false;
    public $boxWidth = 20;
    public $boxBackgroundColor;
    public $boxBorderColor = '#3333cc';
    public $boxBorderWidth = 1;
    public $pixHeightPitch;
    public $whiskerColor = '#3333cc';
    public $whiskerWidth = 1;
    public $fontPath = 'fonts/ipaexg.ttf'; // IPA ex Gothic 00401
    //public $fontPath = 'fonts/ipaexm.ttf'; // IPA ex Mincho 00401
    public $fontSize = 16;
    public $fontColor = '#333333';
    public $baseX;
    public $baseY;
    public $outlier = true;
    public $outlierDiameter = 2;
    public $outlierColor = '#ff0000';
    public $jitter = false;
    public $jitterColor = '#009900';
    public $jitterDiameter = 2;
    public $mean = false;
    public $meanColor = '#ff0000';
    public $labels;
    public $labelX;
    public $labelY;
    public $caption;
    public $legend = false;
    public $legendCount;
    public $legendBackgroundColor = '#ffffff';
    public $legends;
    public $legendWidth = 100;
    public $legendFontSize = 10;
    public $colors = [
        '#9999cc',
        '#cc9999',
        '#99cc99',
        '#99cccc',
        '#cc6666',
        '#ffcc99',
        '#cccc99',
        '#cc99cc',
    ];

    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
        Image::configure(['driver' => 'imagick']);
    }

    /**
     * sets properties
     * @param
     * @return  self
     */
    private function setProperties()
    {
        $this->legendCount = count($this->data);
        if (!$this->boxBackgroundColor) {
            $this->boxBackgroundColor = $this->colors;
        }
        $counts = [];
        foreach ($this->data as $values) {
            $counts[] = count($values);
        }
        $this->boxCount = max($counts);
        $this->baseX = (int) ($this->canvasWidth * (1 - $this->frameXRatio) * 3 / 4);
        $this->baseY = (int) ($this->canvasHeight * (1 + $this->frameYRatio) / 2);
        $maxValues = [];
        foreach ($this->data as $data) {
            foreach ($data as $key => $values) {
                $maxValues[] = max($values);
            }
        }
        $maxValue = max($maxValues);
        $minValues = [];
        foreach ($this->data as $data) {
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
        $this->image = Image::canvas($this->canvasWidth, $this->canvasHeight, $this->canvasBackgroundColor);
        // Note:
        // - If $this->labels has values, those values takes precedence.
        // - The values of $this->labels may be set by the function labels().
        if (empty($this->labels)) {
            $this->labels = array_keys($this->data[0]);
        }
        return $this;
    }

    /**
     * plots axis
     * @param
     * @return  self
     */
    public function plotAxis()
    {
        // Horizontal Axis
        $x1 = (int) $this->baseX;
        $y1 = (int) $this->baseY;
        $x2 = (int) $this->canvasWidth * (3 + $this->frameXRatio) / 4;
        $y2 = (int) $this->baseY;
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->color($this->axisColor);
                $draw->width($this->axisWidth);
            }
        );
        // Vertical Axis
        $x1 = (int) $this->baseX;
        $y1 = (int) $this->canvasHeight * (1 - $this->frameYRatio) / 2;
        $x2 = (int) $this->baseX;
        $y2 = (int) $this->baseY;
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->color($this->axisColor);
                $draw->width($this->axisWidth);
            }
        );
        return $this;
    }

    /**
     * plots grids
     * @param
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
     * @param
     * @return  self
     */
    public function plotGridHorizontal()
    {
        for ($y = $this->gridMin; $y <= $this->gridMax; $y += $this->gridHeightPitch) {
            $x1 = (int) $this->baseX;
            $y1 = (int) ($this->baseY - ($y - $this->gridMin) * $this->pixHeightPitch);
            $x2 = (int) ($this->canvasWidth * (3 + $this->frameXRatio) / 4);
            $y2 = (int) $y1;
            $this->image->line(
                $x1,
                $y1,
                $x2,
                $y2,
                function ($draw) {
                    $draw->color($this->gridColor);
                    $draw->width($this->gridWidth);
                }
            );
        }
        return $this;
    }

    /**
     * plots vertical grid
     * @param
     * @return  self
     */
    public function plotGridVertical()
    {
        if (!$this->gridVertical) {
            return;
        }
        for ($i = 1; $i <= $this->boxCount; $i++) {
            $x1 = (int) ($this->baseX + $i * $this->pixGridWidth);
            $y1 = (int) ($this->canvasHeight * (1 - $this->frameYRatio) / 2);
            $x2 = (int) $x1;
            $y2 = (int) ($this->canvasHeight * (1 + $this->frameYRatio) / 2);
            $this->image->line(
                $x1,
                $y1,
                $x2,
                $y2,
                function ($draw) {
                    $draw->color($this->gridColor);
                    $draw->width($this->gridWidth);
                }
            );
        }
        return $this;
    }

    /**
     * plots grid values
     * @param
     * @return  self
     */
    public function plotGridValues()
    {
        for ($y = $this->gridMin; $y <= $this->gridMax; $y += $this->gridHeightPitch) {
            $x1 = (int) ($this->baseX - $this->fontSize * 1.1);
            $y1 = (int) ($this->baseY - ($y - $this->gridMin) * $this->pixHeightPitch + $this->fontSize * 0.4);
            $this->image->text(
                $y,
                $x1,
                $y1,
                function ($font) {
                    $font->file($this->fontPath);
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
        $this->image->rectangle(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) use ($legend) {
                $draw->background($this->boxBackgroundColor[$legend]);
                $draw->border($this->boxBorderWidth, $this->boxBorderColor);
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
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->color($this->boxBorderColor);
                $draw->width($this->boxBorderWidth);
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
            return;
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
            function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->meanColor);
                $font->align('center');
                $font->valign('center');
            }
        );
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
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->color($this->whiskerColor);
                $draw->width($this->whiskerWidth);
            }
        );
        // top bar
        $x1 = (int) ($x1 - $this->boxWidth / 4);
        $x2 = (int) ($x1 + $this->boxWidth / 2);
        $y2 = (int) $y1;
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->color($this->whiskerColor);
                $draw->width($this->whiskerWidth);
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
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->color($this->whiskerColor);
                $draw->width($this->whiskerWidth);
            }
        );
        // bottom bar
        $x1 = (int) ($x1 - $this->boxWidth / 4);
        $y1 = (int) $y2;
        $x2 = (int) ($x1 + $this->boxWidth / 2);
        $this->image->line(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->color($this->whiskerColor);
                $draw->width($this->whiskerWidth);
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
            return;
        }
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        foreach ($this->getOutliers() as $outlier) {
            $offsetX = ($index + ($legend + 0.5) / $legends) * $gridWidth;
            $x = (int) ($this->baseX + $offsetX);
            $y = (int) ($this->baseY - ($outlier - $this->gridMin) * $this->pixHeightPitch);
            $this->image->circle(
                $this->outlierDiameter,
                $x,
                $y,
                function ($draw) {
                    $draw->background($this->outlierColor);
                    $draw->border(1, $this->outlierColor);
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
            return;
        }
        if (!array_key_exists('data', $this->parsed)) {
            return;
        }
        $data = $this->parsed['data'];
        if (empty($data)) {
            return;
        }
        $gridWidth = $this->pixGridWidth;
        $legends = $this->legendCount;
        $baseX = $this->baseX + ($index + ($legend + 0.5) / $legends) * $gridWidth - $this->boxWidth / 2;
        $pitchX = $this->boxWidth / count($data);
        foreach ($data as $key => $value) {
            $x = (int) ($baseX + $key * $pitchX);
            $y = (int) ($this->baseY - ($value - $this->gridMin) * $this->pixHeightPitch);
            $this->image->circle(
                $this->jitterDiameter,
                $x,
                $y,
                function ($draw) {
                    $draw->background($this->jitterColor);
                    $draw->border(1, $this->jitterColor);
                }
            );
        }
        return $this;
    }

    /**
     * plots labels
     * @param
     * @return  self
     */
    public function plotLabels()
    {
        if (!is_array($this->labels)) {
            return;
        }
        foreach ($this->labels as $index => $label) {
            if (!is_string($label) && !is_numeric($label)) {
                continue;
            }
            $x = $this->baseX + ($index + 0.5) * $this->pixGridWidth;
            $y = $this->baseY + $this->fontSize * 1.2;
            $this->image->text(
                (string) $label,
                $x,
                $y,
                function ($font) {
                    $font->file($this->fontPath);
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
     * @param
     * @return  self
     */
    public function plotLabelX()
    {
        $x = (int) $this->canvasWidth / 2;
        $y = $this->baseY + (1 - $this->frameYRatio) * $this->canvasHeight / 3 ;
        $this->image->text(
            (string) $this->labelX,
            $x,
            $y,
            function ($font) {
                $font->file($this->fontPath);
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
     * @param
     * @return  self
     */
    public function plotLabelY()
    {
        $width = $this->canvasHeight;
        $height = (int) ($this->canvasWidth * (1 - $this->frameXRatio) / 3);
        $image = Image::canvas($width, $height, $this->canvasBackgroundColor);
        $x = $width / 2;
        $y = ($height + $this->fontSize) / 2;
        $image->text(
            (string) $this->labelY,
            $x,
            $y,
            function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            }
        );
        $image->rotate(90);
        $this->image->insert($image, 'left');
        return $this;
    }

    /**
     * plots caption
     * @param
     * @return  self
     */
    public function plotCaption()
    {
        $x = $this->canvasWidth / 2;
        $y = $this->canvasHeight * (1 - $this->frameYRatio) / 3;
        $this->image->text(
            (string) $this->caption,
            $x,
            $y,
            function ($font) {
                $font->file($this->fontPath);
                $font->size($this->fontSize);
                $font->color($this->fontColor);
                $font->align('center');
                $font->valign('bottom');
            }
        );
    }

    /**
     * plots a legend
     * @param
     * @return  self
     */
    public function plotLegend()
    {
        if (!$this->legend) {
            return;
        }
        $baseX = $this->canvasWidth * (3 + $this->frameXRatio) / 4 - $this->legendWidth;
        $baseY = 10;
        $x1 = $baseX;
        $y1 = $baseY;
        $x2 = $x1 + $this->legendWidth;
        $y2 = $y1 + $this->legendFontSize * 1.2 * $this->legendCount + 8;
        $this->image->rectangle(
            $x1,
            $y1,
            $x2,
            $y2,
            function ($draw) {
                $draw->background($this->legendBackgroundColor);
                $draw->border($this->boxBorderWidth, $this->boxBorderColor);
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
            $this->image->rectangle(
                $x1,
                $y1,
                $x2,
                $y2,
                function ($draw) use ($i) {
                    $draw->background($this->boxBackgroundColor[$i]);
                    $draw->border($this->boxBorderWidth, $this->boxBorderColor);
                }
            );
            $x = $x2 + 4;
            $y = $y1;
            $this->image->text(
                $label,
                $x,
                $y,
                function ($font) {
                    $font->file($this->fontPath);
                    $font->size($this->legendFontSize);
                    $font->color($this->fontColor);
                    $font->align('left');
                    $font->valign('top');
                }
            );
        }
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
     */
    public function create(string $filePath)
    {
        if (!(strlen($filePath) > 0)) {
            return false;
        }
        $this->setProperties();
        if (!is_array($this->data)) {
            return false;
        }
        if (empty($this->data)) {
            return false;
        }
        $this->plotGrids();
        $this->plotGridValues();
        foreach ($this->data as $legend => $data) {
            $index = 0;
            foreach ($data as $key => $values) {
                $this->ft->setData($values);
                $this->parsed = $this->ft->parse($values);
                $this->plot($index, $legend);
                $index++;
            }
        }
        $this->plotAxis();
        $this->plotLabels();
        $this->plotLabelX();
        $this->plotLabelY();
        $this->plotCaption();
        $this->plotLegend();
        $this->image->save($filePath);
        return $this;
    }
}
