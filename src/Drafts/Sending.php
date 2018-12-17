<?php namespace Nylas\Drafts;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Draft Sending
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/12/17
 */
class Sending
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Sending constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * send draft
     *
     * @param array $params
     * @param string $accessToken
     * @return array
     */
    public function sendDraft(array $params, string $accessToken = null)
    {
        $params      = Helper::arrayToMulti($params);
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        $rule = V::each(V::keySet(
            V::key('version', V::intType()->min(0)),
            V::key('draft_id', V::stringType()->notEmpty())
        ));

        V::doValidate($rule, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['sending'];
        $header = ['Authorization' => $accessToken];

        foreach ($params as $item)
        {
            $request = $this->options
            ->getAsync()
            ->setFormParams($item)
            ->setHeaderParams($header);

            $queues[] = function () use ($request, $target)
            {
                return $request->post($target);
            };
        }

        $dftId = Helper::generateArray($params, 'draft_id');
        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($dftId, $pools);
    }

    // ------------------------------------------------------------------------------

}
