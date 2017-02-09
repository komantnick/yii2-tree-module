<?php

namespace app\modules\adjacency_list\models;

use Yii;

/**
 * This is the model class for table "adjacency_list".
 *
 * @property integer $user_id
 * @property integer $number_id
 * @property integer $parent_id
 * @property string $user_name
 * @property string $user_status
 */
class AdjacencyList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'adjacency_list';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number_id'], 'required'],
            [['number_id', 'parent_id'], 'integer'],
            [['user_name'], 'string', 'max' => 255],
            [['user_status'], 'string', 'max' => 1],
            [['number_id'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'number_id' => 'Number ID',
            'parent_id' => 'Parent ID',
            'user_name' => 'User Name',
            'user_status' => 'User Status',
        ];
    }
}
