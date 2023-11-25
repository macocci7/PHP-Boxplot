<?php

namespace Macocci7\PhpBoxplot;

use Macocci7\PhpBoxplot\Plotter;

class Boxplot extends Plotter
{
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct();
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
    public function gridHeightPitch($pitch)
    {
        if (!is_int($pitch) && !is_float($pitch)) {
            return;
        }
        if ($pitch <= 0) {
            return;
        }
        $this->gridHeightPitch = $pitch;
        return $this;
    }

    /**
     * resizes width and height
     * @param   int $width
     * @param   int $height
     * @return  self
     */
    public function resize(int $width, int $height)
    {
        if ($width < 100 || $height < 100) {
            return;
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
            return;
        }
        $this->boxWidth = $width;
        return $this;
    }

    /**
     * sets background colors of boxes
     * @param   array   $colors
     * @return  self
     */
    public function boxBackground(array $colors)
    {
        if (!$this->isColorCodeAll($colors)) {
            throw new \Exception("only color codes are acceptable.");
        }
        $this->boxBackgroundColor = $colors;
        return $this;
    }

    /**
     * sets labels
     * @param   array   $labels
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
     * @param   array   $legends
     * @return  self
     */
    public function legends(array $legends)
    {
        $this->legends = $legends;
        return $this;
    }

    /**
     * sets vertical grids on
     * @param
     * @return  self
     */
    public function gridVerticalOn()
    {
        $this->gridVertical = true;
        return $this;
    }

    /**
     * sets vertical grids off
     * @param
     * @return  self
     */
    public function gridVerticalOff()
    {
        $this->gridVertical = false;
        return $this;
    }

    /**
     * sets detecting outliers on
     * @param
     * @return  self
     */
    public function outlierOn()
    {
        $this->outlier = true;
        return $this;
    }

    /**
     * sets detecting outliers off
     * @param
     * @return  self
     */
    public function outlierOff()
    {
        $this->outlier = false;
        return $this;
    }

    /**
     * sets jitter plotting on
     * @param
     * @return  self
     */
    public function jitterOn()
    {
        $this->jitter = true;
        return $this;
    }

    /**
     * sets jitter plotting off
     * @param
     * @return  self
     */
    public function jitterOff()
    {
        $this->jitter = false;
        return $this;
    }

    /**
     * sets plotting means on
     * @param
     * @return  self
     */
    public function meanOn()
    {
        $this->mean = true;
        return $this;
    }

    /**
     * sets plotting means off
     * @param
     * @return  self
     */
    public function meanOff()
    {
        $this->mean = false;
        return $this;
    }

    /**
     * sets showing legends on
     * @param
     * @return  self
     */
    public function legendOn()
    {
        $this->legend = true;
        return $this;
    }

    /**
     * sets showing legends off
     * @param
     * @return  self
     */
    public function legendOff()
    {
        $this->legend = false;
        return $this;
    }
}
