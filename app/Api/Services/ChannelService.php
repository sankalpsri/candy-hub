<?php

namespace GetCandy\Api\Services;

use GetCandy\Api\Exceptions\MinimumRecordRequiredException;
use GetCandy\Api\Models\Channel;

class ChannelService extends BaseService
{
    /**
     * @var AttributeGroup
     */
    protected $model;

    public function __construct()
    {
        $this->model = new Channel();
    }

    /**
     * Creates a resource from the given data
     *
     * @param  array  $data
     *
     * @return GetCandy\Api\Models\Channel
     */
    public function create(array $data)
    {
        $channel = new Channel();
        $channel->name = $data['name'];

        // If this is the first channel, make it default
        if (empty($data['default']) && !$this->count()) {
            $channel->default = true;
        }

        if (!empty($data['default'])) {
            $this->setNewDefault($channel);
        } else {
            $channel->default = false;
        }

        $channel->save();

        return $channel;
    }

    /**
     * Updates a resource from the given data
     *
     * @param  string $id
     * @param  array  $data
     *
     * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws GetCandy\Api\Exceptions\MinimumRecordRequiredException
     *
     * @return GetCandy\Api\Models\Channel
     */
    public function update($hashedId, array $data)
    {
        $channel = $this->getByHashedId($hashedId);

        if (!$channel) {
            return null;
        }

        $channel->fill($data);

        if (!empty($data['default'])) {
            $this->setNewDefault($channel);
        }

        $channel->save();

        return $channel;
    }

    /**
     * Deletes a resource by its given hashed ID
     *
     * @param  string $id
     *
     * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @throws GetCandy\Api\Exceptions\MinimumRecordRequiredException
     *
     * @return Boolean
     */
    public function delete($id)
    {
        $channel = $this->getByHashedId($id);

        if (!$channel) {
            abort(404);
        }

        if ($this->model->count() == 1) {
            throw new MinimumRecordRequiredException(
                trans('getcandy_api::response.error.minimum_record')
            );
        }

        if ($channel->default && $newDefault = $this->model->first()) {
            $newDefault->default = true;
            $newDefault->save();
        }

        return $channel->delete();
    }

    /**
     * Finds and sets a new default record based on whats available
     * @param GetCandy\Api\Models\Channel &$model
     */
    protected function setNewDefault(&$model)
    {
        if ($current = $this->getDefaultRecord()) {
            $current->default = false;
            $current->save();
        }
        $model->default = true;
    }
}