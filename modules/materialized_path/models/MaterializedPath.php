<?php

namespace app\modules\materialized_path\models;

use Yii;

/**
 * This is the model class for table "materialized_path".
 *
 * @property string $user_id
 * @property string $number_id
 * @property string $user_name
 * @property string $user_path
 * @property string $user_status
 */
class MaterializedPath extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'materialized_path';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number_id', 'user_name', 'user_path'], 'required'],
            [['number_id'], 'integer'],
            [['user_name', 'user_path'], 'string', 'max' => 255],
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
            'user_name' => 'User Name',
            'user_path' => 'User Path',
            'user_status' => 'User Status',
        ];
    }
}
