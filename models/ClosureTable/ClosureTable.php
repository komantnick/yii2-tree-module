<?php
//Модель для дерева - Таблица замыканий
namespace app\models\ClosureTable;
use yii\db\ActiveRecord;
use yii\db\Query;
use Yii;
class ClosureTable extends ActiveRecord
{	
	public $node_tree="closure_table_main";//имя таблицы замыканий
	public $node="closure_table";//имя таблицы замыканий
	public $parent_id;//идентификатор родителя
	public function rules()
	{
		return [	       
		[['parent_id'], 'required'],
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
	    	if ($this->parent_id!=0){//есть родитель
	    		$level=Yii::$app->db->createCommand("SELECT level FROM 
	    			".$this->node_tree." WHERE ancestor=".$this->parent_id." 
	    			AND descendant=".$this->parent_id)->queryOne();
	    		$level=$level['level']+1;
	    		$blocks=Yii::$app->db->createCommand("INSERT INTO ".$this->node_tree." 
	    			(ancestor,descendant,level) SELECT ancestor,".$this->node_id."
	    			,level+1 FROM ".$this->node_tree." WHERE descendant=".$this->parent_id."
	    			 UNION ALL SELECT ".$this->node_id.",".$this->node_id.",0")->query();
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
	public function Choose($level){
		
		$result=Yii::$app->db->createCommand("SELECT t.descendant
			FROM node p LEFT JOIN node_tree t ON 
			p.node_id=t.descendant
			WHERE t.level=".$level."")->queryOne();
		return $result['descendant'];

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
	public function selectRand(){
		$result=Yii::$app->db->createCommand("SELECT node_id 
			FROM ".$this->node." ORDER BY RAND() LIMIT 1 ")->queryOne();
		return $result['node_id'];
	}
	//получение поддерева наследников
	public function getChildTree($depth,$root=true){
		//функция возвращает ассоциативный массив - всех потомков текущего узла до глубины $depth
		//получаем свой уровень
		$level=Yii::$app->db->createCommand("SELECT level 
			FROM ".$this->node_tree." WHERE ancestor=".$this->node_id."
			 AND descendant=".$this->node_id)->queryOne();	 
		//выясняем до какого уровня брать   		
		$level=$level['level']+$depth;
		$till=$level-1;
		if ($root){
			$result=Yii::$app->db->createCommand("SELECT ancestor,descendant,
				level,node_title,node_status FROM ".$this->node_tree.",
				closure_table WHERE ancestor=".$this->node_id." AND level>=".$till."
				 AND level<".$level." AND node_id=descendant")->queryAll();
		}
		else{
			$result=Yii::$app->db->createCommand("SELECT ancestor,descendant,level,
				node_title,node_status FROM ".$this->node_tree.",closure_table WHERE 
				ancestor=".$this->node_id." AND level>=".$till." AND 
				level<".$level." AND node_id=descendant AND 
				descendant<>".$this->node_id)->queryAll();			
		}
		if (count($result)==0) $result=false;
		return $result;
	}
	public static function maxDepth(){
		//функция возвращает максимальную глубину дерева
		$level=Yii::$app->db->createCommand("SELECT MAX(level) 
			AS maxlevel FROM ".$this->node_tree)->queryOne();
		return $level['maxlevel'];
	}
	public function getJsonTree(){
		//функция возвращает дерево в формате json. Родителем текущий объект
		//!!!для теста получим два уровня - корень и следующий
		//создаём корень
		$a["name"]=$this->node_title;
		$a["status"]=$this->node_status;
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
		
		$node=new ClosureTable();
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
				$inner_array[]=self::recoursiveTree($item["descendant"],false);
			}
			$a["children"]=$inner_array;
		}
		print_r($a);
		exit;
	}
	public function formJsonFile(){

		$tree=self::recoursiveTree($this->node_id,true);
		$file=fopen($_SERVER['DOCUMENT_ROOT'].
			'/yii2-tree-module/web/d3/flare.json', 'w');
		fwrite($file, json_encode($tree));
	}
	public function getParentTree($depth,$root=true){
		$level=Yii::$app->db->createCommand("SELECT level 
			FROM ".$this->node_tree." WHERE ancestor=
			".$this->node_id." AND descendant=".$this->node_id)->queryOne();	 
		//выясняем до какого уровня брать   		
		$level=$level['level']+1;
		$till=$level+1;
		if ($root){
			$result=Yii::$app->db->createCommand("SELECT * FROM ".$this->node_tree."
				,closure_table WHERE descendant=".$this->node_id." AND level<".$till." 
				AND level>=".$level." AND node_id=ancestor")->queryAll();
		}
		else{
			$result=Yii::$app->db->createCommand("SELECT * FROM ".$this->node_tree.",
				closure_table WHERE descendant=".$this->node_id." AND level<".$till." 
				AND level>=".$level." AND node_id=ancestor AND ancestor<>".$this->node_id."")->queryAll();		
		}
		if (count($result)==0) $result=false;
		return $result;
	}
	public static function recoursiveParentTree($node_id,$root=false){
		
		$node=new ClosureTable();
		$node=$node->findOne($node_id);
		$a["name"]=$node->node_title;
		$a["status"]=$node->node_status;
		$tree=$node->getParentTree(2,$root);
		if (!$tree){
			return $a;
		}
		else{
			$inner_array=array();

			foreach ($tree as $item){
				$inner_array[]=self::recoursiveParentTree
				($item["ancestor"],false);
			}
			$a["children"]=$inner_array;
		}
		return $a;
	}
	public function formJsonParentFile(){

		$tree=self::recoursiveParentTree($this->node_id,true);
		$file=fopen($_SERVER['DOCUMENT_ROOT'].'/yii2-tree-module/web/d3/flare_parent.json', 'w');
		fwrite($file, json_encode($tree));
	}
	public function move($id_from,$id_to){
		$delete=Yii::$app->db->createCommand("DELETE FROM ".$this->node_tree." 
			WHERE descendant IN(SELECT * FROM(SELECT descendant FROM
			 ".$this->node_tree." WHERE ancestor=".$id_from.") t) AND 
			 ancestor IN (SELECT * FROM(SELECT ancestor FROM 
			 ".$this->node_tree." WHERE descendant=".$id_from."
			  AND ancestor!=descendant ) t) ")->query();
		$insert=Yii::$app->db->createCommand("INSERT INTO ".$this->node_tree."
			(descendant,ancestor,level) SELECT suptree.descendant,
			subtree.ancestor,suptree.level+subtree.level+1 FROM 
			".$this->node_tree." AS subtree CROSS JOIN ".$this->node_tree."
			 AS suptree WHERE subtree.descendant=".$id_to." AND
			  suptree.ancestor=".$id_from."")->query();
	}
	//количество узлов в дереве
	public function getNumber(){
		$query=new Query();
		$query->select (['COUNT(*) AS cnt'])
		-> from($this->node_tree_info);
		$command = $query->createCommand();
		$data = $command->queryAll();
		return $data[0]['cnt'];        
	}
	public function deleteNode($id){
		 $node=new ClosureTable();
        $node=$node->findOne($id);
		$query_a= new Query();
		$query_a -> createCommand()
		-> delete($this->node,['node_id'=>$id])
		-> execute();
		$d=Yii::$app->db->createCommand("SELECT ancestor FROM ".$this->node_tree." 
			WHERE level=1 AND descendant=".$id."
				")->queryOne();	
		$qu=$d['ancestor'];
		$z=new ClosureTable();
		$m=Yii::$app->db->createCommand("SELECT descendant FROM ".$this->node_tree." 
			WHERE ancestor=".$id."
				")->queryAll();	
		foreach ($m as $value){
			$z=new ClosureTable();
			$z->move($value['descendant'],$qu);
		}
		$query_b=new Query();
		$query_d= Yii::$app->db->createCommand("DELETE FROM $this->node_tree
		 WHERE ancestor=".$id."")->query(); 
		$query_f= Yii::$app->db->createCommand("DELETE FROM $this->node_tree
		 WHERE descendant=".$id."")->query(); 
		
		return 'Узел удален';

	}
	public function block($id){
		$connection=\Yii::$app->db;
		$connection->createCommand()
		->update('closure_table',[
			'node_status'=>0,
			],'node_id='.$id.'')
		->execute();
		return 'Узел заблокирован';

	}
	public function unblock($id){
		$connection=\Yii::$app->db;
		$connection->createCommand()
		->update('closure_table',[
			'node_status'=>1,
			],'node_id='.$id.'')
		->execute();
		return 'Узел разблокирован';

	}
    public function getLevel($user_id){
    	$query=new Query();
    	$query->select (['level'])
    	-> from($this->node_tree)
    	->where(['user_id'=>$user_id,'parent_id'=>$user_id]);
    	$command = $query->createCommand();
    	$data = $command->queryAll();
    	
    	if (isset($data[0]['level']))
    		{$x=$data[0]['level'];return $x+1;}
    	else {return 0;}
     }
 }

