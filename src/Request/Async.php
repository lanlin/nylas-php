<?php namespace Nylas\Request;

use GuzzleHttp\Pool;
use Nylas\Utilities\Validate as V;
use Nylas\Exceptions\NylasException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * ----------------------------------------------------------------------------------
 * Nylas RESTFul Request Async
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/11/30
 */
class Async
{

    // ------------------------------------------------------------------------------

    /**
     * base trait
     */
    use AbsBase;

    // ------------------------------------------------------------------------------

    /**
     * get request async
     *
     * @param string $api
     * @return PromiseInterface
     * @throws \Exception
     */
    public function get(string $api)
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();

        return $this->guzzle->getAsync($apiPath, $options);
    }

    // ------------------------------------------------------------------------------

    /**
     * put request async
     *
     * @param string $api
     * @return PromiseInterface
     * @throws \Exception
     */
    public function put(string $api)
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();

        return $this->guzzle->putAsync($apiPath, $options);
    }

    // ------------------------------------------------------------------------------

    /**
     * post request async
     *
     * @param string $api
     * @return PromiseInterface
     * @throws \Exception
     */
    public function post(string $api)
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();

        return $this->guzzle->postAsync($apiPath, $options);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete request async
     *
     * @param string $api
     * @return PromiseInterface
     * @throws \Exception
     */
    public function delete(string $api)
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();

        return $this->guzzle->deleteAsync($apiPath, $options);
    }

    // ------------------------------------------------------------------------------

    /**
     * get request & return body stream without preloaded
     *
     * @param string $api
     * @return PromiseInterface
     * @throws \Exception
     */
    public function getStream(string $api)
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();
        $options = array_merge($options, ['stream' => true]);

        return $this->guzzle->getAsync($apiPath, $options);
    }

    // ------------------------------------------------------------------------------

    /**
     * get request & save body to some where
     *
     * @param string $api
     * @param string|resource|\Psr\Http\Message\StreamInterface $sink
     * @return PromiseInterface
     * @throws \Exception
     */
    public function getSink(string $api, $sink)
    {
        $rules = V::oneOf(
            V::resourceType(),
            V::stringType()->notEmpty(),
            V::instance(StreamInterface::class)
        );

        V::doValidate($rules, $sink);

        $options = $this->concatOptions();
        $options = array_merge($options, ['sink' => $sink]);
        $apiPath = $this->concatApiPath($api);

        return $this->guzzle->getAsync($apiPath, $options);
    }

    // ------------------------------------------------------------------------------

    /**
     * pool for requests
     *
     * @param array $funcs
     * @param bool $headers
     * @return mixed
     */
    public function pool(array $funcs, bool $headers = false)
    {
        foreach ($funcs as $func)
        {
            if (!is_callable($func))
            {
                throw new NylasException('callable function required.');
            }
        }

        $data = Pool::batch($this->guzzle, $funcs);

        foreach ($data as $key => $item)
        {
            $data[$key] =
            $item instanceof ResponseInterface ?
            $this->whenSuccess($item, $headers) : $this->whenFailed($item);
        }

        return $data;
    }

    // ------------------------------------------------------------------------------

    /**
     * parse data when failed
     *
     * @param \Exception $exception
     * @return array
     */
    private function whenFailed(\Exception $exception)
    {
        return
        [
            'error'     => true,
            'message'   => $this->getExceptionMsg($exception),
            'exception' => $exception
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * parse data when success
     *
     * @param ResponseInterface $response
     * @param bool $headers
     * @return array
     */
    private function whenSuccess(ResponseInterface $response, bool $headers = false)
    {
        try
        {
            return $this->parseResponse($response, $headers);
        }
        catch (\Exception $e)
        {
            return
            [
                'error'     => true,
                'message'   => $e->getMessage(),
                'exception' => $e
            ];
        }
    }

    // ------------------------------------------------------------------------------

}
