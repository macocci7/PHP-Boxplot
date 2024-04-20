<?php

namespace Macocci7\PhpBoxplot;

use Macocci7\PhpBoxplot\Plotter;
use Macocci7\PhpBoxplot\Helpers\Config;
use Nette\Neon\Neon;

/**
 * class for managing boxplot
 * @author  macocci7 <macocci7@yahoo.co.jp>
 * @license MIT
 */
class Boxplot extends Plotter
{
    use Traits\JudgeTrait;

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
            if (strcmp('dataSet', $key) === 0 && empty($this->legends)) {
                $this->legends = array_keys($value);
            }
        }
        return $this;
    }
}
