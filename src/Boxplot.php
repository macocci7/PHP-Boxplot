<?php

namespace Macocci7\PhpBoxplot;

use Macocci7\PhpBoxplot\Plotter;
use Macocci7\PhpBoxplot\Helpers\Config;
use Macocci7\PhpBoxplot\Traits\JudgeTrait;
use Nette\Neon\Neon;

/**
 * class for managing boxplot
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Boxplot extends Plotter
{
    use JudgeTrait;

    private int $CANVAS_WIDTH_LIMIT_LOWER;
    private int $CANVAS_HEIGHT_LIMIT_LOWER;

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
        $props = [
            'CANVAS_WIDTH_LIMIT_LOWER',
            'CANVAS_HEIGHT_LIMIT_LOWER',
        ];
        foreach ($props as $prop) {
            $this->{$prop} = Config::get($prop);
        }
    }

    /**
     * set config from specified resource
     * @param   string|mixed[]  $configResource
     * @return  self
     */
    public function config(string|array $configResource)
    {
        foreach (Config::filter($configResource) as $key => $value) {
            $this->{$key} = $value;
        }
        return $this;
    }

    /**
     * sets canvas background color
     * @param   string  $bgcolor
     * @return  self
     */
    public function bgcolor(string $bgcolor)
    {
        if (!$this->isColorCode($bgcolor)) {
            throw new \Exception("specify rgb color code.");
        }
        $this->canvasBackgroundColor = $bgcolor;
        return $this;
    }

    /**
     * sets font color
     * @param   string  $color
     * @return  self
     */
    public function fontColor(string $color)
    {
        if (!$this->isColorCode($color)) {
            throw new \Exception("specify rgb color code.");
        }
        $this->fontColor = $color;
        return $this;
    }

    /**
     * sets axis color
     * @param   string  $color
     * @return  self
     */
    public function axisColor(string $color)
    {
        if (!$this->isColorCode($color)) {
            throw new \Exception("specify rgb color code.");
        }
        $this->axisColor = $color;
        return $this;
    }

    /**
     * sets grid color
     * @param   string  $color
     * @return  self
     */
    public function gridColor(string $color)
    {
        if (!$this->isColorCode($color)) {
            throw new \Exception("specify rgb color code.");
        }
        $this->gridColor = $color;
        return $this;
    }

    /**
     * sets legend background color
     * @param   string  $color
     * @return  self
     */
    public function legendBgcolor(string $color)
    {
        if (!$this->isColorCode($color)) {
            throw new \Exception("specify rgb color code.");
        }
        $this->legendBackgroundColor = $color;
        return $this;
    }

    /**
     * sets attributes of border of boxes
     * @param   int     $width
     * @param   string  $color
     * @return  self
     */
    public function boxBorder(int $width, string $color)
    {
        if ($width < 1) {
            throw new \Exception("specify natural number as width.");
        }
        if (!$this->isColorCode($color)) {
            throw new \Exception("specify rgb color code.");
        }
        $this->boxBorderWidth = $width;
        $this->boxBorderColor = $color;
        return $this;
    }

    /**
     * sets attributes of whisker
     * @param   int     $width
     * @param   string  $color
     * @return  self
     */
    public function whisker(int $width, string $color)
    {
        if ($width < 1) {
            throw new \Exception("specify natural number as width.");
        }
        if (!$this->isColorCode($color)) {
            throw new \Exception("specify rgb color code.");
        }
        $this->whiskerWidth = $width;
        $this->whiskerColor = $color;
        return $this;
    }

    /**
     * sets height pitch of grid
     * @param   int|float   $pitch
     * @return  self
     */
    public function gridHeightPitch(int|float $pitch)
    {
        if ($pitch <= 0) {
            throw new \Exception("specify positive number.");
        }
        $this->gridHeightPitch = $pitch;
        return $this;
    }

    /**
     * resizes width and height
     * @param   int $width
     * @param   int $height
     * @return  self
     * @thrown  \Exception
     */
    public function resize(int $width, int $height)
    {
        if ($width < $this->CANVAS_WIDTH_LIMIT_LOWER) {
            throw new \Exception(
                "width is below the lower limit "
                . $this->CANVAS_WIDTH_LIMIT_LOWER
            );
        }
        if ($height < $this->CANVAS_HEIGHT_LIMIT_LOWER) {
            throw new \Exception(
                "height is below the lower limit "
                . $this->CANVAS_HEIGHT_LIMIT_LOWER
            );
        }
        $this->canvasWidth = $width;
        $this->canvasHeight = $height;
        return $this;
    }

    /**
     * sets width of boxes
     * @param   int $width
     * @return  self
     */
    public function boxWidth(int $width)
    {
        if ($width < $this->boxBorderWidth * 2 + 1) {
            throw new \Exception("Box width must be greater than twice of box border width.");
        }
        $this->boxWidth = $width;
        return $this;
    }

    /**
     * sets background colors of boxes
     * @param   string[]    $colors
     * @return  self
     */
    public function boxBackground(array $colors)
    {
        if (!$this->isColorCodeAll($colors)) {
            throw new \Exception("only color codes are acceptable.");
        }
        $this->boxBackgroundColors = $colors;
        return $this;
    }

    /**
     * sets labels
     * @param   array<int|string, int|string>   $labels
     * @return  self
     */
    public function labels(array $labels)
    {
        $this->label = [];
        foreach ($labels as $label) {
            $this->labels[] = (string) $label;
        }
        return $this;
    }

    /**
     * sets the label of X
     * @param   string  $label
     * @return  self
     */
    public function labelX(string $label)
    {
        $this->labelX = $label;
        return $this;
    }

    /**
     * sets the label of Y
     * @param   string  $label
     * @return  self
     */
    public function labelY(string $label)
    {
        $this->labelY = $label;
        return $this;
    }

    /**
     * sets the caption
     * @param   string  $caption
     * @return  self
     */
    public function caption(string $caption)
    {
        $this->caption = $caption;
        return $this;
    }

    /**
     * sets legends
     * @param   string[]    $legends
     * @return  self
     */
    public function legends(array $legends)
    {
        $this->legends = $legends;
        return $this;
    }

    /**
     * sets vertical grids on
     * @return  self
     */
    public function gridVerticalOn()
    {
        $this->gridVertical = true;
        return $this;
    }

    /**
     * sets vertical grids off
     * @return  self
     */
    public function gridVerticalOff()
    {
        $this->gridVertical = false;
        return $this;
    }

    /**
     * sets detecting outliers on
     * @return  self
     */
    public function outlierOn()
    {
        $this->outlier = true;
        return $this;
    }

    /**
     * sets detecting outliers off
     * @return  self
     */
    public function outlierOff()
    {
        $this->outlier = false;
        return $this;
    }

    /**
     * sets jitter plotting on
     * @return  self
     */
    public function jitterOn()
    {
        $this->jitter = true;
        return $this;
    }

    /**
     * sets jitter plotting off
     * @return  self
     */
    public function jitterOff()
    {
        $this->jitter = false;
        return $this;
    }

    /**
     * sets plotting means on
     * @return  self
     */
    public function meanOn()
    {
        $this->mean = true;
        return $this;
    }

    /**
     * sets plotting means off
     * @return  self
     */
    public function meanOff()
    {
        $this->mean = false;
        return $this;
    }

    /**
     * sets showing legends on
     * @return  self
     */
    public function legendOn()
    {
        $this->legend = true;
        return $this;
    }

    /**
     * sets showing legends off
     * @return  self
     */
    public function legendOff()
    {
        $this->legend = false;
        return $this;
    }
}
