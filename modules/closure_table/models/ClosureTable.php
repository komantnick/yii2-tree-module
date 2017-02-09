<?php

namespace app\modules\closure_table\models;

use Yii;


class ClosureTable extends \yii\db\ActiveRecord
{
    public $node_tree="closure_table_main";//имя таблицы замыканий

    public static function tableName()
    {
        return 'closure_table_main';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'level'], 'required'],
            [['user_id', 'parent_id', 'level'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'num_id' => 'Num ID',
            'user_id' => 'User ID',
            'parent_id' => 'Parent ID',
            'level' => 'Level',
        ];
    }

    public function afterSave($insert, $changedAttributes){
        /*
            После сохранения наполняем таблицу  
        */
        parent::afterSave($insert, $changedAttributes);
        try{
            //этот код для сохранения нового!!! 
            $transaction = Yii::$app->db->beginTransaction();
            if ($this->parent_id!=0){//есть родитель
                //выясним уровень на котором лежит родитель
                $level=Yii::$app->db->createCommand("SELECT level FROM ".$this->node_tree." WHERE ancestor=".$this->parent_id." AND descendant=".$this->parent_id)->queryOne();
                //увеличим уровень на 1
                $level=$level['level']+1;
                //запишем всё в таблицу замыканий
                $blocks=Yii::$app->db->createCommand("INSERT INTO ".$this->node_tree." (ancestor,descendant,level) SELECT ancestor,".$this->node_id.",".$level." FROM ".$this->node_tree." WHERE descendant=".$this->parent_id." UNION ALL SELECT ".$this->node_id.",".$this->node_id.",".$level)->query();
            }
            else{//корень
                $blocks=Yii::$app->db->createCommand("INSERT INTO ".$this->node_tree." (ancestor,descendant,level) VALUES (".$this->node_id.",".$this->node_id.",0)")->query();
            }
            $transaction->commit();
        }
        catch(Exception $e){
            //ловим исключение
        }
    }
}
