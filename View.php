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
     * @var Compiler
     */
    private $compiler;

    /**
     * @var
     */
    private $dalvikPath;

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

    /**
     * @param null $file
     */
    public function render($file = null)
    {
        if (!is_null($file)) {
            $this->setFile($file);
        }

        if ($content = $this->getFileContent()) {
            $replaceContent = $this->handleContent($content);


            $this->putContentOnDalvik($replaceContent);
            return $this;
        } else {
            throw new Exception($file . ' does not exists in your view_path');
        }
    }

    /**
     * @throws Exception
     */
    public function show()
    {
        $data = $this->getArgs();

        extract($data, EXTR_SKIP);

        if (!empty($this->dalvikPath)) {
            try {
                include $this->dalvikPath;
            } catch (Exception $e) {
                throw new Exception("Gösterme işlemi sırasında bir hata oluştu:, message:" . $e->getMessage());
            }
        }

    }

    /**
     * @param string $content
     */
    private function putContentOnDalvik($content)
    {
        $this->dalvikPath = $dalvikFile = $this->configs['dalvik_path'] . DIRECTORY_SEPARATOR . md5($this->getFile()) . ".php";


        if (!file_exists($dalvikFile)) {
            chmod($this->configs['dalvik_path'], 0777);
            touch($dalvikFile);
        }
        file_put_contents($dalvikFile, $content);

    }

    /**
     * @param $content
     * @return mixed
     */
    private function handleContent($content)
    {
        return $this->getCompiler()->compile($content);
    }

    /**
     * @return string
     */
    private function getFileContent()
    {
        if ($path = $this->findFile($this->getFile())) {
            return file_get_contents($path);
        } else {
            return false;
        }
    }

    /**
     * @param $file
     * @return string
     */
    private function findFile($file)
    {
        $fullpath = $this->configs['view_path'] . DIRECTORY_SEPARATOR . $file . ".blade.php";

        if (file_exists($fullpath)) {
            return $fullpath;
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
     * @return Compiler
     */
    public function getCompiler()
    {
        return $this->compiler;
    }

    /**
     * @param Compiler $compiler
     * @return View
     */
    public function setCompiler($compiler)
    {
        $this->compiler = $compiler;
        return $this;
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

function _e($content, $cleanHtml = true)
{
    if ($cleanHtml) {
        echo htmlspecialchars(htmlentities(strip_tags($content)));
    } else {
        echo $content;
    }
}