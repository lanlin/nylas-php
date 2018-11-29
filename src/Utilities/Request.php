<?php namespace Nylas\Utilities;

use GuzzleHttp\Client;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;
use Psr\Http\Message\ResponseInterface;

/**
 * ----------------------------------------------------------------------------------
 * Nylas RESTFul Request Tool
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/29
 */
class Request
{

    // ------------------------------------------------------------------------------

    /**
     * @var \GuzzleHttp\Client
     */
    private $guzzle;

    // ------------------------------------------------------------------------------

    /**
     * enable or disable debug mode
     *
     * @var bool|resource
     */
    private $debug = false;

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
            'base_uri' => trim($server ?? API::LIST['server'])
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
    public function setPath(string ...$path)
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
    public function setBody($body)
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
    public function setQuery(array $query)
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
    public function setFormParams(array $params)
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
    public function setFormFiles(array $files)
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
    public function setHeaderParams(array $headers)
    {
        $this->headerParams = ['headers' => $headers];

        return $this;
    }

    // ------------------------------------------------------------------------------

    /**
     * @param callable $func
     */
    public function setHeaderFunctions(callable $func)
    {
        $this->onHeadersFunc[] = $func;
    }

    // ------------------------------------------------------------------------------

    /**
     * get request
     *
     * @param string $api
     * @return mixed
     * @throws \Exception
     */
    public function get(string $api)
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();

        try
        {
            $response = $this->guzzle->get($apiPath, $options);
        }
        catch (\Exception $e)
        {
            throw new NylasException($this->getExceptionMsg($e));
        }

        return $this->parseResponse($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * put request
     *
     * @param string $api
     * @return mixed
     * @throws \Exception
     */
    public function put(string $api)
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();

        try
        {
            $response = $this->guzzle->put($apiPath, $options);
        }
        catch (\Exception $e)
        {
            throw new NylasException($this->getExceptionMsg($e));
        }

        return $this->parseResponse($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * post request
     *
     * @param string $api
     * @return mixed
     * @throws \Exception
     */
    public function post(string $api)
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();

        try
        {
            $response = $this->guzzle->post($apiPath, $options);
        }
        catch (\Exception $e)
        {
            throw new NylasException($this->getExceptionMsg($e));
        }

        return $this->parseResponse($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete request
     *
     * @param string $api
     * @return mixed
     * @throws \Exception
     */
    public function delete(string $api)
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();

        try
        {
            $response = $this->guzzle->delete($apiPath, $options);
        }
        catch (\Exception $e)
        {
            throw new NylasException($this->getExceptionMsg($e));
        }

        return $this->parseResponse($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * get request & return body stream without preloaded
     *
     * @param string $api
     * @return mixed
     * @throws \Exception
     */
    public function getStream(string $api)
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();
        $options = array_merge($options, ['stream' => true]);

        try
        {
            $response = $this->guzzle->get($apiPath, $options);
        }
        catch (\Exception $e)
        {
            throw new NylasException($this->getExceptionMsg($e));
        }

        return $this->parseResponse($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * get request & save body to some where
     *
     * @param string $api
     * @param string|resource|\Psr\Http\Message\StreamInterface $sink
     * @return array
     * @throws \Exception
     */
    public function getSink(string $api, $sink)
    {
        $rules = V::oneOf(
            V::resourceType(),
            V::stringType()->notEmpty(),
            V::instance('\Psr\Http\Message\StreamInterface')
        );

        V::doValidate($rules, $sink);

        $options = $this->concatOptions();
        $options = array_merge($options, ['sink' => $sink]);
        $apiPath = $this->concatApiPath($api);

        try
        {
            $response = $this->guzzle->get($apiPath, $options);
        }
        catch (\Exception $e)
        {
            throw new NylasException($this->getExceptionMsg($e));
        }

        $type   = $response->getHeader('Content-Type');
        $length = $response->getHeader('Content-Length');

        return [current($type) => current($length)];
    }

    // ------------------------------------------------------------------------------

    /**
     * concat api path for request
     *
     * @param string $api
     * @return string
     */
    private function concatApiPath(string $api)
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
    private function getExceptionMsg(\Exception $e)
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
     * @return mixed
     * @throws \Exception if the response body is not in JSON format
     */
    private function parseResponse(ResponseInterface $response)
    {
        $expc = 'application/json';
        $type = $response->getHeader('Content-Type');

        if (strpos(strtolower(current($type)), $expc) === false)
        {
            return $response->getBody();
        }

        $data = $response->getBody()->getContents();
        $data = json_decode($data, true);

        if (JSON_ERROR_NONE !== json_last_error())
        {
            $msg = 'Unable to parse response body into JSON: ';
            throw new NylasException($msg . json_last_error());
        }

        return $data ?? [];
    }

    // ------------------------------------------------------------------------------

    /**
     * check http status code before response body
     */
    private function onHeadersFuncions()
    {
        $request = $this;
        $excpArr = Errors::StatusExceptions;

        return function (ResponseInterface $response) use ($request, $excpArr)
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
                call_user_func($func, $response);
            }
        };
    }

    // ------------------------------------------------------------------------------

}
