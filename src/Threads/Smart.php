<?php namespace Nylas\Threads;

use Nylas\Labels\Label;
use Nylas\Folders\Folder;
use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validator as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Threads Start Update
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2020/04/27
 */
class Smart
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private Options $options;

    // ------------------------------------------------------------------------------

    /**
     * Search constructor.
     *
     * @param \Nylas\Utilities\Options $options
     */
    public function __construct(Options $options)
    {
        $this->options = $options;
    }

    // ------------------------------------------------------------------------------

    /**
     * add labels to thread
     *
     * @param string $threadId
     * @param string|array $labels
     * @return array
     */
    public function addLabels(string $threadId, $labels) : array
    {
        return $this->updateLabels($threadId, $labels);
    }

    // ------------------------------------------------------------------------------

    /**
     * remove labels from thread
     *
     * @param string $threadId
     * @param string|array $labels
     * @return array
     */
    public function removeLabels(string $threadId, $labels) : array
    {
        return $this->updateLabels($threadId, null, $labels);
    }

    // ------------------------------------------------------------------------------

    /**
     * archive thread
     *
     * @param string $threadId
     * @return array
     */
    public function archive(string $threadId) : array
    {
        return Helper::isLabel($this->options) ?
        $this->updateLabels($threadId, null, ['inbox']) :
        $this->updateFolder($threadId, 'archive');
    }

    // ------------------------------------------------------------------------------

    /**
     * unarchive thread
     *
     * @param string $threadId
     * @return array
     */
    public function unarchive(string $threadId) : array
    {
        return Helper::isLabel($this->options) ?
        $this->updateLabels($threadId, ['inbox'], ['archive']) :
        $this->updateFolder($threadId, 'inbox');
    }

    // ------------------------------------------------------------------------------

    /**
     * move thread to trash
     *
     * @param string $threadId
     * @return array
     */
    public function trash(string $threadId) : array
    {
        return Helper::isLabel($this->options) ?
        $this->updateLabels($threadId, ['trash'], ['inbox']) :
        $this->updateFolder($threadId, 'trash');
    }

    // ------------------------------------------------------------------------------

    /**
     * move from "label|folder" to other "label|folder" by name
     *
     * @param string $threadId
     * @param string $from
     * @param string $goto
     * @return array
     */
    public function move(string $threadId, string $from, string $goto) : array
    {
        return Helper::isLabel($this->options) ?
        $this->updateLabels($threadId, [$goto], [$from]) :
        $this->updateFolder($threadId, $goto);
    }

    // ------------------------------------------------------------------------------

    /**
     * set thread to start
     *
     * @param string|array $threadId
     * @return array
     */
    public function star($threadId) : array
    {
        $params = ['starred' => true];

        return $this->updateOneField($threadId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * set thread to un-star
     *
     * @param string|array $threadId
     * @return array
     */
    public function unstar($threadId) : array
    {
        $params = ['starred' => false];

        return $this->updateOneField($threadId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * mark thread as read
     *
     * @param string|array $threadId
     * @return array
     */
    public function markAsRead($threadId) : array
    {
        $params = ['unread' => false];

        return $this->updateOneField($threadId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * mark thread as unread
     *
     * @param string|array $threadId
     * @return array
     */
    public function markAsUnread($threadId) : array
    {
        $params = ['unread' => true];

        return $this->updateOneField($threadId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * move thread to folder by id
     *
     * @param string|array $threadId
     * @param string $folderId
     * @return array
     */
    public function moveToFolder($threadId, string $folderId) : array
    {
        Helper::checkProviderUnit($this->options, false);

        V::doValidate(V::stringType()->notEmpty(), $folderId);

        $params = ['folder_id' => $folderId];

        return $this->updateOneField($threadId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * move thread to labels by id
     *
     * @param string|array $threadId
     * @param array $labelIds
     * @return array
     */
    public function moveToLabel($threadId, array $labelIds) : array
    {
        Helper::checkProviderUnit($this->options, true);

        V::doValidate(V::simpleArray(V::stringType()->notEmpty()), $labelIds);

        $params = ['label_ids' => $labelIds];

        return $this->updateOneField($threadId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * update thread folder
     *
     * @param string $threadId
     * @param string $folder
     * @return array
     */
    private function updateFolder(string $threadId, string $folder) : array
    {
        $folderId   = null;
        $allFolders = (new Folder($this->options))->getFoldersList();

        foreach ($allFolders as $row)
        {
            if ($row['name'] === $folder)
            {
                $folderId = $row['id'];
                break;
            }
        }

        return $this->moveToLabel($threadId, $folderId);
    }

    // ------------------------------------------------------------------------------

    /**
     * update thread labels (update with name or display name)
     *
     * @param string $threadId
     * @param string|array $add
     * @param string|array $del
     * @return array
     */
    private function updateLabels(string $threadId, $add = [], $del = []) : array
    {
        $tmpLabels = [];
        $allLabels = (new Label($this->options))->getLabelsList();
        $threadMsg = (new Thread($this->options))->getThread($threadId);
        $nowLabels = $threadMsg[$threadId]['labels'] ?? [];

        $add = Helper::fooToArray($add);
        $del = Helper::fooToArray($del);

        // check all labels
        foreach($allLabels as $label)
        {
            $secA = !empty($label['name']) && in_array($label['name'], $add, true);
            $secB = empty($label['name']) && in_array($label['display_name'], $add, true);

            if ($secA || $secB)
            {
                $tmpLabels[] = $label['id'];
            }
        }

        // check current thread labels
        foreach($nowLabels as $index => $label)
        {
            $secA = !empty($label['name']) && in_array($label['name'], $del, true);
            $secB = empty($label['name']) && in_array($label['display_name'], $del, true);

            if ($secA || $secB) { continue; }

            $tmpLabels[] = $label['id'];
        }

        return $this->moveToLabel($threadId, $tmpLabels);
    }

    // ------------------------------------------------------------------------------

    /**
     * update the specific field of thread
     *
     * @param string|array $threadId
     * @param array $params
     * @return array
     */
    private function updateOneField($threadId, array $params) : array
    {
        $threadId    = Helper::fooToArray($threadId);
        $accessToken = $this->options->getAccessToken();

        $rule = V::simpleArray(V::stringType()->notEmpty());

        V::doValidate($rule, $threadId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneThread'];
        $header = ['Authorization' => $accessToken];

        foreach ($threadId as $id)
        {
            $request = $this->options
            ->getAsync()
            ->setPath($id)
            ->setFormParams($params)
            ->setHeaderParams($header);

            $queues[] = static function () use ($request, $target)
            {
                return $request->put($target);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($threadId, $pools);
    }

    // ------------------------------------------------------------------------------

}
