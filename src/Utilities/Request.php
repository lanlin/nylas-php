<?php namespace Nylas\Utilities;

use GuzzleHttp\Client;
use Nylas\Exceptions\NylasException;
use Psr\Http\Message\ResponseInterface;

/**
 * ----------------------------------------------------------------------------------
 * Nylas RESTFul Request Tool
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/21
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
     * @var string
     */
    private $debug = false;

    // ------------------------------------------------------------------------------

    /**
     * nylas api server
     *
     * @var string
     */
    private $server = 'https://api.nylas.com';

    // ------------------------------------------------------------------------------

    private $formFiles    = [];
    private $pathParams   = [];
    private $formParams   = [];
    private $queryParams  = [];
    private $headerParams = [];

    // ------------------------------------------------------------------------------

    /**
     * Request constructor.
     *
     * @param string|NULL $server
     * @param bool $debug
     */
    public function __construct(string $server = null, bool $debug = false)
    {
        $option = ['base_uri' => $server ?? $this->server];

        $this->debug  = $debug;
        $this->guzzle = new Client($option);
    }

    // ------------------------------------------------------------------------------

    /**
     * set path params
     *
     * @param string ...$path
     * @return $this
     */
    public function setPath(string ...$path)
    {
        $this->pathParams = $path;

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
        $this->formParams = ['form_params' => $params];

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
     * get request
     *
     * @param string $api
     * @return mixed
     * @throws \Exception
     */
    public function get(string $api)
    {
        $apiPath  = $this->concatApiPath($api);
        $options  = $this->concatOptions();
        $response = $this->guzzle->get($apiPath, $options);

        $this->checkStatusCode($response->getStatusCode());

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
        $apiPath  = $this->concatApiPath($api);
        $options  = $this->concatOptions();
        $response = $this->guzzle->put($apiPath, $options);

        $this->checkStatusCode($response->getStatusCode());

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
        $apiPath  = $this->concatApiPath($api);
        $options  = $this->concatOptions();
        $response = $this->guzzle->post($apiPath, $options);

        $this->checkStatusCode($response->getStatusCode());

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
        $apiPath  = $this->concatApiPath($api);
        $options  = $this->concatOptions();
        $response = $this->guzzle->delete($apiPath, $options);

        $this->checkStatusCode($response->getStatusCode());

        return $this->parseResponse($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * check http status code
     *
     * @param int $statusCode
     */
    private function checkStatusCode(int $statusCode)
    {
        if ($statusCode === Errors::StatusOK) { return; }

        // normal exception
        if (isset(Errors::StatusExceptions[$statusCode]))
        {
            throw new (Errors::StatusExceptions[$statusCode]);
        }

        // unexpected exception
        throw new (Errors::StatusExceptions['default']);
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
        return sprintf($api, $this->pathParams);
    }

    // ------------------------------------------------------------------------------

    /**
     * concat options for request
     *
     * @return array
     */
    private function concatOptions()
    {
        $temp =
        [
            'debug'       => $this->debug,
            'http_errors' => false
        ];

        return array_merge(
            $temp,
            $this->formFiles,
            $this->formParams,
            $this->queryParams,
            $this->headerParams
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

        if (strpos(strtolower($type), $expc) === false)
        {
            throw new NylasException("Invalid content type responsed: {$type}");
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

}
