<?php


namespace sinri\ark\web\psr\psr17;


use InvalidArgumentException;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use RuntimeException;
use sinri\ark\web\psr\psr7\ArkWebStreamOfFile;
use sinri\ark\web\psr\psr7\ArkWebStreamOfResource;
use sinri\ark\web\psr\psr7\ArkWebStreamOfString;

class ArkWebStreamFactory implements StreamFactoryInterface
{

    /**
     * Create a new stream from a string.
     *
     * The stream SHOULD be created with a temporary resource.
     *
     * @param string $content String content with which to populate the stream.
     *
     * @return StreamInterface
     */
    public function createStream(string $content = ''): StreamInterface
    {
//        $stream = fopen('php://temp', 'r+');
//        if ($content !== '') {
//            fwrite($stream, $content);
//            fseek($stream, 0);
//        }
//        return new Stream($stream, $options);

        return new ArkWebStreamOfString($content);
    }

    /**
     * Create a stream from an existing file.
     *
     * The file MUST be opened using the given mode, which may be any mode
     * supported by the `fopen` function.
     *
     * The `$filename` MAY be any string supported by `fopen()`.
     *
     * @param string $filename Filename or stream URI to use as basis of stream.
     * @param string $mode Mode with which to open the underlying filename/stream.
     *
     * @return StreamInterface
     * @throws RuntimeException If the file cannot be opened.
     * @throws InvalidArgumentException If the mode is invalid.
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        return new ArkWebStreamOfFile($filename,$mode);
    }

    /**
     * Create a new stream from an existing resource.
     *
     * The stream MUST be readable and may be writable.
     *
     * @param resource $resource PHP resource to use as basis of stream.
     *
     * @return StreamInterface
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        return new ArkWebStreamOfResource($resource);
    }
}