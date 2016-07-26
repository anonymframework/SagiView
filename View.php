<?php

/**
 * Created by PhpStorm.
 * User: sagi
 * Date: 26.07.2016
 * Time: 21:53
 */
class View
{

    /**
     * @var array
     */
    private $configs;

    /**
     * @var array
     */
    private $args;

    /**
     * @var string
     */
    private $file;

    /**
     * View constructor.
     * @param array $configs
     * @throws Exception
     */
    public function __construct($configs = [], $file = '')
    {
        if (isset($configs['view_path']) && isset($configs['dalvik_path'])) {
            $this->setConfigs($configs);
            $this->checkDirs();
        } else {
            throw new Exception('we need to view_path and dalvik_path for make a good start');
        }

        $this->file = $file;
    }

    /**
     * @param mixed $a
     * @param null $b
     * @return $this
     */
    public function with($a, $b = null)
    {
        if (is_null($b)) {
            $this->args = array_merge($this->args, $a);
        } else {
            $this->args[$a] = $b;
        }

        return $this;
    }

    public function render($file = null)
    {

        if (!is_null($file)) {
            $this->setFile($file);
        }

    }

    /**
     * checks dirs exists
     */
    private function checkDirs()
    {
        if (!is_dir($this->configs['view_path'])) {
            mkdir($this->configs['view_path'], 0777);
        }

        if (!is_dir($this->configs['dalvik_path'])) {
            mkdir($this->configs['dalvik_path'], 0777);

        }
    }


    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * @param array $configs
     * @return View
     */
    public function setConfigs($configs)
    {
        $this->configs = $configs;
        return $this;
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param string $file
     */
    public function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    /**
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * @param array $args
     * @return View
     */
    public function setArgs($args)
    {
        $this->args = $args;
        return $this;
    }


}