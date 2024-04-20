<?php

namespace Macocci7\PhpBoxplot\Traits;

trait VisibilityTrait
{
    protected bool $gridVertical;
    protected bool $outlier;
    protected bool $jitter;
    protected bool $mean;
    protected bool $legend;

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
