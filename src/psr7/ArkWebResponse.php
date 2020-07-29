<?php


namespace sinri\ark\web\psr\psr7;


use Exception;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use sinri\ark\core\ArkHelper;
use sinri\ark\web\psr\psr17\ArkWebStreamFactory;

/**
 * Class ArkWebResponse
 * @package sinri\ark\web\psr7
 *
 * implementation of `ResponseInterface`
 */
class ArkWebResponse extends ArkWebMessage implements ResponseInterface
{
    /** @var array Map of standard HTTP status code/reason phrases */
    protected static $phrases = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-status',
        208 => 'Already Reported',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Time-out',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Large',
        415 => 'Unsupported Media Type',
        416 => 'Requested range not satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Time-out',
        505 => 'HTTP Version not supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        511 => 'Network Authentication Required',
    ];
    /**
     * @var bool
     */
    protected $handleFinished=false;
    protected $statusCode;
    protected $statusReasonPhrase;

    public function __construct()
    {
        $this->body=(new ArkWebStreamFactory())->createStreamFromFile('php://temp','w+');
    }

    public static function makeResponse(int $statusCode,string $statusReasonPhrase=''){
        $response=new ArkWebResponse();
        $response->setStatus($statusCode,$statusReasonPhrase);
        return $response;
    }

    public function setStatus($code,$reasonPhrase=''){
        $this->statusCode=$code;
        if($reasonPhrase===''){
            $reasonPhrase=ArkHelper::readTarget(self::$phrases,[$code],'');
        }
        $this->statusReasonPhrase=$reasonPhrase;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHandleFinished(): bool
    {
        return $this->handleFinished;
    }

    /**
     * @param bool $handleFinished
     * @return ArkWebResponse
     */
    public function setHandleFinished(bool $handleFinished): ArkWebResponse
    {
        $this->handleFinished = $handleFinished;
        return $this;
    }

    /**
     * Gets the response status code.
     *
     * The status code is a 3-digit integer result code of the server's attempt
     * to understand and satisfy the request.
     *
     * @return int Status code.
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Return an instance with the specified status code and, optionally, reason phrase.
     *
     * If no reason phrase is specified, implementations MAY choose to default
     * to the RFC 7231 or IANA recommended reason phrase for the response's
     * status code.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated status and reason phrase.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @param int $code The 3-digit integer result code to set.
     * @param string $reasonPhrase The reason phrase to use with the
     *     provided status code; if none is provided, implementations MAY
     *     use the defaults as suggested in the HTTP specification.
     * @return static
     * @throws InvalidArgumentException For invalid status code arguments.
     */
    public function withStatus($code, $reasonPhrase = '')
    {
        $cloned=clone $this;
        return $cloned->setStatus($code,$reasonPhrase);
    }

    /**
     * Gets the response reason phrase associated with the status code.
     *
     * Because a reason phrase is not a required element in a response
     * status line, the reason phrase value MAY be null. Implementations MAY
     * choose to return the default RFC 7231 recommended reason phrase (or those
     * listed in the IANA HTTP Status Code Registry) for the response's
     * status code.
     *
     * @link http://tools.ietf.org/html/rfc7231#section-6
     * @link http://www.iana.org/assignments/http-status-codes/http-status-codes.xhtml
     * @return string Reason phrase; must return an empty string if none present.
     */
    public function getReasonPhrase()
    {
        return $this->statusReasonPhrase;
    }

    public static function respond(ResponseInterface $response){
        http_response_code($response->statusCode);
        foreach ($response->getHeaders() as $headerName=>$headerValues){
            header($headerName.": ".implode(", ",$headerValues));
        }
        echo $response->getBody()->__toString();
    }

    /**
     * @param string $content
     * @return $this
     */
    public function appendToBody($content){
        $this->getBody()->write($content);
        return $this;
    }

    /**
     * @param mixed $object
     * @param int $options
     * @param int $depth
     * @return $this
     * @throws Exception
     */
    public function writeAsJson($object,$options=0,$depth=512){
        $json=json_encode($object,$options,$depth);
        if($json===false){
            throw new Exception("Cannot create json: ".json_last_error()."(".json_last_error_msg().")");
        }
        $this->setHeader('Content-Type','application/json');
        $this->getBody()->write($json);
        return $this;
    }
}