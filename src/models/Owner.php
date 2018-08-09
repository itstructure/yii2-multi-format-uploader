<?php

namespace Itstructure\MFUploader\models;

use yii\db\ActiveQuery;
use yii\base\InvalidArgumentException;

/**
 * This is the base class for owners.
 *
 * @property int $ownerId Owner id.
 * @property string $owner Owner name (post, article, page e.t.c.).
 * @property string $ownerAttribute Owner attribute (thumbnail, image e.t.c.).
 *
 * @package Itstructure\MFUploader\models
 *
 * @author Andrey Girnik <girnikandrey@gmail.com>
 */
class Owner extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'ownerId',
                    'owner',
                    'ownerAttribute',
                ],
                'required',
            ],
            [
                'ownerId',
                'integer',
            ],
            [
                [
                    'owner',
                    'ownerAttribute',
                ],
                'string',
                'max' => 255,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'ownerId' => 'Owner ID',
            'owner' => 'Owner',
            'ownerAttribute' => 'Owner Attribute',
        ];
    }

    /**
     * Get Id's by owner.
     *
     * @param string $nameId
     * @param array $args It can be an array of the next params: owner{string}, ownerId{int}, ownerAttribute{string}.
     *
     * @throws InvalidArgumentException
     *
     * @return ActiveQuery
     */
    protected static function getEntityIdsQuery(string $nameId, array $args): ActiveQuery
    {
        $conditions = [];

        if (isset($args['owner'])) {
            if (!is_string($args['owner']) || empty($args['owner'])) {
                throw new InvalidArgumentException('Parameter owner must be a string.');
            }
            $conditions['owner'] = $args['owner'];

            if (isset($args['ownerId'])) {
                if (!is_numeric($args['ownerId'])) {
                    throw new InvalidArgumentException('Parameter ownerId must be numeric.');
                }
                $conditions['ownerId'] = $args['ownerId'];
            }
        }

        if (isset($args['ownerAttribute'])) {
            if (!is_string($args['ownerAttribute']) || empty($args['ownerAttribute'])) {
                throw new InvalidArgumentException('Parameter ownerAttribute must be a string.');
            }
            $conditions['ownerAttribute'] = $args['ownerAttribute'];
        }

        $query = static::find()
            ->select($nameId);

        if (count($conditions) > 0) {
            return $query->where($conditions);
        }

        return $query;
    }
}
