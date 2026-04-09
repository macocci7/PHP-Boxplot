<?php

namespace Macocci7\PhpBoxplot\Traits;

trait VisibilityTrait
{
    protected bool $showAxis;
    protected bool $gridVertical;
    protected bool $outlier;
    protected bool $jitter;
    protected bool $mean;
    protected bool $legend;

    /**
     * sets vertical grids on
     */
    public function gridVerticalOn(): self
    {
        $this->gridVertical = true;
        return $this;
    }

    /**
     * sets vertical grids off
     */
    public function gridVerticalOff(): self
    {
        $this->gridVertical = false;
        return $this;
    }

    /**
     * sets detecting outliers on
     */
    public function outlierOn(): self
    {
        $this->outlier = true;
        return $this;
    }

    /**
     * sets detecting outliers off
     */
    public function outlierOff(): self
    {
        $this->outlier = false;
        return $this;
    }

    /**
     * sets jitter plotting on
     */
    public function jitterOn(): self
    {
        $this->jitter = true;
        return $this;
    }

    /**
     * sets jitter plotting off
     */
    public function jitterOff(): self
    {
        $this->jitter = false;
        return $this;
    }

    /**
     * sets plotting means on
     */
    public function meanOn(): self
    {
        $this->mean = true;
        return $this;
    }

    /**
     * sets plotting means off
     */
    public function meanOff(): self
    {
        $this->mean = false;
        return $this;
    }

    /**
     * sets showing legends on
     */
    public function legendOn(): self
    {
        $this->legend = true;
        return $this;
    }

    /**
     * sets showing legends off
     */
    public function legendOff(): self
    {
        $this->legend = false;
        return $this;
    }
}
