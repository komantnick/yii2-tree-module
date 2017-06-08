<?php
//Модель для дерева - Вложенные множества
namespace app\models\NestedSets;
use yii\db\ActiveRecord;
use Yii;
class NestedSets extends ActiveRecord
{	
	public $table="nested_sets";//имя таблицы
	public $parent_id;//идентификатор родителя
	public function rules()
	{
	    return [	       
	    	[['node_title'],'default','value'=>''],	        
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
           $model3=new \app\models\NestedSets();
           if (isset($this->level)){
           	$model3=$model3->findOne($this->level);
           	$right = $model3['right_number'];
		    $level = $model3['level']; 
		     $update=\Yii::$app->db->createCommand("UPDATE ".$this->table."
			SET right_number = right_number + 2, 
			left_number = IF(left_number > ".$right.", left_number + 2, left_number) 
			WHERE right_number >= ".$right."")->execute();
	       }
           else {
           	$level=0; 
           	$right=Yii::$app->db->createCommand("SELECT right_number FROM ".$this->table." WHERE node_id=".$this->node_id." ORDER BY right_number DESC LIMIT 1")->queryOne();
           	$right=$right['right_number']+1;
           }
              $entry=\Yii::$app->db->createCommand("UPDATE ".$this->table."
			SET left_number = ".$right.", right_number = ".$right." + 1, 
			level = ".$level." + 1
			WHERE node_id= ".$this->node_id." ")->execute();
	     	$transaction->commit();
    	}
    	catch(Exception $e){
    		//ловим исключение
    	}
	 }
	 //удаление узла(или подветки из данного узла)
	 public function deleteNode($node_id){
	 	 $model=new \app\models\NestedSets();
	 	 	$model=$model->findOne($node_id);
	 	 	$left  = $model['left_number'];
           	$right = $model['right_number'];
		    $level = $model['level'];
		    $delete= \Yii::$app->db->createCommand("
			DELETE 
			FROM ".$this->table." 
			WHERE left_number >= ".$left." AND 
			right_number <= ".$right." ")->execute();

			$update=\Yii::$app->db->createCommand("UPDATE ".$this->table."
			SET 
			left_number = IF(left_number > ".$left.", left_number - (".$right." - ".$left." + 1), left_number), 
			right_number = right_number - (".$right." - ".$left." + 1) 
			WHERE right_number > ".$right."")->execute();

	 }
	 public function block($node_id){
        $connection=\Yii::$app->db;
        $connection->createCommand()
                   ->update('nested_sets',[
                    'node_status'=>0,
                    ],'node_id='.$node_id.'')
                   ->execute();
        return 'Узел заблокирован!';
    }
    public function unblock($node_id){
        $connection=\Yii::$app->db;
        $connection->createCommand()
                   ->update('nested_sets',[
                    'node_status'=>1,
                    ],'node_id='.$node_id.'')
                   ->execute();
        return 'Узел разблокирован!';
    }
    public function Choose($level){
        
        $result=Yii::$app->db->createCommand("SELECT node_id FROM nested_sets WHERE level=".$level." ORDER BY RAND() LIMIT 1")->queryOne();
        return $result['node_id'];

    }
    public function selectRand(){
    $result=Yii::$app->db->createCommand("SELECT node_id FROM ".$this->table." ORDER BY RAND() LIMIT 1 ")->queryOne();
    return $result['node_id'];
   }
        public function generateName($length){
  $chars = 'abdefhiknrstyzABDEFGHKNQRSTYZ23456789';
  $numChars = strlen($chars);
  $string = '';
  for ($i = 0; $i < $length; $i++) {
    $string .= substr($chars, rand(1, $numChars) - 1, 1);
  }
  return $string;
}
	    public function move($node_id,$node_id_to){
	    	$model=new \app\models\NestedSets();
	 	 	$node=$model->findOne($node_id);
	 	 	$model_to=new \app\models\NestedSets();
	 	 	$node_to=$model_to->findOne($node_id_to);
            $left 	= $node['left_number'];
		    $right 	= $node['right_number'];
		    $level 	= $node['level'];
		    $level_up = $node_to['level'];
		    $query_a=Yii::$app->db->createCommand("SELECT (right_number- 1) AS right_number FROM ".$this->table." WHERE node_id = ".$node_id_to."")->queryOne();
		    $right_near=$query_a['right_number'];
		    $skew_level = $level_up - $level + 1;
		$skew_tree = $right- $left + 1;	
		$query_b=Yii::$app->db->createCommand("SELECT node_id FROM ".$this->table." WHERE left_number >= ".$left." AND right_number <= ".$right."")->query();
		$id_edit = [];
		foreach ($query_b as $value){
			$id_edit[] = $value['node_id'];
		}
		$id_edit = implode(', ', $id_edit);
		if($right_near < $right) {
			//вышестоящие
			$skew_edit = $right_near - $left + 1;
			$sql[0] = "
				UPDATE ".$this->table."
				SET right_number = right_number + ".$skew_tree." 
				WHERE 
					right_number < ".$left." AND 
					right_number > ".$right_near."";
			$sql[1] = "
				UPDATE ".$this->table."
				SET left_number = left_number + ".$skew_tree."
				WHERE 
					left_number < ".$left." AND 
					left_number > ".$right_near."";
			$sql[2] = "
				UPDATE ".$this->table."
				SET left_number = left_number + ".$skew_edit.", 
					right_number = right_number + ".$skew_edit.", 
					level = level + ".$skew_level."
				WHERE node_id IN (".$id_edit.")";
			
		} else {
			//нижестоящие
			$skew_edit = $right_near - $left +1 - $skew_tree;
			
			$sql[0] = "
				UPDATE ".$this->table."
				SET right_number = right_number - ".$skew_tree." 
				WHERE 
					right_number > ".$right." AND 
					right_number <= ".$right_near."";
				
			$sql[1] = "
				UPDATE ".$this->table."
				SET left_number = left_number - ".$skew_tree."
				WHERE 
					left_number > ".$right." AND 
					left_number <= ".$right_near."";
				
			$sql[2] = "
				UPDATE ".$this->table."
				SET left_number = left_number + ".$skew_edit.", 
					right_number = right_number + ".$skew_edit.", 
					level = level + ".$skew_level." 
				WHERE node_id IN (".$id_edit.")";
		}
		$query_x=Yii::$app->db->createCommand($sql[0])->query();
		$query_y=Yii::$app->db->createCommand($sql[1])->query();
		$query_z=Yii::$app->db->createCommand($sql[2])->query();
	    }
	    public function getTree($id){
	    	$model=new \app\models\NestedSets();
	 	 	$node=$model->findOne($id);
            $left 	= $node['left_number'];
		    $right 	= $node['right_number'];
	    	$sql = $query=Yii::$app->db->createCommand("
			SELECT node_id, node_title, level 
			FROM ".$this->table."
			WHERE 
				left_number >= ".$left." AND 
				right_number <= ".$right."
			ORDER BY left_number")->query();
			$html="";
    		foreach ($sql as $row){
    			$x=$row['level'];
    			$html.=str_repeat("- ".str_repeat(" ",$x-1),$x).$row['node_id'].". ".$row['node_title']."<br>";
    		}
    		return $html;
	    }
	    public function getChildTree($depth,$root=true){
	    	$model=new \app\models\NestedSets();
	 	 	$node=$model->findOne($this->node_id);
		    $left = $node['left_number'];
		    $right = $node['right_number'];
		    $level = $node['level'] + 1;
	    	if ($root){
            $result=Yii::$app->db->createCommand("SELECT node_id,node_title,node_status 
			FROM ".$this->table."
			WHERE 
				left_number >= ".$left." AND 
				right_number <= ".$right." AND
				level <= ".$level." AND level>".$level."-1 
			ORDER BY left_number")->queryAll();
        }
        else{
            $result=Yii::$app->db->createCommand("SELECT node_id,node_title,node_status 
			FROM ".$this->table."
			WHERE 
				left_number >= ".$left." AND 
				right_number <= ".$right." AND
				level <= ".$level." AND level>".$level."-1 
			ORDER BY left_number AND node_id<>".$this->node_id)->queryAll();          
        }
        if (count($result)==0) $result=false;
        return $result;
	    }
	    public function getJsonTree(){
	    	 $a["name"]=$this->node_title;
        $tree=$this->getChildTree(2);           
        $inner_array=array();       
        foreach ($tree as $item){         
            array_push($inner_array,array("name"=>$item['node_title'],"status"=>$item['node_status']));
        }
        $a["children"]=$inner_array;
        return $a;
	    }
	    public static function recoursiveTree($node_id,$root=false){
	    	  $node=new NestedSets();
        $node=$node->findOne($node_id);
        $a["name"]=$node->node_title;
         $a["status"]=$node->node_status;
        $tree=$node->getChildTree(2,$root);
        if (!$tree){
            return $a;
        }
        else{
            $inner_array=array();
            foreach ($tree as $item){
                $inner_array[]=self::recoursiveTree($item["node_id"],false);
            }
            $a["children"]=$inner_array;
        }
        return $a;
        }
        public function formJsonFile(){
        	   $tree=self::recoursiveTree($this->node_id,true);
        $file=fopen($_SERVER['DOCUMENT_ROOT'].'/yii2-tree-module/web/d3/sets-child.json', 'w');
        fwrite($file, json_encode($tree));
        }
        public function getParentTree($depth,$root=true){
        	$model=new \app\models\NestedSets();
	 	 	$node=$model->findOne($this->node_id);
            $left 	= $node['left_number'];
		    $right 	= $node['right_number'];
		    $level= $node['level'];
        	 if ($root){
            $result=Yii::$app->db->createCommand("SELECT node_id,node_title, node_status 
			FROM ".$this->table."
			WHERE 
				left_number <= ".$left." AND 
				right_number >= ".$right." AND
				level< ".$level." AND level >=".$level." -1 
			ORDER BY left_number DESC LIMIT 1")->queryAll();
        }
        else{
            $result=Yii::$app->db->createCommand("SELECT node_id,node_title, node_status
			FROM ".$this->table."
			WHERE 
				left_number <= ".$left." AND 
				right_number >= ".$right." AND
				level< ".$level." AND level >=".$level." -1 
			ORDER BY left_number DESC LIMIT 1 ")->queryAll();          
        }
        if (count($result)==0) $result=false;
        return $result;
        }
        public function getPTree($id){
        	$model=new \app\models\NestedSets();
	 	 	$node=$model->findOne($id);
            $left 	= $node['left_number'];
		    $right 	= $node['right_number'];
	    	$sql = $query=Yii::$app->db->createCommand("
			SELECT node_id,node_title, level 
			FROM ".$this->table."
			WHERE 
				left_number <= ".$left." AND 
				right_number >= ".$right."
			ORDER BY left_number")->query();
			$html="";
			foreach ($sql as $row){
    			$x=$row['level'];
    			$html.=str_repeat("- ".str_repeat(" ",$x-1),$x).$row['node_id'].". ".$row['node_title']."<br>";
    		}
    		return $html;
        }
        public static function recoursiveParentTree($node_id,$root=false){
        $node=new NestedSets();
        $node=$node->findOne($node_id);
        $a["name"]=$node->node_title;
        $a['status']=$node->node_status;
        $tree=$node->getParentTree(2,$root);
        if (!$tree){
            return $a;
        }
        else{
            $inner_array=array();
            foreach ($tree as $item){
                print_r($item["node_id"]);
                $inner_array[]=self::recoursiveParentTree($item["node_id"],false);
            }
            $a["children"]=$inner_array;
        }
        return $a;
        }
        public function formJsonParentFile(){
        $tree=self::recoursiveParentTree($this->node_id,true);
        $file=fopen($_SERVER['DOCUMENT_ROOT'].'/yii2-tree-module/web/d3/sets-parent.json', 'w');
        fwrite($file, json_encode($tree));
    }
}