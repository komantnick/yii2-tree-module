<?php

namespace app\modules\adjacency_list\models;

use Yii;
use yii\db\ActiveRecord;
use yii\db\QueryBuilder;
use yii\db\Command;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Query;
/**
 * Model class for tree structure 'adjacency_list' *
 */
class AdjacencyList extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'adjacency_list';
    }
    public function rules()
    {
        return [
            [['parent_id'], 'integer'],
            [['user_name'], 'string', 'max' => 255],
            [['user_status'], 'string', 'max' => 1],
        ];
    }
    public function get($id){
        $query= new Query();
        $query -> from ('adjacency_list')
               -> where(['number_id' => $id]);
        $command = $query->createCommand();
        $data = $command->queryAll();
        return $data;
    }
    public function parent_node($id){
        $node=$this->get($id);
        $parent=$node['parent_id'];
        return $parent;
    }
    public function getNumber(){
        $query=new Query();
        $query->select (['COUNT(*) AS cnt'])
              -> from('adjacency_list');
        $command = $query->createCommand();
         $data = $command->queryAll();
        return $data[0]['cnt'];        
    }
    public function create($name){
        $tableName='adjacency_list';
        $query_a= new Query();
        $query_a -> createCommand()
               -> delete('adjacency_list')
               -> execute();
        $connection=\Yii::$app->db;
        $connection->createCommand()
                   ->insert('adjacency_list',[
                    'number_id'=>$this->getNumber()+1,
                    'user_name'=>$name,
                    ])
                   ->execute();
        return 'Новое дерево создано!';
    }
    public function add($id,$name){
         $node=$this->get($id);
        $user=$node[0]['number_id'];
        $connection=\Yii::$app->db;
        $connection->createCommand()
                   ->insert('adjacency_list',[
                    'number_id'=>$this->getNumber()+1,
                    'parent_id'=>$user,
                    'user_name'=>$name,
                    ])
                   ->execute();
        return 'Узел добавлен!';
    }
    public function deletenode($id){
         $node=$this->get($id);
        $user=$node[0]['number_id'];
        $query_a= new Query();
        $query_a -> createCommand()
               -> delete('adjacency_list')
               -> where(['number_id'=>$id])
               -> execute();
        return 'Узел удален';
    }
    public function block($id){
        $node=$this->get($id);
        $user=$node[0]['number_id'];
        $connection=\Yii::$app->db;
        $connection->createCommand()
                   ->update('adjacency_list',[
                    'user_status'=>0,
                    ],'number_id='.$id.'')
                   ->execute();
        return 'Узел заблокирован!';
    }
    public function move($id,$id_to){
        $node = $this->get($id);    
        $node_to = $this->get($id_to);  
        $connection=\Yii::$app->db;
        $connection->createCommand()
                   ->update('adjacency_list',[
                    'parent_id'=>$id_to,
                    ],'number_id='.$id.'')
                   ->execute();
        return 'Узел перемещен!';

    }
     public function tree(){
          $query=new Query();
        $query->select (['t1.user_name name1','t1.number_id id1', 't1.parent_id par1','t2.user_name name2','t2.number_id id2'])
              -> from('adjacency_list as t1')
              ->leftJoin('adjacency_list as t2','t2.parent_id=t1.number_id');
        $command = $query->createCommand();
         $data = $command->queryAll();
         $json=array();

         $count=0;
          $tmp=array();
         for ($i=0;$i<count($data);$i++){
            if ($data[$i]['par1']==NULL){
                if (empty($val)||$val!=$data[$i]['id1']){

                    if (isset($val)&&$val!=$data[$i]['id1']){
                        for ($v=0;$v<count($tmp)-1;$v++){
       
                        for ($vv=$v+1;$vv<count($tmp);$vv++){
                           
                        if ($tmp[$v]['name']==$tmp[$vv]['name']) 
                            {
                         
                                
                                array_splice($tmp,$vv,1);
                                $vv--;
                              

                            /*array_splice($tmp,$i+1,1);$i=0;*/}
                        
                        
                        //else {$i=0;}

                        }
                    }
                        $json[$number]['children']=$tmp;
                        $tmp=array();
                    }

                    $val=$data[$i]['id1'];
                    $json[$i]['name']=$data[$i]['id1']."-".$data[$i]['name1'];
                    $json[$i]['parent']=$data[$i]['par1'];
              
                    $number=$i;

                    $search=$data[$i]['id2'];
                    for ($j=0;$j<count($data);$j++){
                        if ($data[$j]['id1']==$search){
                            $tmp[$count]['name']=$data[$j]['id1']."-".$data[$j]['name1'];
                            $tmp[$count]['parent']=$data[$j]['par1']."-".$data[$i]['name1'];
                            $num=$j;
                            if ($data[$j]['id2']!=NULL){
                              
                                $tmp[$count]['children']='Reserve';
                                $search=$data[$j]['id2'];
                                $num=$j;
                                $j=0;
                            }
                            $count++;
                            array_splice($data,$num,1);

                        }

                    }
                    for ($cnt=$count-1;$cnt>=0;$cnt--){
                        if (isset($tmp[$cnt]['children'])&&$tmp[$cnt]['children']=='Reserve'){
                            $maxx=array();
                            $maxx=$tmp[$cnt+1];
                            $tmp[$cnt]['children']=$maxx;
                         
                            array_splice($tmp,$cnt+1,1);
                            $count--;
                            
                        }
                    }

                }
                else {
                    $search=$data[$i]['id2'];
                    $county=$data[$i]['name1'];
                    $id=$i;
                    for ($j=0;$j<count($data);$j++){
                       
                        if ($data[$j]['id1']==$search){
                           echo "<br>".$county."<br>";
                           print_r($data[$id]);
                            $tmp[$count]['name']=$data[$j]['id1']."-".$data[$j]['name1'];
                            $tmp[$count]['parent']=$data[$j]['par1']."-".$county;
                             $num=$j;

                            if ($data[$j]['id2']!=NULL){
                               
                                $tmp[$count]['children']='Reserve';
                                $search=$data[$j]['id2'];
                                $county=$data[$j]['name1'];
                                $id=$j;
                                //echo $i." ".$j." ".$county."<br>";
                                $j=0;
                            }
                            else if ($data[$j]['id2']==NULL) {
                                $search=$data[$i]['id2'];
                                $county=$i;
                                $id=$i;
                                //echo $i." ".$j." ".$county."<br>";
                                $j=0;
                            }
                            $count++;
                            array_splice($data,$num,1);
                            
                        }

                    }
                    /*echo "<br>";
                    echo $i;*/
                    print_r($tmp);//exit;
                    /*echo "<br>";*/
                    $maxx=array();
                       for ($cnt=$count-1;$cnt>=0;$cnt--){
                        if (isset($tmp[$cnt]['children'])&&$tmp[$cnt]['children']=='Reserve'){
                            
                            
                            array_push($maxx,$tmp[$cnt+1]);
                            $numb=$cnt;
                              echo "<br>";
                    echo $i;
                    print_r($maxx);//exit;
                    echo "<br>";
                            array_splice($tmp,$cnt+1,1);
                            $count--;
                            if ($tmp[$numb]['name']!=$tmp[$numb-1]['name']) {$tmp[$numb]['children']=$maxx;}

                        }
                        
                    } 
                    exit;                  
                }               
            }
         }
         $str='';
         print_r($json);
         foreach ($json as $value){
            $str.=json_encode($value).',';

         }
         return $str;
    }
      public function child_branch($id){
          $query=new Query();
        $query->select (['t1.user_name name1','t1.number_id id1', 't1.parent_id par1','t2.user_name name2','t2.number_id id2'])
              -> from('adjacency_list as t1')
              ->leftJoin('adjacency_list as t2','t2.parent_id=t1.number_id');
        $command = $query->createCommand();
         $data = $command->queryAll();
         $json=array();

         $count=0; $counter=0;
          $tmp=array();
          $du=$this->get($id);
          $parent=$du[0]['parent_id'];
          $user=$du[0]['number_id'];
          for ($i=0;$i<count($data);$i++){
            if ($data[$i]['id1']==$parent&&$data[$i]['id2']==$user){
               $json[$counter]['name']=$data[$i]['id2']."-".$data[$i]['name2'];
               $json[$counter]['parent']=NULL;
               $search=$data[$i]['id2'];
               $tempo=0;
               for ($j=0;$j<count($data);$j++){
                       
                        if ($data[$j]['id1']==$search){

                           
                            $tmp[$count]['name']=$data[$j]['id1']."-".$data[$j]['name1'];
                            $tmp[$count]['parent']=$data[$j]['par1']."-".$data[$i]['name1'];
                             $num=$j;

                            if ($data[$j]['id2']!=NULL){
                               
                                $tmp[$count]['children']='Reserve';
                                $search=$data[$j]['id2'];
                                $j=0;
                            }
                            else if ($data[$j]['id2']==NULL) {
                                $search=$data[$i]['id2'];
                                $j=0;
                            }
                            $count++;
                            array_splice($data,$num,1);
                            
                        }
                        }

                         $maxx=array();
                         $count=count($tmp);

                       for ($cnt=$count-1;$cnt>=0;$cnt--){
                        if (isset($tmp[$cnt]['children'])&&$tmp[$cnt]['children']=='Reserve'){
                          array_push($maxx,$tmp[$cnt+1]);
                            $numb=$cnt;
                          $tmp[$cnt]['children']=$tmp[$cnt+1];
                          array_splice($tmp,$cnt+1,1);
                          if (isset($tmp[$numb-1]['name'])&&$tmp[$numb]['name']!=$tmp[$numb-1]['name']) {$tmp[$numb]['children']=$maxx;$maxx=array();}
                        }
                        
                    } 
                    $count=count($tmp);
                    $b=array();
                    for ($x=0;$x<count($tmp);$x++){
                      $z=$tmp[$x]['children'];
                      if (isset($z[0])) {array_push($b,$z[0]);}
                        else {array_push($b,$z);}
                    }
                    $json[$counter]['children']=$b;

                     


            }
          }
          //print_r($json);exit;
          $str='';
          foreach ($json as $value){
            $str.=json_encode($value).',';

         }
         return $str;
         
       }
       public function child($id){
         $query=new Query();
        $query->select (['t1.user_name name1','t1.number_id id1', 't1.parent_id par1','t2.user_name name2','t2.number_id id2'])
              -> from('adjacency_list as t1')
              ->leftJoin('adjacency_list as t2','t2.parent_id=t1.number_id');
        $command = $query->createCommand();
         $data = $command->queryAll();
         $json=array();

         $count=0; $counter=0;
          $tmp=array();
          $du=$this->get($id);
          $parent=$du[0]['parent_id'];
          $user=$du[0]['number_id'];
          for ($i=0;$i<count($data);$i++){
            if ($data[$i]['id1']==$parent&&$data[$i]['id2']==$user){
               $json[$counter]['name']=$data[$i]['id2']."-".$data[$i]['name2'];
               $json[$counter]['parent']=NULL;
               $search=$data[$i]['id2'];
               $tempo=$i;
               for ($j=0;$j<count($data);$j++){
                       
                        if ($data[$j]['id1']==$search){
                          echo "A";
                           print_r($tempo);echo "<br>";
                            $tmp[$count]['name']=$data[$j]['id1']."-".$data[$j]['name1'];
                            $tmp[$count]['parent']=$data[$j]['par1']."-".$data[$tempo]['name1'];
                             $num=$j;

                            if ($data[$j]['id2']!=NULL){
                               
                               
                                if ($search==$id) {$tmp[$count]['children']='Reserve';$search=$data[$j]['id2'];}
                                else {$search=$data[$i]['id2'];}
                                $tempo=$j;
                                $j=0;
                                

                            }
                            else if ($data[$j]['id2']==NULL) {
                                $search=$data[$i]['id2'];
                                $j=0;
                            }
                            $count++;
                            array_splice($data,$num,1);
                            
                        }
                        }
                        //print_r($tmp);exit;

                         $maxx=array();
                         $count=count($tmp);
                         //print_r($tmp);//exit;

                       for ($cnt=$count-1;$cnt>=0;$cnt--){
                        if (isset($tmp[$cnt]['children'])&&$tmp[$cnt]['children']=='Reserve'){
                          array_push($maxx,$tmp[$cnt+1]);
                            $numb=$cnt;
                          $tmp[$cnt]['children']=$tmp[$cnt+1];
                          array_splice($tmp,$cnt+1,1);
                          if (isset($tmp[$numb-1]['name'])&&$tmp[$numb]['name']!=$tmp[$numb-1]['name']) {$tmp[$numb]['children']=$maxx;$maxx=array();}
                        }
                        
                    } 
                    $count=count($tmp);
                    $b=array();
                    //print_r($tmp);exit;

                    for ($x=0;$x<count($tmp);$x++){
                      $z=$tmp[$x]['children'];
                      if (isset($z[0])) {array_push($b,$z[0]);}
                        else {array_push($b,$z);}
                    }
                    $json[$counter]['children']=$b;

                     


            }
          }
          $str='';
          foreach ($json as $value){
            $str.=json_encode($value).',';

         }
         return $str;
       }
}
