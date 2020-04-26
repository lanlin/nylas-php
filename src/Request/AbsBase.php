<?php namespace Nylas\Request;

use GuzzleHttp\Client;
use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Errors;
use Nylas\Exceptions\NylasException;
use Psr\Http\Message\ResponseInterface;

/**
 * ----------------------------------------------------------------------------------
 * Nylas RESTFul Request Base
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 */
trait AbsBase
{

    // ------------------------------------------------------------------------------

    /**
     * @var \GuzzleHttp\Client
     */
    private Client $guzzle;

    // ------------------------------------------------------------------------------

    /**
     * enable or disable debug mode
     *
     * @var bool|resource
     */
    private $debug = false;

    // ------------------------------------------------------------------------------

    /**
     * enable or disable json_decode exception throws
     *
     * @var bool
     */
    private bool $offDecodeError = false;

    // ------------------------------------------------------------------------------

    private array $formFiles     = [];
    private array $pathParams    = [];
    private array $jsonParams    = [];
    private array $queryParams   = [];
    private array $headerParams  = [];
    private array $bodyContents  = [];
    private array $onHeadersFunc = [];

    // ------------------------------------------------------------------------------

    /**
     * Request constructor.
     *
     * @param string|NULL $server
     * @param bool|resource $debug
     * @param bool $offDecodeError
     */
    public function __construct(string $server = null, $debug = false, bool $offDecodeError = false)
    {
        $option =
        [
            'base_uri' => trim($server ?? API::LIST['server'])
        ];

        $this->debug  = $debug;
        $this->guzzle = new Client($option);
        $this->offDecodeError = $offDecodeError;
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
     * @param array $headers
     * @return $this
     */
    public function setHeaderParams(array $headers) : self
    {
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
     * get exception message
     *
     * @param \Exception $e
     * @return string
     */
    private function getExceptionMsg(\Exception $e) : string
    {
        $preExcep = $e->getPrevious();

        $finalExc = ($preExcep instanceof NylasException) ? $preExcep : $e;

        return $finalExc->getMessage();
    }

    // ------------------------------------------------------------------------------

    /**
     * concat options for request
     *
     * @param bool $httpErrors
     * @return array
     */
    private function concatOptions(bool $httpErrors = false)
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
        $type = $response->getHeader('Content-Type');

        if (strpos(strtolower(current($type)), $expc) === false)
        {
            return $response->getBody();
        }

        $data = $response->getBody()->getContents();
        $temp = json_decode($data, true, 512, JSON_THROW_ON_ERROR);
        $errs = JSON_ERROR_NONE !== json_last_error();

        if ($errs && $this->offDecodeError)
        {
            return $data;
        }

        if ($errs)
        {
            $msg = 'Unable to parse response body into JSON: ';
            throw new NylasException($msg . json_last_error());
        }

        return $temp ?? [];
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

}
