<?php


namespace sinri\ark\web\psr\psr7;


use Exception;
use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use sinri\ark\core\ArkHelper;

class ArkWebStreamOfResource implements StreamInterface
{

    /*
     * Resource modes.
     *
     * @var string
     *
     * @see http://php.net/manual/function.fopen.php
     * @see http://php.net/manual/en/function.gzopen.php
     *
     * READABLE: r,+
     *  r,a+,ab+,w+,wb+,x+,xb+,c+,cb+
     * WRITABLE: a,w,x,c,+
     *  a,w,r+,rb+,rw,x,c
     */

    const READABLE_MODES = '/r|a\+|ab\+|w\+|wb\+|x\+|xb\+|c\+|cb\+/';
    const WRITABLE_MODES = '/a|w|r\+|rb\+|rw|x|c/';

    /**
     * @var resource
     */
    protected $resource;
    /**
     * @var bool
     */
    protected $seekable;
    /**
     * @var bool
     */
    protected $readable;
    /**
     * @var bool
     */
    protected $writable;
    /**
     * @var int|null
     */
    protected $size;

    public function __construct($resource,$options=[])
    {
        $this->resource=$resource;

        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Initialized with a non resource');
        }

        if (isset($options['size'])) {
            $this->size = $options['size'];
        }

//        $this->customMetadata = isset($options['metadata'])
//            ? $options['metadata']
//            : [];

        $meta = stream_get_meta_data($this->resource);
        $this->seekable = $meta['seekable'];
        $this->readable = (bool)preg_match(self::READABLE_MODES, $meta['mode']);
        $this->writable = (bool)preg_match(self::WRITABLE_MODES, $meta['mode']);
        //$this->uri = $this->getMetadata('uri');
    }

    /**
     * Reads all data from the stream into a string, from the beginning to end.
     *
     * This method MUST attempt to seek to the beginning of the stream before
     * reading data and read the stream until the end is reached.
     *
     * Warning: This could attempt to load a large amount of data into memory.
     *
     * This method MUST NOT raise an exception in order to conform with PHP's
     * string casting operations.
     *
     * @see http://php.net/manual/en/language.oop5.magic.php#object.tostring
     * @return string
     */
    public function __toString()
    {
        try{
            $this->rewind();
            return $this->getContents();
        }catch (Exception $exception){
            return __CLASS__;
        }
    }

    /**
     * Closes the stream and any underlying resources.
     *
     * @return void
     */
    public function close()
    {
        if(isset($this->resource)) {
            fclose($this->resource);
            $this->detach();
        }
    }

    /**
     * Separates any underlying resources from the stream.
     *
     * After the stream has been detached, the stream is in an unusable state.
     *
     * @return resource|null Underlying PHP stream, if any
     */
    public function detach()
    {
        $this->resource=null;

        $this->readable=false;
        $this->writable=false;
        $this->seekable=false;

        return null;
    }

    /**
     * Get the size of the stream if known.
     *
     * @return int|null Returns the size in bytes if known, or null if unknown.
     */
    public function getSize()
    {
        if ($this->size !== null) {
            return $this->size;
        }

        if (!isset($this->stream)) {
            return null;
        }

        // Clear the stat cache if the stream has a URI
//        if ($this->uri) {
//            clearstatcache(true, $this->uri);
//        }

        $stats = fstat($this->stream);
        if (isset($stats['size'])) {
            $this->size = $stats['size'];
            return $this->size;
        }

        return null;
    }

    /**
     * Returns the current position of the file read/write pointer
     *
     * @return int Position of the file pointer
     * @throws RuntimeException on error.
     */
    public function tell()
    {
        $x = ftell($this->resource);
        if($x===false){
            throw new RuntimeException("Cannot Tell");
        }
        return $x;
    }

    /**
     * Returns true if the stream is at the end of the stream.
     *
     * @return bool
     */
    public function eof()
    {
        return feof($this->resource);
    }

    /**
     * Returns whether or not the stream is seekable.
     *
     * @return bool
     */
    public function isSeekable()
    {
        return $this->seekable;
    }

    /**
     * Seek to a position in the stream.
     *
     * @link http://www.php.net/manual/en/function.fseek.php
     * @param int $offset Stream offset
     * @param int $whence Specifies how the cursor position will be calculated
     *     based on the seek offset. Valid values are identical to the built-in
     *     PHP $whence values for `fseek()`.  SEEK_SET: Set position equal to
     *     offset bytes SEEK_CUR: Set position to current location plus offset
     *     SEEK_END: Set position to end-of-stream plus offset.
     * @throws RuntimeException on failure.
     */
    public function seek($offset, $whence = SEEK_SET)
    {
        $x=fseek($this->resource,$offset,$whence);
        if($x===-1){
            throw new RuntimeException("Cannot Seek");
        }
    }

    /**
     * Seek to the beginning of the stream.
     *
     * If the stream is not seekable, this method will raise an exception;
     * otherwise, it will perform a seek(0).
     *
     * @throws RuntimeException on failure.
     * @link http://www.php.net/manual/en/function.fseek.php
     * @see seek()
     */
    public function rewind()
    {
        $this->seek(0);
    }

    /**
     * Returns whether or not the stream is writable.
     *
     * @return bool
     */
    public function isWritable()
    {
        return $this->writable;
    }

    /**
     * Write data to the stream.
     *
     * @param string $string The string that is to be written.
     * @return int Returns the number of bytes written to the stream.
     * @throws RuntimeException on failure.
     */
    public function write($string)
    {
        if(!$this->writable){
            throw new RuntimeException("Cannot Write");
        }
        $x = fwrite($this->resource,$string);
        if($x===false){
            throw new RuntimeException("Write Failed");
        }
        return $x;
    }

    /**
     * Returns whether or not the stream is readable.
     *
     * @return bool
     */
    public function isReadable()
    {
        return $this->readable;
    }

    /**
     * Read data from the stream.
     *
     * @param int $length Read up to $length bytes from the object and return
     *     them. Fewer than $length bytes may be returned if underlying stream
     *     call returns fewer bytes.
     * @return string Returns the data read from the stream, or an empty string
     *     if no bytes are available.
     * @throws RuntimeException if an error occurs.
     */
    public function read($length)
    {
        if(!$this->resource){
            throw new RuntimeException("Cannot Read");
        }
        $x=fread($this->resource,$length);
        if($x===false){
            throw new RuntimeException("Read Failed");
        }
        return $x;
    }

    /**
     * Returns the remaining contents in a string
     *
     * @return string
     * @throws RuntimeException if unable to read or an error occurs while
     *     reading.
     */
    public function getContents()
    {
        $buffer='';
        while(!$this->eof()) {
            $buffer.=fread($this->resource, 1024);
        }
        return $buffer;
    }

    /**
     * Get stream metadata as an associative array or retrieve a specific key.
     *
     * The keys returned are identical to the keys returned from PHP's
     * stream_get_meta_data() function.
     *
     * @link http://php.net/manual/en/function.stream-get-meta-data.php
     * @param string $key Specific metadata to retrieve.
     * @return array|mixed|null Returns an associative array if no key is
     *     provided. Returns a specific key value if a key is provided and the
     *     value is found, or null if the key is not found.
     */
    public function getMetadata($key = null)
    {
        $x = stream_get_meta_data($this->resource);
        if($key===null){
            return $x;
        }
        return ArkHelper::readTarget($x,$key,null);
    }

    public function append(string $content){
        $this->write($content);
        return $this;
    }
}