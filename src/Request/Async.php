<?php

namespace Nylas\Request;

use Exception;
use Throwable;
use GuzzleHttp\Pool;
use Nylas\Utilities\Validator as V;
use Nylas\Exceptions\NylasException;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * ----------------------------------------------------------------------------------
 * Nylas RESTFul Request Async
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2021/09/22
 */
class Async
{
    // ------------------------------------------------------------------------------

    // base trait
    use AbsBase;

    // ------------------------------------------------------------------------------

    /**
     * get request async
     *
     * @param string $api
     *
     * @throws Exception
     *
     * @return PromiseInterface
     */
    public function get(string $api): PromiseInterface
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
     *
     * @throws Exception
     *
     * @return PromiseInterface
     */
    public function put(string $api): PromiseInterface
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
     *
     * @throws Exception
     *
     * @return PromiseInterface
     */
    public function post(string $api): PromiseInterface
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
     *
     * @throws Exception
     *
     * @return PromiseInterface
     */
    public function delete(string $api): PromiseInterface
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
     *
     * @throws Exception
     *
     * @return PromiseInterface
     */
    public function getStream(string $api): PromiseInterface
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();
        $options = \array_merge($options, ['stream' => true]);

        return $this->guzzle->getAsync($apiPath, $options);
    }

    // ------------------------------------------------------------------------------

    /**
     * get request & save body to some where
     *
     * @param string                                            $api
     * @param \Psr\Http\Message\StreamInterface|resource|string $sink
     *
     * @throws Exception
     *
     * @return PromiseInterface
     */
    public function getSink(string $api, mixed $sink): PromiseInterface
    {
        $rules = V::oneOf(
            V::resourceType(),
            V::stringType()->notEmpty(),
            V::instance(StreamInterface::class)
        );

        V::doValidate($rules, $sink);

        $options = $this->concatOptions();
        $options = \array_merge($options, ['sink' => $sink]);
        $apiPath = $this->concatApiPath($api);

        return $this->guzzle->getAsync($apiPath, $options);
    }

    // ------------------------------------------------------------------------------

    /**
     * pool for requests
     *
     * @param array $funcs
     * @param bool  $headers
     *
     * @return array
     */
    public function pool(array $funcs, bool $headers = false): array
    {
        foreach ($funcs as $func)
        {
            if (!\is_callable($func))
            {
                throw new NylasException(null, 'callable function required.');
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
     * @param Exception $exception
     *
     * @return array
     */
    private function whenFailed(Exception $exception): array
    {
        $finalExc = $this->checkIfHasNylasException($exception);

        return
        [
            'error'     => true,
            'code'      => $finalExc->getCode(),
            'message'   => $finalExc->getMessage(),
            'exception' => $finalExc,
        ];
    }

    // ------------------------------------------------------------------------------

    /**
     * parse data when success
     *
     * @param ResponseInterface $response
     * @param bool              $headers
     *
     * @return array
     */
    private function whenSuccess(ResponseInterface $response, bool $headers = false): ?array
    {
        try
        {
            return $this->parseResponse($response, $headers);
        }
        catch (Exception $e)
        {
            return
            [
                'error'     => true,
                'code'      => $e->getCode(),
                'message'   => $e->getMessage(),
                'exception' => $e,
            ];
        }
    }

    // ------------------------------------------------------------------------------

    /**
     * check if has nylas exception throwed
     *
     * @param Throwable $exception
     *
     * @return Throwable
     */
    private function checkIfHasNylasException(Throwable $exception): Throwable
    {
        if ($exception instanceof NylasException)
        {
            return $exception;
        }

        if ($exception->getPrevious())
        {
            return $this->checkIfHasNylasException($exception->getPrevious());
        }

        return $exception;
    }

    // ------------------------------------------------------------------------------
}
