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
     * nylas api server
     *
     * @var string
     */
    private $server = 'https://api.nylas.com';

    // ------------------------------------------------------------------------------

    /**
     * Request constructor.
     *
     * @param string|NULL $server
     */
    public function __construct(string $server = null)
    {
        $option = ['base_uri' => $server ?? $this->server];

        $this->guzzle = new Client($option);
    }

    // ------------------------------------------------------------------------------

    /**
     * get request
     *
     * @param string $api
     * @param array  $params
     * @return mixed
     * @throws \Exception
     */
    public function get(string $api, array $params = [])
    {
        $query = ['query' => $params];

        $response = $this->guzzle->get($api, $query);

        $this->checkStatusCode($response->getStatusCode());

        return $this->parseResponse($response);
    }

    // ------------------------------------------------------------------------------

    public function put(string $api, array $params = [])
    {
        $response = $this->guzzle->put($api);

        $this->checkStatusCode($response->getStatusCode());

        return $this->parseResponse($response);
    }

    // ------------------------------------------------------------------------------

    /**
     * post request
     *
     * @param string $api
     * @param array  $params
     * @return mixed
     * @throws \Exception
     */
    public function post(string $api, array $params = [])
    {
        $options = ['form_params' => $params];

        $response = $this->guzzle->post($api, $options);

        $this->checkStatusCode($response->getStatusCode());

        return $this->parseResponse($response);
    }

    // ------------------------------------------------------------------------------

    public function delete(string $api, array $params = [])
    {
        $response = $this->guzzle->delete($api);

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
     * create request headers
     *
     * @return array
     */
    protected function createHeaders()
    {
        $token = 'Basic ' . base64_encode($this->apiToken . ':');

        $headers =
        [
            'debug'   => $this->apiDebug,
            'expect'  => false,
            'headers' =>
            [
                'Authorization'       => $token,
                'X-Nylas-API-Wrapper' => 'php'
            ]
        ];

        return $headers;
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
