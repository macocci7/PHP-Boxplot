<?php

namespace Macocci7\PhpBoxplot\Traits;

trait AttributeTrait
{
    protected int $CANVAS_WIDTH_LIMIT_LOWER;
    protected int $CANVAS_HEIGHT_LIMIT_LOWER;
    protected int $canvasWidth;
    protected int $canvasHeight;
    /**
     * @var string[]    $labels
     */
    protected array $labels = [];
    protected string $labelX;
    protected string $labelY;
    protected string $caption;

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
     * sets labels
     * @param   array<int|string, int|string>   $labels
     * @return  self
     */
    public function labels(array $labels)
    {
        $this->labels = [];
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
}
