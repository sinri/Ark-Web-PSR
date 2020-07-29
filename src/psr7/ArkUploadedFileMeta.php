<?php


namespace sinri\ark\web\psr\psr7;


use Exception;

class ArkUploadedFileMeta
{
    /**
     * @var string used in $_FILES
     */
    protected $uploadKey;
    /**
     * @var bool if as item of group in $_FILES
     */
    protected $asFileGroup;
    protected $name;
    protected $type;
    protected $size;
    protected $error;
    protected $tmpName;

    /**
     * @return ArkUploadedFileMeta[]
     * @throws Exception
     */
    public static function fetchAllUploadedFiles(){
        if(empty($_FILES))return [];
        $list=[];
        foreach ($_FILES as $key => $file){
            if(is_array($file['name'])){
                $subMetaList=self::fetchGroupUploadedFiles($key);
                foreach ($subMetaList as &$meta){
                    $meta->setUploadKey($key);
                    $meta->setAsFileGroup(true);
                }
            }else{
                $subMetaList=[
                    self::fetchSingleUploadedFile($key)
                    ->setUploadKey($key)
                    ->setAsFileGroup(false)
                ];
            }
            $list=array_merge($list,$subMetaList);
        }
        return $list;
    }

    /**
     * @param string $key
     * @return ArkUploadedFileMeta[]
     * @throws Exception
     */
    public static function fetchGroupUploadedFiles(string  $key){
        if(!isset($_FILES[$key])){
            throw new Exception("Cannot find upload file key");
        }
        $fileIndices=array_keys($_FILES[$key]['name']);
        $metaList=[];
        foreach ($fileIndices as $fileIndex){
            $meta=new ArkUploadedFileMeta();
            $meta->name=$_FILES[$key]['name'][$fileIndex];
            $meta->type=$_FILES[$key]['type'][$fileIndex];
            $meta->size=$_FILES[$key]['size'][$fileIndex];
            $meta->error=$_FILES[$key]['error'][$fileIndex];
            $meta->tmpName=$_FILES[$key]['tmp_name'][$fileIndex];
            $meta->uploadKey=$key;
            $meta->asFileGroup=true;
            $metaList[$fileIndex]=$meta;
        }
        return $metaList;
    }

    /**
     * @param string $key
     * @return ArkUploadedFileMeta
     * @throws Exception
     */
    public static function fetchSingleUploadedFile(string $key){
        if(!isset($_FILES[$key])){
            throw new Exception("Cannot find upload file key");
        }
        $meta=new ArkUploadedFileMeta();
        $meta->name=$_FILES[$key]['name'];
        $meta->type=$_FILES[$key]['type'];
        $meta->size=$_FILES[$key]['size'];
        $meta->error=$_FILES[$key]['error'];
        $meta->tmpName=$_FILES[$key]['tmp_name'];
        $meta->asFileGroup=false;
        $meta->uploadKey=$key;
        return $meta;
    }

    /**
     * @return bool
     */
    public function asFileGroup(): bool
    {
        return $this->asFileGroup;
    }

    /**
     * @param bool $asFileGroup
     * @return ArkUploadedFileMeta
     */
    public function setAsFileGroup(bool $asFileGroup): ArkUploadedFileMeta
    {
        $this->asFileGroup = $asFileGroup;
        return $this;
    }

    /**
     * @return string
     */
    public function getUploadKey(): string
    {
        return $this->uploadKey;
    }

    /**
     * @param string $uploadKey
     * @return ArkUploadedFileMeta
     */
    public function setUploadKey(string $uploadKey): ArkUploadedFileMeta
    {
        $this->uploadKey = $uploadKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return ArkUploadedFileMeta
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return ArkUploadedFileMeta
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     * @return ArkUploadedFileMeta
     */
    public function setSize($size)
    {
        $this->size = $size;
        return $this;
    }

    /**
     * @return int
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     * @return ArkUploadedFileMeta
     */
    public function setError($error)
    {
        $this->error = $error;
        return $this;
    }

    /**
     * @return string
     */
    public function getTmpName()
    {
        return $this->tmpName;
    }

    /**
     * @param mixed $tmpName
     * @return ArkUploadedFileMeta
     */
    public function setTmpName($tmpName)
    {
        $this->tmpName = $tmpName;
        return $this;
    }
}