<?php

namespace Nylas\Request;

use Exception;
use Nylas\Utilities\Validator as V;
use Nylas\Exceptions\NylasException;
use Psr\Http\Message\StreamInterface;

/**
 * ----------------------------------------------------------------------------------
 * Nylas RESTFul Request Tool
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/06/22
 */
class Sync
{
    // ------------------------------------------------------------------------------

    // base trait
    use AbsBase;

    // ------------------------------------------------------------------------------

    /**
     * get request sync
     *
     * @param string $api
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function get(string $api)
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();

        try
        {
            $response = $this->guzzle->get($apiPath, $options);
        }
        catch (Exception $e)
        {
            throw new NylasException($e);
        }

        return $this->parseResponse($response, false);
    }

    // ------------------------------------------------------------------------------

    /**
     * put request sync
     *
     * @param string $api
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function put(string $api)
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();

        try
        {
            $response = $this->guzzle->put($apiPath, $options);
        }
        catch (Exception $e)
        {
            throw new NylasException($e);
        }

        return $this->parseResponse($response, false);
    }

    // ------------------------------------------------------------------------------

    /**
     * post request sync
     *
     * @param string $api
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function post(string $api)
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();

        try
        {
            $response = $this->guzzle->post($apiPath, $options);
        }
        catch (Exception $e)
        {
            throw new NylasException($e);
        }

        return $this->parseResponse($response, false);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete request sync
     *
     * @param string $api
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function delete(string $api)
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();

        try
        {
            $response = $this->guzzle->delete($apiPath, $options);
        }
        catch (Exception $e)
        {
            throw new NylasException($e);
        }

        return $this->parseResponse($response, false);
    }

    // ------------------------------------------------------------------------------

    /**
     * get request & return body stream without preloaded
     *
     * @param string $api
     *
     * @throws Exception
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function getStream(string $api): \Psr\Http\Message\ResponseInterface
    {
        $apiPath = $this->concatApiPath($api);
        $options = $this->concatOptions();
        $options = \array_merge($options, ['stream' => true]);

        try
        {
            $response = $this->guzzle->get($apiPath, $options);
        }
        catch (Exception $e)
        {
            throw new NylasException($e);
        }

        return $this->parseResponse($response, false);
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
     * @return array
     */
    public function getSink(string $api, $sink): array
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

        try
        {
            $response = $this->guzzle->get($apiPath, $options);
        }
        catch (Exception $e)
        {
            throw new NylasException($e);
        }

        return $this->parseResponse($response, true);
    }

    // ------------------------------------------------------------------------------
}
