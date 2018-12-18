<?php namespace Nylas\Messages;

use Nylas\Labels\Label;
use Nylas\Folders\Folder;
use Nylas\Utilities\API;
use Nylas\Utilities\Helper;
use Nylas\Utilities\Options;
use Nylas\Utilities\Validate as V;

/**
 * ----------------------------------------------------------------------------------
 * Nylas Messages Smart Update
 * ----------------------------------------------------------------------------------
 *
 * @author lanlin
 * @change 2018/12/18
 */
class Smart
{

    // ------------------------------------------------------------------------------

    /**
     * @var \Nylas\Utilities\Options
     */
    private $options;

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
     * add labels to message
     *
     * @param string $messageId
     * @param string|array $labels
     * @return array
     */
    public function addLabels(string $messageId, $labels)
    {
        return $this->updateLabels($messageId, $labels);
    }

    // ------------------------------------------------------------------------------

    /**
     * remove labels from message
     *
     * @param string $messageId
     * @param string|array $labels
     * @return array
     */
    public function removeLabels(string $messageId, $labels)
    {
        return $this->updateLabels($messageId, [], $labels);
    }

    // ------------------------------------------------------------------------------

    /**
     * archive message
     *
     * @param string $messageId
     * @return array
     */
    public function archive(string $messageId)
    {
        return Helper::isLabel($this->options) ?
        $this->updateLabels($messageId, null, ['inbox']) :
        $this->updateFolder($messageId, 'archive');
    }

    // ------------------------------------------------------------------------------

    /**
     * unarchive message
     *
     * @param string $messageId
     * @return array
     */
    public function unarchive(string $messageId)
    {
        return Helper::isLabel($this->options) ?
        $this->updateLabels($messageId, ['inbox'], ['archive']) :
        $this->updateFolder($messageId, 'inbox');
    }

    // ------------------------------------------------------------------------------

    /**
     * move message to trash
     *
     * @param string $messageId
     * @return array
     */
    public function trash(string $messageId)
    {
        return Helper::isLabel($this->options) ?
        $this->updateLabels($messageId, ['trash'], ['inbox']) :
        $this->updateFolder($messageId, 'trash');
    }

    // ------------------------------------------------------------------------------

    /**
     * move from "label|folder" to other "label|folder" by name
     *
     * @param string $messageId
     * @param string $from
     * @param string $goto
     * @return array
     */
    public function move(string $messageId, string $from, string $goto)
    {
        return Helper::isLabel($this->options) ?
        $this->updateLabels($messageId, [$goto], [$from]) :
        $this->updateFolder($messageId, $goto);
    }

    // ------------------------------------------------------------------------------

    /**
     * set message to start
     *
     * @param string|array $messageId
     * @return array
     */
    public function star($messageId)
    {
        $params = ['starred' => true];

        return $this->updateOneField($messageId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * set message to un-star
     *
     * @param string|array $messageId
     * @return array
     */
    public function unstar($messageId)
    {
        $params = ['starred' => false];

        return $this->updateOneField($messageId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * mark message as read
     *
     * @param string|array $messageId
     * @return array
     */
    public function markAsRead($messageId)
    {
        $params = ['unread' => false];

        return $this->updateOneField($messageId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * mark message as unread
     *
     * @param string|array $messageId
     * @return array
     */
    public function markAsUnread($messageId)
    {
        $params = ['unread' => true];

        return $this->updateOneField($messageId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * move message to folder by id
     *
     * @param string|array $messageId
     * @param string $folderId
     * @return array
     */
    public function moveToFolder($messageId, string $folderId)
    {
        Helper::checkProviderUnit($this->options, false);

        V::doValidate(V::stringType()->notEmpty(), $folderId);

        $params = ['folder_id' => $folderId];

        return $this->updateOneField($messageId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * move message to labels by id
     *
     * @param string|array $messageId
     * @param array $labelIds
     * @return array
     */
    public function moveToLabel($messageId, array $labelIds)
    {
        Helper::checkProviderUnit($this->options, true);

        V::doValidate(V::each(V::stringType()->notEmpty()), $labelIds);

        $params = ['label_ids' => $labelIds];

        return $this->updateOneField($messageId, $params);
    }

    // ------------------------------------------------------------------------------

    /**
     * update message folder
     *
     * @param string $messageId
     * @param string $folder
     * @return array
     */
    private function updateFolder(string $messageId, string $folder)
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

        return $this->moveToLabel($messageId, $folderId);
    }

    // ------------------------------------------------------------------------------

    /**
     * update message labels
     *
     * @param string $messageId
     * @param string|array $add
     * @param string|array $del
     * @return array
     */
    private function updateLabels(string $messageId, $add = [], $del = [])
    {
        $tmpLabels = [];
        $allLabels = (new Label($this->options))->getLabelsList();
        $emailData = (new Message($this->options))->getMessage($messageId);
        $nowLabels = $emailData[$messageId]['labels'] ?? [];

        $add = Helper::fooToArray($add);
        $del = Helper::fooToArray($del);

        // check all labels
        foreach($allLabels as $label)
        {
            $secA = !empty($label['name']) && in_array($label['name'], $add);
            $secB = empty($label['name']) && in_array($label['display_name'], $add);

            if ($secA || $secB)
            {
                array_push($tmpLabels, $label['id']);
            }
        }

        // check current message labels
        foreach($nowLabels as $index => $label)
        {
            $secA = !empty($label['name']) && in_array($label['name'], $del);
            $secB = empty($label['name']) && in_array($label['display_name'], $del);

            if ($secA || $secB) { continue; }

            array_push($tmpLabels, $label['id']);
        }

        return $this->moveToLabel($messageId, $tmpLabels);
    }

    // ------------------------------------------------------------------------------

    /**
     * update the specific field of message
     *
     * @param string|array $messageId
     * @param array $params
     * @return array
     */
    private function updateOneField($messageId, array $params)
    {
        $messageId    = Helper::fooToArray($messageId);
        $accessToken = $this->options->getAccessToken();

        $rule = V::each(V::stringType()->notEmpty(), V::intType());

        V::doValidate($rule, $messageId);
        V::doValidate(V::stringType()->notEmpty(), $accessToken);

        $queues = [];
        $target = API::LIST['oneMessage'];
        $header = ['Authorization' => $accessToken];

        foreach ($messageId as $id)
        {
            $request = $this->options
            ->getAsync()
            ->setPath($id)
            ->setFormParams($params)
            ->setHeaderParams($header);

            $queues[] = function () use ($request, $target)
            {
                return $request->put($target);
            };
        }

        $pools = $this->options->getAsync()->pool($queues, false);

        return Helper::concatPoolInfos($messageId, $pools);
    }

    // ------------------------------------------------------------------------------

}
