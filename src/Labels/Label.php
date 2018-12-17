<?php namespace Nylas\Labels;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Labels
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/12/17
 */
class Label
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Label constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get labels list
     *
     * @param string $accessToken
     * @return array
     */
    public function getLabelsList(string $accessToken = null)
    {
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        $rule = V::stringType()->notEmpty();

        V::doValidate($rule, $accessToken);

        $header = ['Authorization' => $accessToken];

        return $this->options
        ->getSync()
        ->setHeaderParams($header)
        ->get(API::LIST['labels']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add label
     *
     * @param string $displayName
     * @param string $accessToken
     * @return array
     */
    public function addLabel(string $displayName, string $accessToken = null)
    {
        $params =
        [
            'display_name' => $displayName,
            'access_token' => $accessToken ?? $this->options->getAccessToken(),
        ];

        $rule = V::keySet(
            V::key('access_token', V::stringType()->notEmpty()),
            V::key('display_name', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options
        ->getSync()
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->post(API::LIST['labels']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update label
     *
     * @param array $params
     * @return array
     */
    public function updateLabel(array $params)
    {
        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        $rule = V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('access_token', V::stringType()->notEmpty()),
            V::key('display_name', V::stringType()->notEmpty())
        );

        V::doValidate($rule, $params);

        $path   = $params['id'];
        $header = ['Authorization' => $params['access_token']];

        unset($params['id'], $params['access_token']);

        return $this->options
        ->getSync()
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneLabel']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get label
     *
     * @param string|array $labelId
     * @param string $accessToken
     * @return array
     */
    public function getLabel($labelId, string $accessToken = null)
    {
        $labelId     = Helper::fooToArray($labelId);
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        $rule = V::each(V::stringType()->notEmpty(), V::intType());

        V::doValidate($rule, $labelId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneLabel'];
        $header = ['Authorization' => $accessToken];

        foreach ($labelId as $id)
        {
            $request = $this->options
            ->getAsync()
            ->setPath($id)
            ->setHeaderParams($header);

            $queues[] = function () use ($request, $target)
            {
                return $request->get($target);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($labelId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete label
     *
     * @param array $params
     * @param string $accessToken
     * @return array
     */
    public function deleteLabel(array $params, string $accessToken = null)
    {
        $params      = Helper::arrayToMulti($params);
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        $rule = V::each(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('display_name', V::stringType()->notEmpty())
        ));

        V::doValidate($rule, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneLabel'];
        $header = ['Authorization' => $accessToken];

        foreach ($params as $item)
        {
            $request = $this->options
            ->getAsync()
            ->setPath($item['id'])
            ->setFormParams(['display_name' => $item['display_name']])
            ->setHeaderParams($header);

            $queues[] = function () use ($request, $target)
            {
                return $request->delete($target);
            };
        }

        $labId = Helper::generateArray($params, 'id');
        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($labId, $pools);
    }

    // ------------------------------------------------------------------------------

}
