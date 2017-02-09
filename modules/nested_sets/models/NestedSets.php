<?php

namespace app\modules\nested_sets\models;

use Yii;

/**
 * This is the model class for table "nested_sets".
 *
 * @property integer $user_id
 * @property string $user_name
 * @property integer $left_number
 * @property integer $right_number
 * @property integer $level
 * @property string $user_status
 */
class NestedSets extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nested_sets';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'left_number', 'right_number', 'level'], 'required'],
            [['left_number', 'right_number', 'level'], 'integer'],
            [['user_name'], 'string', 'max' => 11],
            [['user_status'], 'string', 'max' => 1],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'left_number' => 'Left Number',
            'right_number' => 'Right Number',
            'level' => 'Level',
            'user_status' => 'User Status',
        ];
    }
}
