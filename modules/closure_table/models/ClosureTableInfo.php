<?php

namespace app\modules\closure_table\models;

use Yii;

/**
 * This is the model class for table "closure_table_info".
 *
 * @property integer $user_id
 * @property string $user_name
 * @property string $user_status
 */
class ClosureTableInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $node_tree="closure_table_main";//имя таблицы замыканий
    public $node="closure_table_info";//имя таблицы замыканий
    public static function tableName()
    {
        return 'closure_table_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name'], 'required'],
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
            'user_status' => 'User Status',
        ];
    }
    public function afterSave($insert, $changedAttributes){
        /*
            После сохранения наполняем таблицу  
        */
        parent::afterSave($insert, $changedAttributes);
        try{
             $level=Yii::$app->db->createCommand("INSERT INTO ".$this->node_tree." VALUES (NULL,".$this->user_id.",1,1)")->query();
            /*
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
            */
        }
        catch(Exception $e){
            //ловим исключение
        }

    }
    //получение поддерева наследников
    public function getChildTree($depth,$root=true){
        //функция возвращает ассоциативный массив - всех потомков текущего узла до глубины $depth
        //получаем свой уровень
        $level=Yii::$app->db->createCommand("SELECT level FROM ".$this->node_tree." WHERE ancestor=".$this->node_id." AND descendant=".$this->node_id)->queryOne();  
        //выясняем до какого уровня брать           
        $level=$level['level']+$depth;
        $till=$level-1;
        if ($root){
            $result=Yii::$app->db->createCommand("SELECT ancestor,descendant,level,node_title FROM ".$this->node_tree.",node WHERE ancestor=".$this->node_id." AND level>=".$till." AND level<".$level." AND node_id=descendant")->queryAll();
        }
        else{
            $result=Yii::$app->db->createCommand("SELECT ancestor,descendant,level,node_title FROM ".$this->node_tree.",node WHERE ancestor=".$this->node_id." AND level>=".$till." AND level<".$level." AND node_id=descendant AND descendant<>".$this->node_id)->queryAll();          
        }
        if (count($result)==0) $result=false;
        return $result;
    }
    public static function maxDepth(){
        //функция возвращает максимальную глубину дерева
        $level=Yii::$app->db->createCommand("SELECT MAX(level) AS maxlevel FROM ".$this->node_tree)->queryOne();
        return $level['maxlevel'];
    }
    public function getJsonTree(){
        //функция возвращает дерево в формате json. Родителем текущий объект
        //!!!для теста получим два уровня - корень и следующий
        //создаём корень
        $a["name"]=$this->node_title;
        //получаем первый уровень
        $tree=$this->getChildTree(2);           
        //осуществляем разбор ассоциативного массива
        $inner_array=array();       
        foreach ($tree as $item){
            //$inner_array[]=array("name"=>$item['node_title']);            
            array_push($inner_array,array("name"=>$item['node_title']));
        }
        $a["children"]=$inner_array;
        return $a;
    }
    public static function recoursiveTree($node_id,$root=false){
        $node=new Node();
        $node=$node->findOne($node_id);
        $a["name"]=$node->node_title;
        $tree=$node->getChildTree(2,$root);
        if (!$tree){
            return $a;
        }
        else{
            $inner_array=array();
            foreach ($tree as $item){
                $inner_array[]=self::recoursiveTree($item["descendant"],false);
            }
            $a["children"]=$inner_array;
        }
        return $a;
    }
    public function formJsonFile(){
        $tree=self::recoursiveTree($this->node_id,true);
        $file=fopen($_SERVER['DOCUMENT_ROOT'].'/d3/flare.json', 'w');
        fwrite($file, json_encode($tree));
    }
}



