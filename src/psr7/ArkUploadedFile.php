<?php


namespace sinri\ark\web\psr\psr7;


use InvalidArgumentException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use RuntimeException;
use sinri\ark\web\psr\psr17\ArkWebStreamFactory;
use const UPLOAD_ERR_OK;

class ArkUploadedFile implements UploadedFileInterface
{
    /**
     * @var string|null
     */
    protected $pathOfTargetFile = null;
    /**
     * @var StreamInterface
     */
    protected $streamOfUploadedTempFile;
    /**
     * @var ArkUploadedFileMeta
     */
    protected $uploadMeta;

    public function __construct($uploadMeta)
    {
        $this->uploadMeta = $uploadMeta;
    }

    public static function createUploadedFileWithMeta(ArkUploadedFileMeta $meta){
        $instance=new ArkUploadedFile($meta);
        $instance->streamOfUploadedTempFile=(new ArkWebStreamFactory())->createStreamFromFile($meta->getTmpName());
        return $instance;
    }

    /**
     * @param StreamInterface $stream
     * @param int|null $size
     * @param int $error
     * @param string|null $clientFilename
     * @param string|null $clientMediaType
     * @return UploadedFileInterface
     */
    public static function createUploadedFileWithStream(
        StreamInterface $stream,
        int $size = null,
        int $error = UPLOAD_ERR_OK,
        string $clientFilename = null,
        string $clientMediaType = null
    ): UploadedFileInterface
    {
        $meta=(new ArkUploadedFileMeta())
            ->setName($clientFilename)
            ->setError($error)
            ->setSize($size)
            ->setType($clientMediaType)
        ;

        if($stream instanceof ArkWebStreamOfFile){
            $meta->setTmpName($stream->getFilePath());
        }

        $instance=new ArkUploadedFile($meta);
        $instance->streamOfUploadedTempFile=$stream;
        return $instance;
    }

    /**
     * Retrieve a stream representing the uploaded file.
     *
     * This method MUST return a StreamInterface instance, representing the
     * uploaded file. The purpose of this method is to allow utilizing native PHP
     * stream functionality to manipulate the file upload, such as
     * stream_copy_to_stream() (though the result will need to be decorated in a
     * native PHP stream wrapper to work with such functions).
     *
     * If the moveTo() method has been called previously, this method MUST raise
     * an exception.
     *
     * @return StreamInterface Stream representation of the uploaded file.
     * @throws RuntimeException in cases when no stream is available or can be
     *     created.
     */
    public function getStream()
    {
        if(file_exists($this->pathOfTargetFile)){
            throw new RuntimeException("The uploaded temp file had been moved to target");
        }
        return $this->streamOfUploadedTempFile;
    }

    /**
     * Move the uploaded file to a new location.
     *
     * Use this method as an alternative to move_uploaded_file(). This method is
     * guaranteed to work in both SAPI and non-SAPI environments.
     * Implementations must determine which environment they are in, and use the
     * appropriate method (move_uploaded_file(), rename(), or a stream
     * operation) to perform the operation.
     *
     * $targetPath may be an absolute path, or a relative path. If it is a
     * relative path, resolution should be the same as used by PHP's rename()
     * function.
     *
     * The original file or stream MUST be removed on completion.
     *
     * If this method is called more than once, any subsequent calls MUST raise
     * an exception.
     *
     * When used in an SAPI environment where $_FILES is populated, when writing
     * files via moveTo(), is_uploaded_file() and move_uploaded_file() SHOULD be
     * used to ensure permissions and upload status are verified correctly.
     *
     * If you wish to move to a stream, use getStream(), as SAPI operations
     * cannot guarantee writing to stream destinations.
     *
     * @see http://php.net/is_uploaded_file
     * @see http://php.net/move_uploaded_file
     * @param string $targetPath Path to which to move the uploaded file.
     * @return ArkUploadedFile
     * @throws InvalidArgumentException if the $targetPath specified is invalid.
     * @throws RuntimeException on any error during the move operation, or on
     *     the second or subsequent call to the method.
     */
    public function moveTo($targetPath): ArkUploadedFile
    {
        $tmpFileName=$this->uploadMeta->getTmpName();
        if($tmpFileName!==null && is_uploaded_file($tmpFileName)) {
            $done = move_uploaded_file($tmpFileName, $targetPath);
            if(!$done){
                throw new RuntimeException('Cannot move the uploaded file from temp to target');
            }
        }elseif($this->streamOfUploadedTempFile instanceof StreamInterface){
            $this->streamOfUploadedTempFile->rewind();
            while($this->streamOfUploadedTempFile->isReadable()) {
                $buffer = $this->streamOfUploadedTempFile->read(1024);
                $written = file_put_contents($targetPath, $buffer, FILE_APPEND);
                if ($written === false) {
                    throw new RuntimeException('Cannot transfer the uploaded file from temp to target');
                }
            }
        }

        $this->pathOfTargetFile=$targetPath;
        return $this;
    }

    /**
     * Retrieve the file size.
     *
     * Implementations SHOULD return the value stored in the "size" key of
     * the file in the $_FILES array if available, as PHP calculates this based
     * on the actual size transmitted.
     *
     * @return int|null The file size in bytes or null if unknown.
     */
    public function getSize()
    {
        return $this->uploadMeta->getSize();
    }

    /**
     * Retrieve the error associated with the uploaded file.
     *
     * The return value MUST be one of PHP's UPLOAD_ERR_XXX constants.
     *
     * If the file was uploaded successfully, this method MUST return
     * UPLOAD_ERR_OK.
     *
     * Implementations SHOULD return the value stored in the "error" key of
     * the file in the $_FILES array.
     *
     * @see http://php.net/manual/en/features.file-upload.errors.php
     * @return int One of PHP's UPLOAD_ERR_XXX constants.
     */
    public function getError()
    {
        return $this->uploadMeta->getError();
    }

    /**
     * Retrieve the filename sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious filename with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "name" key of
     * the file in the $_FILES array.
     *
     * @return string|null The filename sent by the client or null if none
     *     was provided.
     */
    public function getClientFilename()
    {
        return $this->uploadMeta->getName();
    }

    /**
     * Retrieve the media type sent by the client.
     *
     * Do not trust the value returned by this method. A client could send
     * a malicious media type with the intention to corrupt or hack your
     * application.
     *
     * Implementations SHOULD return the value stored in the "type" key of
     * the file in the $_FILES array.
     *
     * @return string|null The media type sent by the client or null if none
     *     was provided.
     */
    public function getClientMediaType()
    {
        return $this->uploadMeta->getType();
    }
}