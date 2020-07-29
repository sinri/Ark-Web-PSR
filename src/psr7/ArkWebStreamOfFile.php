<?php


namespace sinri\ark\web\psr\psr7;


use RuntimeException;

class ArkWebStreamOfFile extends ArkWebStreamOfResource
{
    /**
     * @var string
     */
    protected $filePath;

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }

    /**
     * ArkWebStreamOfFile constructor.
     * @param string $filePath
     * @param string $mode
     * @throws RuntimeException
     */
    public function __construct($filePath,$mode='r'){
        $resource=fopen($filePath,$mode);
        if($resource===false){
            throw new RuntimeException("Cannot Create Resource");
        }
        parent::__construct($resource,[]);
        $this->filePath=$filePath;
    }

    public function append(string $content){
        $this->write($content);
        return $this;
    }
}