<?php

class Compiler
{

    /**
     * @var array
     */

    private $rawTags = ["{!!", "!!}"];
    /**
     * @var array
     */
    private $contentTags = ["{{", "}}"];

    /**
     * @param $content
     * @return mixed
     */
    public function compile($content)
    {
        $parsed = explode("\n", $content);

        $newContent = '';
        foreach ($parsed as $line) {
            $lineContent = $this->compileTags($line);
            $newContent .= $lineContent . "\n";
        }

        return $newContent;
    }

    private function compileTags($content)
    {
        $contentPattern = "/" . $this->contentTags[0] . "(.*?)" . $this->contentTags[1] . "/s";
        $rawPattern = "/" . $this->rawTags[0] . "(.*?)" . $this->rawTags[1] . "/s";


        $content = $this->compileOpenedTags($content);
        $content = $this->compileContentEchos($content, $contentPattern);
        $content = $this->compileRawEchos($content, $rawPattern);

        return $content;
    }

    private function compileOpenedTags($content)
    {
        if (preg_match("/@(.*)/s", $content, $matches)) {
            $match = $matches[1];

            if (strstr($match, "end") or strstr($match, "break")) {
                $content = str_replace($matches[0], "<?php " . $match . "; ?>", $content);
            } else {
                $content = str_replace($matches[0], "<?php " . $match . ": ?>", $content);
            }

        }


        return $content;
    }

    /**
     * @param $content
     * @param $rawPattern
     */
    private function compileRawEchos($content, $rawPattern)
    {
        if (preg_match_all($rawPattern, $content, $matches)) {

            for ($i = 0; $i < count($matches[0]); $i++) {
                $match = $matches[0][$i];
                $content = str_replace($match, '<?php _e(' . $matches[1][$i] . ', false); ?>', $content);
            }
        }

        return $content;
    }

    /**
     * @param $content
     * @param $contentPattern
     * @return mixed
     */
    private function compileContentEchos($content, $contentPattern)
    {
        if (preg_match_all($contentPattern, $content, $matches)) {
            for ($i = 0; $i < count($matches[0]);
                 $i++) {
                $match = $matches[0][$i];
                $content = str_replace($match, '<?php _e(' . $matches[1][$i] . '); ?>', $content);
            }
        }

        return $content;
    }

    /**
     * @return mixed
     */
    public
    function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     * @return Compiler
     */
    public
    function setContent($content)
    {
        $this->content = $content;
        return $this;
    }


}