<?php


namespace sinri\ark\web\psr\psr7;


class ArkWebHeader
{
    /**
     * @var string
     */
    protected $headerName;
    /**
     * @var string[]
     */
    protected $headerValues;

    /**
     * @return string
     */
    public function getHeaderName(): string
    {
        return $this->headerName;
    }

    /**
     * @param string $headerName
     * @return ArkWebHeader
     */
    public function setHeaderName(string $headerName): ArkWebHeader
    {
        $this->headerName = $headerName;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getHeaderValues(): array
    {
        return $this->headerValues;
    }

    /**
     * @param string[] $headerValues
     * @return ArkWebHeader
     */
    public function setHeaderValues(array $headerValues): ArkWebHeader
    {
        $this->headerValues = $headerValues;
        return $this;
    }

    public function appendValue(string $value){
        $this->headerValues[]=$value;
    }

    public function getHeaderNameKey():string {
        return self::makeHeaderKey($this->getHeaderName());
    }

    public static function makeHeaderKey($name){
        return strtolower($name);
    }
}