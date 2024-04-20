<?php

namespace Macocci7\PhpBoxplot\Traits;

trait StyleTrait
{
    protected string|null $canvasBackgroundColor;
    protected string|null $fontColor;
    protected string|null $axisColor;
    protected string|null $gridColor;
    protected string|null $legendBackgroundColor;
    protected string|null $boxBorderColor;
    protected int $boxBorderWidth;
    protected string|null $whiskerColor;
    protected int $whiskerWidth;
    protected int|null $gridHeightPitch;
    protected int $boxWidth;
    /**
     * @var string[]    $boxBackgroundColors
     */
    protected array $boxBackgroundColors;
    protected string $fontPath;
    protected int|float $fontSize;
    protected int $outlierDiameter;
    protected string|null $outlierColor;
    protected string|null $jitterColor;
    protected int $jitterDiameter;
    protected string|null $meanColor;
    protected int $legendWidth;
    protected int $legendFontSize;
    /**
     * @var string[]    $colors
     */
    protected array $colors;

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
}
