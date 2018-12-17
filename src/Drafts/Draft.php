<?php namespace Nylas\Drafts;

use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Drafts
 * ----------------------------------------------------------------------------------
 *
 * @info include inline image <img src="cid:file_id">
 * @author lanlin
 * @change 2018/12/17
 */
class Draft
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

    // ------------------------------------------------------------------------------

    /**
     * Draft constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * get drafts list
     *
     * @param string $anyEmail
     * @param string $accessToken
     * @return array
     */
    public function getDraftsList(string $anyEmail = null, string $accessToken = null)
    {
        $params = ['access_token' => $accessToken ?? $this->options->getAccessToken()];

        !empty($anyEmail) AND $params['any_email'] = $anyEmail;

        $rule = V::keySet(
            V::key('access_token', V::stringType()->notEmpty()),
            V::keyOptional('any_email', V::arrayVal()->each(V::email(), V::intType()))
        );

        V::doValidate($rule, $params);

        $emails = implode(',', $params['any_email'] ?? []);
        $header = ['Authorization' => $params['access_token']];
        $query  = empty($emails) ? [] : ['any_email' => $emails];

        return $this->options
        ->getSync()
        ->setQuery($query)
        ->setHeaderParams($header)
        ->get(API::LIST['drafts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * add draft
     *
     * @param array $params
     * @return array
     */
    public function addDraft(array $params)
    {
        $rules = $this->getBaseRules();

        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);

        $header = ['Authorization' => $params['access_token']];

        unset($params['access_token']);

        return $this->options
        ->getSync()
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->post(API::LIST['drafts']);
    }

    // ------------------------------------------------------------------------------

    /**
     * update draft
     *
     * @param array $params
     * @return array
     */
    public function updateDraft(array $params)
    {
        $rules = $this->getUpdateRules();

        $params['access_token'] =
        $params['access_token'] ?? $this->options->getAccessToken();

        V::doValidate(V::keySet(...$rules), $params);

        $path   = $params['id'];
        $header = ['Authorization' => $params['access_token']];

        unset($params['id'], $params['access_token']);

        return $this->options
        ->getSync()
        ->setPath($path)
        ->setFormParams($params)
        ->setHeaderParams($header)
        ->put(API::LIST['oneDraft']);
    }

    // ------------------------------------------------------------------------------

    /**
     * get draft
     *
     * @param string|array $draftId
     * @param string $accessToken
     * @return array
     */
    public function getDraft($draftId, string $accessToken = null)
    {
        $draftId     = Helper::fooToArray($draftId);
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        $rule = V::each(V::stringType()->notEmpty(), V::intType());

        V::doValidate($rule, $draftId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneDraft'];
        $header = ['Authorization' => $accessToken];

        foreach ($draftId as $id)
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

        return Helper::concatPoolInfos($draftId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * delete draft
     *
     * @param array $params
     * @param string $accessToken
     * @return array
     */
    public function deleteDraft(array $params, string $accessToken = null)
    {
        $params      = Helper::arrayToMulti($params);
        $accessToken = $accessToken ?? $this->options->getAccessToken();

        $rule = V::each(V::keySet(
            V::key('id', V::stringType()->notEmpty()),
            V::key('version', V::intType()->min(0))
        ));

        V::doValidate($rule, $params);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneDraft'];
        $header = ['Authorization' => $accessToken];

        foreach ($params as $item)
        {
            $request = $this->options
            ->getAsync()
            ->setPath($item['id'])
            ->setFormParams(['version' => $item['version']])
            ->setHeaderParams($header);

            $queues[] = function () use ($request, $target)
            {
                return $request->delete($target);
            };
        }

        $dftId = Helper::generateArray($params, 'id');
        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($dftId, $pools);
    }

    // ------------------------------------------------------------------------------

    /**
     * array of string
     *
     * @return \Respect\Validation\Validator
     */
    private function arrayOfString()
    {
        return V::arrayVal()->each(
            V::stringType()->notEmpty(),
            V::intType()
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * array of object
     *
     * @return \Respect\Validation\Validator
     */
    private function arrayOfObject()
    {
        return V::arrayType()->each(
            V::keySet(
                V::key('name', V::stringType(), false),
                V::key('email', V::email())
            ),
            V::intType()
        );
    }

    // ------------------------------------------------------------------------------

    /**
     * rules for update
     *
     * @return array
     */
    private function getUpdateRules()
    {
        $rules = $this->getBaseRules();

        $update =
        [
            V::key('id', V::stringType()->notEmpty()),
            V::key('version', V::intType()->min(0))
        ];

        return array_merge($rules, $update);
    }

    // ------------------------------------------------------------------------------

    /**
     * draft base validate rules
     *
     * @return array
     */
    private function getBaseRules()
    {
        return
        [
            V::keyOptional('to', $this->arrayOfObject()),
            V::keyOptional('cc', $this->arrayOfObject()),
            V::keyOptional('bcc', $this->arrayOfObject()),
            V::keyOptional('from', $this->arrayOfObject()),
            V::keyOptional('reply_to', $this->arrayOfObject()),

            V::keyOptional('file_ids', $this->arrayOfString()),
            V::keyOptional('subject', V::stringType()),
            V::keyOptional('body', V::stringType()),

            V::key('access_token', V::stringType()->notEmpty())
        ];
    }

    // ------------------------------------------------------------------------------

}
