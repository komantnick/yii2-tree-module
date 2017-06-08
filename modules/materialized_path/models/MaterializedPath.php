<?php

namespace app\modules\materialized_path\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\QueryBuilder;
use yii\db\Command;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Expression;
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
    public function get($id){
        $query= new Query();
        $query -> from ('materialized_path')
               -> where(['number_id' => $id]);
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }
    public function getNumber(){
        $query=new Query();
        $query->select (['COUNT(*) AS cnt'])
              -> from('materialized_path');
        $command = $query->createCommand();
         $data = $command->queryAll();
        return $data[0]['cnt'];        
    }
    public function create($name){
         
        $query_a= new Query();
        $query_a -> createCommand()
               -> delete('materialized_path')
               -> execute();
        $numb=$this->getNumber()+1;
        $ai=str_pad($numb,7,"0",STR_PAD_LEFT);
        $mpath='/'.$ai;
        $connection=\Yii::$app->db;
        $connection->createCommand()
                   ->insert('materialized_path',[
                    'number_id'=>$this->getNumber()+1,
                    'user_name'=>$name,
                    'user_path'=>$mpath,
                    ])
                   ->execute();
        return 'Новое дерево создано!';
    }
    public function add($id,$name){
         $node=$this->get($id);
        $user=$node[0]['number_id'];
        $mpath=$node[0]['user_path'];
        //echo $mpath;exit;
        $numb=$this->getNumber()+1;
        $ai=str_pad($numb,7,"0",STR_PAD_LEFT);
        $path=$mpath.'/'.$ai;
        $connection=\Yii::$app->db;
        $connection->createCommand()
                   ->insert('materialized_path',[
                    'number_id'=>$this->getNumber()+1,
                    'user_name'=>$name,
                    'user_path'=>$path,
                    ])
                   ->execute();
        return 'Узел добавлен!';
    }
    public function deletenode($id){
         $node=$this->get($id);
        $user=$node[0]['number_id'];
        $query_a= new Query();
        $query_a -> createCommand()
               -> delete('materialized_path')
               -> where(['number_id'=>$user])
               -> execute();
        return 'Узел удален';


    }
    public function block($id){
        $node=$this->get($id);
        $user=$node[0]['number_id'];
        $connection=\Yii::$app->db;
        $connection->createCommand()
                   ->update('materialized_path',[
                    'user_status'=>0,
                    ],'number_id='.$id.'')
                   ->execute();
        return 'Узел заблокирован!';
        
    }
    public function move($id,$id_to){
         $node = $this->get($id);    
        $node_to = $this->get($id_to); 
        $path=$node[0]['user_path'];
        $path_0=/*'"'.*/$path/*.'"'*/;
        $path='"'.$path.'"';

        $path_to=$node_to[0]['user_path'];
        $path_to='"'.$path_to.'/'.$node[0]['number_id'].'"'; 
        $connection=\Yii::$app->db;
        $expression = new Expression('REPLACE(user_path, '.$path.', '.$path_to.')');
        $connection->createCommand()
                   ->update('materialized_path',[
                    'user_path'=>$expression
                    /*REPLACE(user_path, '.$path.', '.$path_to.'),'user_path',*/
                    /*'user_path'=>REPLACE(user_path, '.$path.', '.$path_to.'),*/
                    ],['like','user_path',$path_0 ])
                   ->execute();
        return 'Узел перемещен!';
    }
    public function child_branch($id){
        $node = $this->get($id);
    $path=$node[0]['user_path']; 
    //$path='"'.$path.'%"';
    //echo $path;exit;
     $query= new Query();
        $query -> from ('materialized_path')
               -> where(['like','user_path',$path ])
               -> orderBy('user_path');
        $command = $query->createCommand();
        $data = $command->queryAll();
        $json=array();
        $parent=array();
        $path=array();
        $tmp=array();
        $oks=array();
        $num=0;
        for ($i=0;$i<count($data);$i++){
            $x=substr_count($data[$i]['user_path'],'/');
            $json[$i]['name']=$data[$i]['number_id']."-".$data[$i]['user_name'];
            if (empty($parent)) {$json[$i]['parent']=NULL;}
            else if ($x<=$num){
                for ($v=count($parent)-1;$v>=0;$v--){
                    if (substr_count($path[$v],'/')>$x-1) {array_pop($parent);}
                    else {break;}
                }
                $json[$i]['parent']=array_pop($parent);array_push($parent,$json[$i]['parent']);
            }
            else {$json[$i]['parent']=array_pop($parent);array_push($parent,$json[$i]['parent']);}
            array_push($parent,$json[$i]['name']);
            array_push($path,$data[$i]['user_path']);
            $num=$x;
            


        }
        
        for ($i=0;$i<count($json);$i++){
            $search=$json[$i]['name'];
            for ($j=0;$j<count($json);$j++){
                if ($search==$json[$j]['parent']) {
                    $json[$i]['children']='Reserve';
                }
            }
        }
        //1 цикл сортировки
        for ($i=0;$i<count($json);$i++){
            if (empty($json[$i]['children'])){
                for ($j=0;$j<count($json);$j++){
                    if ($json[$i]['parent']==$json[$j]['name']&&(isset($json[$j]['children']))){
                        if (empty($tmp[$json[$i]['parent']])) {$tmp[$json[$i]['parent']]=array();}
                        array_push($tmp[$json[$i]['parent']],$json[$i]);
                        //$tmp[$json[$i]['parent']][$json[$i]['name']]=$json[$i];
                        
                        $arry=array();


                        $json[$j]['children']=$tmp[$json[$i]['parent']];

                         array_splice($json,$i,1);
                         $j=0;$i=0;
                    }
                   
                }
            }
        }
        //2 цикл сортировки
       for ($i=count($json)-1;$i>=0;$i--){
        $i=count($json)-1;
        
            for ($j=0;$j<count($json);$j++){
                
                if ($json[$i]['parent']==
                    $json[$j]['name']
                    &&(isset($json[$j]['children']))){
                    if (empty($tmp[$json[$i]['parent']])) {$tmp[$json[$i]['parent']]=array();}
                        array_push($tmp[$json[$i]['parent']],$json[$i]);
                        
                        $json[$j]['children']=$tmp[$json[$i]['parent']];
  
                         array_splice($json,$i,1);
                         $j=0;$i=count($json)-1;


                } 
            }

        }
        //return $json;

        $str='';
        foreach ($json as $value){
            $str.=json_encode($value).',';

         }
         return $str;
    }
    //алгоритм Флойда
    public function floyd($number){
        $inf=999999;
        $floyd=array();
        $distance = array();
        //генерация используемого массива.
        for ($i=0;$i<$number;$i++){
            for ($j=0;$j<$i;$j++){
                $floyd[$i][$j]=$inf;
            }
            for ($j=$i;$j<$number;$j++){
                $rand=rand(1,2);
                if ($i==$j) {$floyd[$i][$j]=0;}
                else if ($rand==2) {$floyd[$i][$j]=$inf;}
                else {$floyd[$i][$j]=rand(1,25);}
            }
        }
        //return $floyd;
        //алгоритм Флойда-Уоршелла
     echo "Таблица расстояний графа Флойда:" . "<br/>";
    echo "<table border='1'>";
    for ($i = 0; $i < $number; ++$i)
    {
        echo "<tr>";

        for ($j = 0; $j < $number; ++$j)
        {
            if ($floyd[$i][$j] == $inf)
                echo "<td>"."INF"."</td>";
            else
                echo "<td>".$floyd[$i][$j]."</td>";
        }
        echo "<tr/>";
    }
    echo "</table>";
    for ($i = 0;$i<$number; $i++)
        for ($j = 0; $j < $number;$j++)
            $distance[$i][$j] = $floyd[$i][$j];

    for ($k=0;$k < $number; $k++)
    {
        for ($i=0; $i < $number; $i++)
        {
            for ($j=0;$j<$number;$j++)
            {
                if ($distance[$i][$k] + $distance[$k][$j] < $distance[$i][$j])
                    $distance[$i][$j] = $distance[$i][$k] + $distance[$k][$j];
            }
        }
    }
    return $distance;
    }
    public function child($id){
          $node = $this->get($id);
    $path=$node[0]['user_path']; 
     $patho='"'.$path.'/%"';
    $expression = new Expression('CONCAT('.$patho.',"/%")');
     $query= new Query();
        $query -> from ('materialized_path')
               -> where(['and',['like','user_path',$path],['not like','user_path',$expression ]])
               -> orderBy('user_path');
        $command = $query->createCommand();
        $data = $command->queryAll();
        $json=array();
        $parent=array();
        $path=array();
        $tmp=array();
        $oks=array();
        $num=0;
        for ($i=0;$i<count($data);$i++){
            $x=substr_count($data[$i]['user_path'],'/');
            $json[$i]['name']=$data[$i]['number_id']."-".$data[$i]['user_name'];
            if (empty($parent)) {$json[$i]['parent']=NULL;}
            else if ($x<=$num){
                for ($v=count($parent)-1;$v>=0;$v--){
                    if (substr_count($path[$v],'/')>$x-1) {array_pop($parent);}
                    else {break;}
                }
                $json[$i]['parent']=array_pop($parent);array_push($parent,$json[$i]['parent']);
            }
            else {$json[$i]['parent']=array_pop($parent);array_push($parent,$json[$i]['parent']);}
            array_push($parent,$json[$i]['name']);
            array_push($path,$data[$i]['user_path']);
            $num=$x;
            


        }
        
        for ($i=0;$i<count($json);$i++){
            $search=$json[$i]['name'];
            for ($j=0;$j<count($json);$j++){
                if ($search==$json[$j]['parent']) {
                    $json[$i]['children']='Reserve';
                }
            }
        }
        //1 цикл сортировки
        for ($i=0;$i<count($json);$i++){
            if (empty($json[$i]['children'])){
                for ($j=0;$j<count($json);$j++){
                    if ($json[$i]['parent']==$json[$j]['name']&&(isset($json[$j]['children']))){
                        if (empty($tmp[$json[$i]['parent']])) {$tmp[$json[$i]['parent']]=array();}
                        array_push($tmp[$json[$i]['parent']],$json[$i]);
                        //$tmp[$json[$i]['parent']][$json[$i]['name']]=$json[$i];
                        
                        $arry=array();


                        $json[$j]['children']=$tmp[$json[$i]['parent']];

                         array_splice($json,$i,1);
                         $j=0;$i=0;
                    }
                   
                }
            }
        }
        //2 цикл сортировки
       for ($i=count($json)-1;$i>=0;$i--){
        $i=count($json)-1;
        
            for ($j=0;$j<count($json);$j++){
                
                if ($json[$i]['parent']==
                    $json[$j]['name']
                    &&(isset($json[$j]['children']))){
                    if (empty($tmp[$json[$i]['parent']])) {$tmp[$json[$i]['parent']]=array();}
                        array_push($tmp[$json[$i]['parent']],$json[$i]);
                        
                        $json[$j]['children']=$tmp[$json[$i]['parent']];
  
                         array_splice($json,$i,1);
                         $j=0;$i=count($json)-1;


                } 
            }

        }
        //return $json;

        $str='';
        foreach ($json as $value){
            $str.=json_encode($value).',';

         }
         return $str;
    }
    public function tree(){
        $query= new Query();
        $query -> from ('materialized_path')
               -> orderBy('user_path');
        $command = $query->createCommand();
        $data = $command->queryAll();
        
        $json=array();
        $parent=array();
        $path=array();
        $tmp=array();
        $oks=array();
        $num=0;
        for ($i=0;$i<count($data);$i++){
            $x=substr_count($data[$i]['user_path'],'/');
            $json[$i]['name']=$data[$i]['number_id']."-".$data[$i]['user_name'];
            if (empty($parent)) {$json[$i]['parent']=NULL;}
            else if ($x<=$num){
                for ($v=count($parent)-1;$v>=0;$v--){
                    if (substr_count($path[$v],'/')>$x-1) {array_pop($parent);}
                    else {break;}
                }
                $json[$i]['parent']=array_pop($parent);array_push($parent,$json[$i]['parent']);
            }
            else {$json[$i]['parent']=array_pop($parent);array_push($parent,$json[$i]['parent']);}
            array_push($parent,$json[$i]['name']);
            array_push($path,$data[$i]['user_path']);
            $num=$x;
            


        }
        
        for ($i=0;$i<count($json);$i++){
            $search=$json[$i]['name'];
            for ($j=0;$j<count($json);$j++){
                if ($search==$json[$j]['parent']) {
                    $json[$i]['children']='Reserve';
                }
            }
        }
        
        //1 цикл сортировки
        for ($i=0;$i<count($json);$i++){
            if (empty($json[$i]['children'])){
                for ($j=0;$j<count($json);$j++){
                    if ($json[$i]['parent']==$json[$j]['name']&&(isset($json[$j]['children']))){
                        if (empty($tmp[$json[$i]['parent']])) {$tmp[$json[$i]['parent']]=array();}
                        array_push($tmp[$json[$i]['parent']],$json[$i]);
                        //$tmp[$json[$i]['parent']][$json[$i]['name']]=$json[$i];
                        
                        $arry=array();


                        $json[$j]['children']=$tmp[$json[$i]['parent']];

                         array_splice($json,$i,1);
                         $j=0;$i=0;
                    }
                   
                }
            }
        }
       
        //2 цикл сортировки
       for ($i=count($json)-1;$i>=0;$i--){
        //$i=count($json)-1;
         //print_r($json);exit;
         if (isset($json[$i]['parent'])) {
            for ($j=0;$j<count($json);$j++){
                if ($json[$i]['parent']==
                    $json[$j]['name']
                    &&(isset($json[$j]['children']))){
                    if (empty($tmp[$json[$i]['parent']])) {$tmp[$json[$i]['parent']]=array();}
                        array_push($tmp[$json[$i]['parent']],$json[$i]);
                        
                        $json[$j]['children']=$tmp[$json[$i]['parent']];
  
                         array_splice($json,$i,1);
                         $j=0;$i=count($json)-1;


                } 
            }

         }
            

        }
        //return $json;
        //print_r($json);exit;
        $str='';
        foreach ($json as $value){
            $str.=json_encode($value).',';

         }
         return $str;

    }
    public function parent_branch($id){
         $node = $this->get($id);
    $path=$node[0]['user_path']; 
     $patho='"'.$path.'"';
    $expression = new Expression('CONCAT(user_path,"%")');
    $expressions = new Expression($patho);
     $query= new Query();
        $query -> from ('materialized_path')
               -> where(['like',new Expression($path), $expression])
               -> orderBy('user_path');
        $command = $query->createCommand();
        $data = $command->queryAll();
        $json=array();
        $parent=array();
        $path=array();
        $tmp=array();
        $oks=array();
        $num=0;
        for ($i=0;$i<count($data);$i++){
            $x=substr_count($data[$i]['user_path'],'/');
            $json[$i]['name']=$data[$i]['number_id']."-".$data[$i]['user_name'];
            if (empty($parent)) {$json[$i]['parent']=NULL;}
            else if ($x<=$num){
                for ($v=count($parent)-1;$v>=0;$v--){
                    if (substr_count($path[$v],'/')>$x-1) {array_pop($parent);}
                    else {break;}
                }
                $json[$i]['parent']=array_pop($parent);array_push($parent,$json[$i]['parent']);
            }
            else {$json[$i]['parent']=array_pop($parent);array_push($parent,$json[$i]['parent']);}
            array_push($parent,$json[$i]['name']);
            array_push($path,$data[$i]['user_path']);
            $num=$x;
            


        }
        
        for ($i=0;$i<count($json);$i++){
            $search=$json[$i]['name'];
            for ($j=0;$j<count($json);$j++){
                if ($search==$json[$j]['parent']) {
                    $json[$i]['children']='Reserve';
                }
            }
        }
        //1 цикл сортировки
        for ($i=0;$i<count($json);$i++){
            if (empty($json[$i]['children'])){
                for ($j=0;$j<count($json);$j++){
                    if ($json[$i]['parent']==$json[$j]['name']&&(isset($json[$j]['children']))){
                        if (empty($tmp[$json[$i]['parent']])) {$tmp[$json[$i]['parent']]=array();}
                        array_push($tmp[$json[$i]['parent']],$json[$i]);
                        //$tmp[$json[$i]['parent']][$json[$i]['name']]=$json[$i];
                        
                        $arry=array();


                        $json[$j]['children']=$tmp[$json[$i]['parent']];

                         array_splice($json,$i,1);
                         $j=0;$i=0;
                    }
                   
                }
            }
        }
        //2 цикл сортировки
       for ($i=count($json)-1;$i>=0;$i--){
        $i=count($json)-1;
        
            for ($j=0;$j<count($json);$j++){
                
                if ($json[$i]['parent']==
                    $json[$j]['name']
                    &&(isset($json[$j]['children']))){
                    if (empty($tmp[$json[$i]['parent']])) {$tmp[$json[$i]['parent']]=array();}
                        array_push($tmp[$json[$i]['parent']],$json[$i]);
                        
                        $json[$j]['children']=$tmp[$json[$i]['parent']];
  
                         array_splice($json,$i,1);
                         $j=0;$i=count($json)-1;


                } 
            }

        }
        //return $json;

        $str='';
        foreach ($json as $value){
            $str.=json_encode($value).',';

         }
         return $str;

    }


}
