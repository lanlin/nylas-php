<?php namespace Nylas\Drafts;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Draft Sending
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/26
 */
class Sending
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

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
     * @return array
     */
    public function sendDraft(array $params) : array
    {
        $params      = Helper::arrayToMulti($params);
        $accessToken = $this->options->getAccessToken();

        $rule = V::simpleArray(V::keySet(
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

            $queues[] = static function () use ($request, $target)
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
