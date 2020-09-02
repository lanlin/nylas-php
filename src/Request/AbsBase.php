<?php namespace Nylas\Request;

use GuzzleHttp\Client;
use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Errors;
use Psr\Http\Message\ResponseInterface;

/**
 * ----------------------------------------------------------------------------------
 * Nylas RESTFul Request Base
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/06/28
 */
trait AbsBase
{
    // ------------------------------------------------------------------------------

    /**
     * enable or disable debug mode
     *
     * @var bool|resource
     */
    private $debug = false;

    // ------------------------------------------------------------------------------

    /**
     * @var \GuzzleHttp\Client
     */
    private $guzzle;

    // ------------------------------------------------------------------------------

    private $formFiles     = [];
    private $pathParams    = [];
    private $jsonParams    = [];
    private $queryParams   = [];
    private $headerParams  = [];
    private $bodyContents  = [];
    private $onHeadersFunc = [];

    // ------------------------------------------------------------------------------

    /**
     * Request constructor.
     *
     * @param string|NULL $server
     * @param bool|resource $debug
     */
    public function __construct(string $server = null, $debug = false)
    {
        $option =
        [
            'verify'   => true,
            'base_uri' => trim($server ?? API::LIST['server']),
        ];

        $this->debug  = $debug;
        $this->guzzle = new Client($option);
    }

    // ------------------------------------------------------------------------------

    /**
     * set path params
     *
     * @param string[] $path
     * @return $this
     */
    public function setPath(string ...$path) : self
    {
        $this->pathParams = $path;

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * set body value
     *
     * @param string|resource|\Psr\Http\Message\StreamInterface $body
     * @return $this
     */
    public function setBody($body) : self
    {
        $this->bodyContents = ['body' => $body];

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * set query params
     *
     * @param array $query
     * @return $this
     */
    public function setQuery(array $query) : self
    {
        $this->queryParams = ['query' => $query];

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * set form params
     *
     * @param array $params
     * @return $this
     */
    public function setFormParams(array $params) : self
    {
        $this->jsonParams = ['json' => $params];

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * set form files
     *
     * @param array $files
     * @return $this
     */
    public function setFormFiles(array $files) : self
    {
        $this->formFiles = ['multipart' => Helper::arrayToMulti($files)];

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * set header params
     *
     * @see https://docs.nylas.com/docs/using-access-tokens
     *
     * @param array $headers
     *
     * @return $this
     */
    public function setHeaderParams(array $headers) : self
    {
        if (!empty($headers['Authorization']))
        {
            $encoded = \base64_encode("{$headers['Authorization']}:");

            $headers['Authorization'] = "Basic {$encoded}";
        }

        $this->headerParams = ['headers' => $headers];

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param callable $func
     */
    public function setHeaderFunctions(callable $func) : void
    {
        $this->onHeadersFunc[] = $func;
    }

    // ------------------------------------------------------------------------------

    /**
     * concat api path for request
     *
     * @param string $api
     * @return string
     */
    private function concatApiPath(string $api) : string
    {
        return sprintf($api, ...$this->pathParams);
    }

    // ------------------------------------------------------------------------------

    /**
     * concat response data when invalid json data responsed
     *
     * @param array  $type
     * @param string $code
     * @param string $data
     *
     * @return array
     */
    private function concatForInvalidJsonData(array $type, string $code, string $data): array
    {
        return
        [
            'httpStatus'  => $code,
            'invalidJson' => true,
            'contentType' => current($type),
            'contentBody' => $data,
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * concat options for request
     *
     * @param bool $httpErrors
     * @return array
     */
    private function concatOptions(bool $httpErrors = false) : array
    {
        $temp =
        [
            'debug'       => $this->debug,
            'on_headers'  => $this->onHeadersFuncions(),
            'http_errors' => $httpErrors
        ];

        return array_merge(
            $temp,
            empty($this->formFiles) ? [] : $this->formFiles,
            empty($this->jsonParams) ? [] : $this->jsonParams,
            empty($this->queryParams) ? [] : $this->queryParams,
            empty($this->headerParams) ? [] : $this->headerParams,
            empty($this->bodyContents) ? [] : $this->bodyContents
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * check http status code before response body
     */
    private function onHeadersFuncions() : callable
    {
        $request = $this;
        $excpArr = Errors::StatusExceptions;

        return static function (ResponseInterface $response) use ($request, $excpArr)
        {
            $statusCode = $response->getStatusCode();

            // check status code
            if ($statusCode >= 400)
            {
                // normal exception
                if (isset($excpArr[$statusCode]))
                {
                    throw new $excpArr[$statusCode];
                }

                // unexpected exception
                throw new $excpArr['default'];
            }

            // execute others on header functions
            foreach ($request->onHeadersFunc as $func)
            {
                $func($response);
            }
        };
    }

    // ------------------------------------------------------------------------------

    /**
     * Parse the JSON response body and return an array
     *
     * @param ResponseInterface $response
     * @param bool $headers  TIPS: true for get headers, false get body data
     * @return mixed
     * @throws \Exception if the response body is not in JSON format
     */
    private function parseResponse(ResponseInterface $response, bool $headers = false)
    {
        if ($headers) { return $response->getHeaders(); }

        $expc = 'application/json';
        $code = $response->getStatusCode();
        $type = $response->getHeader('Content-Type');

        // when not json type
        if (strpos(strtolower(current($type)), $expc) === false)
        {
            return $this->concatForInvalidJsonData($type, $code, $response->getBody());
        }

        // decode json data
        $data = $response->getBody()->getContents();
        $temp = json_decode(trim(utf8_encode($data)), true, 512);
        $errs = JSON_ERROR_NONE !== json_last_error();

        // not throw when decode error
        if ($errs)
        {
            return $this->concatForInvalidJsonData($type, $code, $data);
        }

        return $temp ?? [];
    }

    // ------------------------------------------------------------------------------
}
