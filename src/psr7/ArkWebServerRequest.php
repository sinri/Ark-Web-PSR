<?php


namespace sinri\ark\web\psr\psr7;


use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use sinri\ark\core\ArkHelper;

/**
 * Class ArkServerRequest
 * @package sinri\ark\web\psr7
 *
 * Implementation of `ServerRequestInterface`
 */
class ArkWebServerRequest extends ArkWebRequest implements ServerRequestInterface
{
    protected $serverParameters;
    protected $cookieParameters;
    protected $queryParameters;
    protected $uploadedFiles;
    protected $parsedBody;
    protected $attributes;

    /**
     * @param mixed $serverParameters
     * @return ArkWebServerRequest
     */
    public function setServerParameters($serverParameters)
    {
        $this->serverParameters = $serverParameters;
        return $this;
    }

    /**
     * Retrieve server parameters.
     *
     * Retrieves data related to the incoming request environment,
     * typically derived from PHP's $_SERVER superglobal. The data IS NOT
     * REQUIRED to originate from $_SERVER.
     *
     * @return array
     */
    public function getServerParams()
    {
        return $this->serverParameters;
    }

    /**
     * Retrieve cookies.
     *
     * Retrieves cookies sent by the client to the server.
     *
     * The data MUST be compatible with the structure of the $_COOKIE
     * superglobal.
     *
     * @return array
     */
    public function getCookieParams()
    {
        return $this->cookieParameters;
    }

    /**
     * Return an instance with the specified cookies.
     *
     * The data IS NOT REQUIRED to come from the $_COOKIE superglobal, but MUST
     * be compatible with the structure of $_COOKIE. Typically, this data will
     * be injected at instantiation.
     *
     * This method MUST NOT update the related Cookie header of the request
     * instance, nor related values in the server params.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated cookie values.
     *
     * @param array $cookies Array of key/value pairs representing cookies.
     * @return static
     */
    public function withCookieParams(array $cookies)
    {
        $cloned = clone $this;
        return $cloned->setCookieParams($cookies);
    }

    public function setCookieParams(array $cookies)
    {
        $this->cookieParameters = $cookies;
        return $this;
    }

    /**
     * Retrieve query string arguments.
     *
     * Retrieves the deserialized query string arguments, if any.
     *
     * Note: the query params might not be in sync with the URI or server
     * params. If you need to ensure you are only getting the original
     * values, you may need to parse the query string from `getUri()->getQuery()`
     * or from the `QUERY_STRING` server param.
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParameters;
    }

    /**
     * Return an instance with the specified query string arguments.
     *
     * These values SHOULD remain immutable over the course of the incoming
     * request. They MAY be injected during instantiation, such as from PHP's
     * $_GET superglobal, or MAY be derived from some other value such as the
     * URI. In cases where the arguments are parsed from the URI, the data
     * MUST be compatible with what PHP's parse_str() would return for
     * purposes of how duplicate query parameters are handled, and how nested
     * sets are handled.
     *
     * Setting query string arguments MUST NOT change the URI stored by the
     * request, nor the values in the server params.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated query string arguments.
     *
     * @param array $query Array of query string arguments, typically from
     *     $_GET.
     * @return static
     */
    public function withQueryParams(array $query)
    {
        $cloned = clone $this;
        return $cloned->setQueryParams($query);
    }

    public function setQueryParams(array $query)
    {
        $this->queryParameters = $query;
        return $this;
    }

    /**
     * Retrieve normalized file upload data.
     *
     * This method returns upload metadata in a normalized tree, with each leaf
     * an instance of Psr\Http\Message\UploadedFileInterface.
     *
     * These values MAY be prepared from $_FILES or the message body during
     * instantiation, or MAY be injected via withUploadedFiles().
     *
     * @return array An array tree of UploadedFileInterface instances; an empty
     *     array MUST be returned if no data is present.
     */
    public function getUploadedFiles()
    {
        return $this->uploadedFiles;
    }

    public function setUploadedFiles(array $uploadedFiles)
    {
        $this->uploadedFiles = $uploadedFiles;
        return $this;
    }

    /**
     * Create a new instance with the specified uploaded files.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param array $uploadedFiles An array tree of UploadedFileInterface instances.
     * @return static
     * @throws InvalidArgumentException if an invalid structure is provided.
     */
    public function withUploadedFiles(array $uploadedFiles)
    {
        $cloned = clone $this;
        return $cloned->setUploadedFiles($uploadedFiles);
    }

    /**
     * Retrieve any parameters provided in the request body.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, this method MUST
     * return the contents of $_POST.
     *
     * Otherwise, this method may return any results of deserializing
     * the request body content; as parsing returns structured content, the
     * potential types MUST be arrays or objects only. A null value indicates
     * the absence of body content.
     *
     * @return null|array|object The deserialized body parameters, if any.
     *     These will typically be an array or object.
     */
    public function getParsedBody()
    {
        return $this->parsedBody;
    }

    public function setParsedBody($data)
    {
        $this->parsedBody = $data;
        return $this;
    }

    /**
     * Return an instance with the specified body parameters.
     *
     * These MAY be injected during instantiation.
     *
     * If the request Content-Type is either application/x-www-form-urlencoded
     * or multipart/form-data, and the request method is POST, use this method
     * ONLY to inject the contents of $_POST.
     *
     * The data IS NOT REQUIRED to come from $_POST, but MUST be the results of
     * deserializing the request body content. Deserialization/parsing returns
     * structured data, and, as such, this method ONLY accepts arrays or objects,
     * or a null value if nothing was available to parse.
     *
     * As an example, if content negotiation determines that the request data
     * is a JSON payload, this method could be used to create a request
     * instance with the deserialized parameters.
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated body parameters.
     *
     * @param null|array|object $data The deserialized body data. This will
     *     typically be in an array or object.
     * @return static
     * @throws InvalidArgumentException if an unsupported argument type is
     *     provided.
     */
    public function withParsedBody($data)
    {
        $cloned = clone $this;
        return $cloned->setParsedBody($data);
    }

    /**
     * Retrieve attributes derived from the request.
     *
     * The request "attributes" may be used to allow injection of any
     * parameters derived from the request: e.g., the results of path
     * match operations; the results of decrypting cookies; the results of
     * deserializing non-form-encoded message bodies; etc. Attributes
     * will be application and request specific, and CAN be mutable.
     *
     * @return array Attributes derived from the request.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Retrieve a single derived request attribute.
     *
     * Retrieves a single derived request attribute as described in
     * getAttributes(). If the attribute has not been previously set, returns
     * the default value as provided.
     *
     * This method obviates the need for a hasAttribute() method, as it allows
     * specifying a default value to return if the attribute is not found.
     *
     * @param string $name The attribute name.
     * @param mixed $default Default value to return if the attribute does not exist.
     * @return mixed
     * @see getAttributes()
     */
    public function getAttribute($name, $default = null)
    {
        return (isset($this->attributes[$name])?$this->attributes[$name]:$default);
    }

    /**
     * Return an instance with the specified derived request attribute.
     *
     * This method allows setting a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that has the
     * updated attribute.
     *
     * @param string $name The attribute name.
     * @param mixed $value The value of the attribute.
     * @return static
     * @see getAttributes()
     */
    public function withAttribute($name, $value)
    {
        $cloned=clone $this;
        return $cloned->setAttribute($name,$value);
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name]=$value;
        return $this;
    }

    /**
     * Return an instance that removes the specified derived request attribute.
     *
     * This method allows removing a single derived request attribute as
     * described in getAttributes().
     *
     * This method MUST be implemented in such a way as to retain the
     * immutability of the message, and MUST return an instance that removes
     * the attribute.
     *
     * @param string $name The attribute name.
     * @return static
     * @see getAttributes()
     */
    public function withoutAttribute($name)
    {
        $cloned=clone $this;
        return $cloned->removeAttribute($name);
    }

    protected function removeAttribute($name)
    {
        if(isset($this->attributes[$name])){
            unset($this->attributes[$name]);
        }
        return $this;
    }

    public static function autoCreateServerRequest(){
        // method
        if(ArkHelper::isCLI()) {
            $method='cli';
        }else{
            $method=ArkHelper::readTarget($_SERVER,['REQUEST_METHOD']);
        }

        // uri
        $schema=ArkHelper::readTarget($_SERVER,['REQUEST_SCHEME'],'http');
        $requestUri=ArkHelper::readTarget($_SERVER,['REQUEST_URI'],'');
        $username=ArkHelper::readTarget($_SERVER,['PHP_AUTH_USER']);
        $password=ArkHelper::readTarget($_SERVER,['PHP_AUTH_PW']);
        $auth=
            $username===null
                ?''
                :(
                    $username
                    .(
                        $password===null
                            ?''
                            :(':'.$password)
                    )
                    .'@'
                )
        ;
        $uri=ArkWebUri::fromUriString($schema.'://'.$auth.$requestUri);

        // header (ArkWebHeader[])
        $headers=[];
        $originalHeaders=getallheaders();
        foreach ($originalHeaders as $originalHeaderName => $originalHeaderValue){
            $headers[]=(new ArkWebHeader())->setHeaderName($originalHeaderName)->setHeaderValues([$originalHeaderValue]);
        }

        // protocol version
        $protocolWord=ArkHelper::readTarget($_SERVER,['SERVER_PROTOCOL'],'HTTP/1.1');
        $protocolVersion=ArkHelper::readTarget(explode('/',$protocolWord),[1],'1.1');

        // ArkWebServerRequest
        $instance=new ArkWebServerRequest($method,$uri,$headers,null,$protocolVersion);

        // injection of common
        $instance->setServerParameters($_SERVER);
        $instance->setCookieParams($_COOKIE);
        $instance->setQueryParams($_GET);
        if(preg_match('#application/json#',$instance->getHeaderLine('content-type'))){
            $instance->setParsedBody(json_decode($instance->getBody()->__toString(),true));
        }else{
            $instance->setParsedBody($_POST);
        }

        // injection of uploading file
        $uploadFiles=[];
        $uploadedFileMetaList=ArkUploadedFileMeta::fetchAllUploadedFiles();
        foreach ($uploadedFileMetaList as $meta){
            $uploadFiles[]=ArkUploadedFile::createUploadedFileWithMeta($meta);
        }
        $instance->setUploadedFiles($uploadFiles);

        return $instance;
    }
}