<?php

namespace Macocci7\PhpBoxplot;

use Macocci7\PhpFrequencyTable\FrequencyTable;

class Analyzer
{
    public $ft;
    public $data;
    public $parsed = [];
    public $limitUpper;
    public $limitLower;
    public $boxCount = 0;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->ft = new FrequencyTable();
    }

    /**
     * sets lower limit and upper limit
     * @param   int|float   $lower
     * @param   int|float   $upper
     * @return  self
     */
    public function limit($lower, $upper)
    {
        if (!is_int($lower) && !is_float($lower)) {
            throw new \Exception("lower limit must be a number.");
        }
        if (!is_int($upper) && !is_float($upper)) {
            throw new \Exception("upper limit must be a number.");
        }
        if ($lower >= $upper) {
            throw new \Exception("lower limit must be less than upper limit.");
        }
        $this->limitUpper = $upper;
        $this->limitLower = $lower;
        return $this;
    }

    /**
     * sets data
     * @param   array   $data
     * @return  self
     */
    public function setData(array $data)
    {
        foreach ($data as $key => $values) {
            $this->data[0][$key] = $values;
        }
        return $this;
    }

    /**
     * sets dataset
     * @param   array   $dataset
     * @return  self
     */
    public function setDataset(array $dataset)
    {
        $this->data = $dataset;
        return $this;
    }

    /**
     * judges wheter $color is in supported color code format or not
     * @param string $color
     * @return bool
     */
    public function isColorCode(string $color)
    {
        return preg_match('/^#[A-Fa-f0-9]{3}$|^#[A-Fa-f0-9]{6}$/', $color)
               ? true
               : false
               ;
    }

    /**
     * judges if all of params are colorcode or not
     * @param   array   $colors
     * @return  bool
     */
    public function isColorCodeAll(array $colors)
    {
        if (empty($colors)) {
            return false;
        }
        foreach ($colors as $color) {
            if (!$this->isColorCode($color)) {
                return false;
            }
        }
        return true;
    }

    /**
     * gets mean of $data
     * @param   array   $data
     * @return  float
     */
    public function getMean(array $data)
    {
        if (empty($data)) {
            return null;
        }
        foreach ($data as $value) {
            if (!is_int($value) && !is_float($value)) {
                return null;
            }
        }
        return array_sum($data) / count($data);
    }

    /**
     * gets UCL
     * @param
     * @return  float
     */
    public function getUcl()
    {
        if (!is_array($this->parsed)) {
            return;
        }
        if (!array_key_exists('ThirdQuartile', $this->parsed)) {
            return;
        }
        if (!array_key_exists('InterQuartileRange', $this->parsed)) {
            return;
        }
        return $this->parsed['ThirdQuartile'] + 1.5 * $this->parsed['InterQuartileRange'];
    }

    /**
     * gets LCL
     * @param
     * @return  float
     */
    public function getLcl()
    {
        if (!is_array($this->parsed)) {
            return;
        }
        if (!array_key_exists('FirstQuartile', $this->parsed)) {
            return;
        }
        if (!array_key_exists('InterQuartileRange', $this->parsed)) {
            return;
        }
        return $this->parsed['FirstQuartile'] - 1.5 * $this->parsed['InterQuartileRange'];
    }

    /**
     * gets outliers
     * @param
     * @return  array
     */
    public function getOutliers()
    {
        if (!is_array($this->parsed)) {
            return;
        }
        if (!array_key_exists('data', $this->parsed)) {
            return;
        }
        $ucl = $this->getUcl();
        $lcl = $this->getLcl();
        if (null === $ucl || null === $lcl) {
            return;
        }
        $outliers = [];
        foreach ($this->parsed['data'] as $value) {
            if ($value > $ucl || $value < $lcl) {
                $outliers[] = $value;
            }
        }
        return $outliers;
    }
}
