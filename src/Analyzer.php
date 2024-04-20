<?php

namespace Macocci7\PhpBoxplot;

use Macocci7\PhpFrequencyTable\FrequencyTable;
use Macocci7\PhpBoxplot\Helpers\Config;

/**
 * class for analysis
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Analyzer
{
    use Traits\JudgeTrait;

    public FrequencyTable $ft;
    /**
     * @var array<int|string, array<int|string, list<int|float>>>   $dataSet
     */
    protected array $dataSet;
    /**
     * @var array<string, mixed>    $parsed
     */
    public array $parsed;
    protected int|float $limitUpper;
    protected int|float $limitLower;
    /**
     * @var string[]    $legends
     */
    protected array $legends;

    /**
     * constructor
     */
    public function __construct()
    {
        $this->loadConf();
        $this->ft = new FrequencyTable();
    }

    /**
     * loads config.
     * @return  void
     */
    private function loadConf()
    {
        Config::load();
        $props = [
            'parsed',
        ];
        foreach ($props as $prop) {
            $this->{$prop} = Config::get($prop);
        }
    }

    /**
     * returns prop:
     * - of the specified key
     * - all configs if param is not specified
     * @param   string|null $key = null
     * @return  mixed
     */
    public function getProp(string|null $key = null)
    {
        if (is_null($key)) {
            $config = [];
            foreach (array_keys(Config::get('validConfig')) as $key) {
                $config[$key] = $this->{$key};
            }
            return $config;
        }
        if (isset($this->{$key})) {
            return $this->{$key};
        }
        return null;
    }

    /**
     * sets lower limit and upper limit
     * @param   int|float   $lower
     * @param   int|float   $upper
     * @return  self
     * @thrown  \Exception
     */
    public function limit(int|float $lower, int|float $upper)
    {
        if ($lower >= $upper) {
            throw new \Exception("lower limit must be less than upper limit.");
        }
        $this->limitUpper = $upper;
        $this->limitLower = $lower;
        return $this;
    }

    /**
     * sets data
     * @param   array<int|string, list<int|float>>   $data
     * @return  self
     * @thrown  \Exception
     */
    public function setData(array $data)
    {
        if (!$this->isValidData($data)) {
            throw new \Exception("Invalid data specified. array<int|string, list<int|float>> expected.");
        }
        $this->dataSet = [[]];
        foreach ($data as $key => $values) {
            $this->dataSet[0][$key] = $values;
        }
        $this->legends = [];
        return $this;
    }

    /**
     * sets dataset
     * @param   array<int|string, array<int|string, array<int|string, int|float>>>  $dataset
     * @return  self
     * @thrown  \Exception
     */
    public function setDataset(array $dataset)
    {
        if (!$this->isValidDataset($dataset)) {
            throw new \Exception(
                "Invalid dataset specified."
                . " array<int|string, array<int|string, int|float>> expected."
            );
        }
        $this->dataSet = $dataset;
        $this->legends = array_keys($dataset);
        return $this;
    }

    /**
     * gets mean of $data
     * @param   array<int|string, int|float>    $data
     * @return  float|null
     */
    public function getMean(array $data)
    {
        if (!$this->isNumbersAll($data)) {
            return null;
        }
        return array_sum($data) / count($data);
    }

    /**
     * gets UCL
     * @return  float|null
     */
    public function getUcl()
    {
        if (!is_array($this->parsed)) {
            return null;
        }
        if (!array_key_exists('ThirdQuartile', $this->parsed)) {
            return null;
        }
        if (!array_key_exists('InterQuartileRange', $this->parsed)) {
            return null;
        }
        return $this->parsed['ThirdQuartile'] + 1.5 * $this->parsed['InterQuartileRange'];
    }

    /**
     * gets LCL
     * @return  float|null
     */
    public function getLcl()
    {
        if (!is_array($this->parsed)) {
            return null;
        }
        if (!array_key_exists('FirstQuartile', $this->parsed)) {
            return null;
        }
        if (!array_key_exists('InterQuartileRange', $this->parsed)) {
            return null;
        }
        return $this->parsed['FirstQuartile'] - 1.5 * $this->parsed['InterQuartileRange'];
    }

    /**
     * gets outliers
     * @return  list<int|float>|null
     */
    public function getOutliers()
    {
        if (!is_array($this->parsed)) {
            return null;
        }
        if (!array_key_exists('data', $this->parsed)) {
            return null;
        }
        $ucl = $this->getUcl();
        $lcl = $this->getLcl();
        if (is_null($ucl) || is_null($lcl)) {
            return null;
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
