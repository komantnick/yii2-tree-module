<?php
//Модель для дерева - Таблица замыканий
namespace app\models\MaterializedPath;
use yii\db\ActiveRecord;
use Yii;
class MaterializedPath extends ActiveRecord
{	
	public $node_tree="materialized_path";//имя таблицы
	public $parent_id;//идентификатор родителя
	public function rules()
	{
	    return [	       
	    	[['node_path'], 'required'],
	    	[['node_title'],'default','value'=>''],	        
	    ];
	}
    //функция для приведения к состоянию zerofill(7);
    
    public function afterSave($insert, $changedAttributes){
		/*
			После сохранения наполняем таблицу	
		*/
    	parent::afterSave($insert, $changedAttributes);
     	try{
	     	//этот код для сохранения нового!!! 
	     	$transaction = Yii::$app->db->beginTransaction();
            $path=$this->node_path.$this->addNulls($this->node_id);
            $connection=\Yii::$app->db;
            $connection->createCommand()
                   ->update('materialized_path',[
                    'node_path'=>$path,
                    ],'node_id='.$this->node_id.'')
                   ->execute();
	     	$transaction->commit();
    	}
    	catch(Exception $e){
    		//ловим исключение
    	}

	 }
     public function Choose($level){
        
        $result=Yii::$app->db->createCommand("SELECT node_id,node_path FROM materialized_path where (LENGTH(node_path) 
- LENGTH(REPLACE(node_path, '/', ''))) / LENGTH('/')=2 ORDER BY RAND() LIMIT 1")->queryOne();
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
public function selectRand(){
    $result=Yii::$app->db->createCommand("SELECT node_id FROM ".$this->node_tree." ORDER BY RAND() LIMIT 1 ")->queryOne();
    return $result['node_id'];
   }
        public function block($node_id){
        $connection=\Yii::$app->db;
        $connection->createCommand()
                   ->update('materialized_path',[
                    'node_status'=>0,
                    ],'node_id='.$node_id.'')
                   ->execute();
        return 'Узел заблокирован!';
    }
    public function unblock($node_id){
        $connection=\Yii::$app->db;
        $connection->createCommand()
                   ->update('materialized_path',[
                    'node_status'=>1,
                    ],'node_id='.$node_id.'')
                   ->execute();
        return 'Узел разблокирован!';
    }
    public function move($user_id,$user_id_to){
        $connection=\Yii::$app->db;
        $x=$this->getPath($user_id);
        $y=$this->getPath($user_id_to).'/'.$user_id;
        $z=$x.'%';
        $x='"'.$x.'"';
        $y='"'.$y.'"';
        $z='"'.$z.'"';
        //echo "A";exit;
        $query=\Yii::$app->db->createCommand("UPDATE ".$this->node_tree." SET node_path=REPLACE(node_path, $x, $y) WHERE node_path LIKE $z")->execute();
        return 'Узел перемещен!';

    }
	 public function addNulls($id){
    	return str_pad($id,7,"0",STR_PAD_LEFT);;
    }
    public function getPath($id){
    	$path=Yii::$app->db->createCommand("SELECT node_path FROM ".$this->node_tree." WHERE node_id=".$id."")->queryOne();
    	return $path['node_path'];
    }
    public function deleteNode($id){
        $node=new MaterializedPath();
        $c=$node->addNulls($id);
            $node=$node->findOne($id);
        $d=Yii::$app->db->createCommand("SELECT node_path
            FROM ".$this->node_tree." 
            WHERE node_id=".$c."
                ")->queryOne(); 
        $x=$d['node_path'];

        $z= explode("/", $x);
        $y="";
        for ($i=1;$i<count($z)-1;$i++){
            $y.="/".$z[$i];
        }
        
        $z=$x.'%';
        $x='"'.$x.'"';
        $y='"'.$y.'"';
        $z='"'.$z.'"';
        //$node->delete();
        //print_r($str);exit;
         $query=\Yii::$app->db->createCommand("UPDATE ".$this->node_tree."
          SET node_path=REPLACE(node_path, $x,$y)
           WHERE node_path LIKE $z")->execute();

        return 'Узел удален';

    }
    //нерекурсивный способ
    public function getTree($id){
     $path=$this->getPath($id);
     $path='"'.$path.'%"';
     $query=Yii::$app->db->createCommand("SELECT * 
            FROM ".$this->node_tree."
            WHERE 
                node_path LIKE $path
            ORDER BY node_path")->query();
    
    $html="";
            foreach ($query as $row){
                $x=substr_count($row['node_path'],'/');
                $html.=str_repeat("- ".str_repeat(" ",$x-1),$x).ltrim($row['node_id'],'0').". ".$row['node_title']."<br>";
            }
            return $html;
        }
         public function getChildTree($depth,$root=true){
             $path=$this->getPath($this->node_id);
     $path='"'.$path.'/%"';
     $k='"'.'/%"';
        //функция возвращает ассоциативный массив - всех потомков текущего узла до глубины $depth
        //получаем свой уровень
        //выясняем до какого уровня брать           
        if ($root){
            $result=Yii::$app->db->createCommand("SELECT node_id,node_title,node_path,node_status FROM ".$this->node_tree." WHERE node_path LIKE $path AND node_path NOT LIKE CONCAT($path,$k)")->queryAll();
        }
        else{
            $result=Yii::$app->db->createCommand("SELECT node_id,node_title,node_path,node_status FROM  ".$this->node_tree." WHERE node_path LIKE $path AND node_path NOT LIKE CONCAT($path,$k) AND node_id<>".$this->node_id)->queryAll();          
        }
        //echo "A";exit;
        if (count($result)==0) $result=false;
        return $result;
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
        
        $node=new MaterializedPath();
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
        $file=fopen($_SERVER['DOCUMENT_ROOT'].'/yii2-tree-module/web/d3/mpath_child.json', 'w');
        fwrite($file, json_encode($tree));
    }
    public function getParentTree($depth,$root=true){
         $path=$this->getPath($this->node_id);
    $path='"'.$path.'"';
     $z='"%"';
     $count=mb_substr_count($path,'/');
        //функция возвращает ассоциативный массив - всех потомков текущего узла до глубины $depth
        //получаем свой уровень
        //выясняем до какого уровня брать           
        if ($root){
            $result=Yii::$app->db->createCommand("SELECT node_id,node_title,node_path,node_status FROM ".$this->node_tree." WHERE $path LIKE CONCAT(node_path,$z) AND node_id<>".$this->node_id."  ORDER BY node_path DESC LIMIT 1")->queryAll();
        }
        else{
            $result=Yii::$app->db->createCommand("SELECT node_id,node_title,node_path,node_status FROM  ".$this->node_tree." WHERE $path LIKE CONCAT(node_path,$z) AND node_id<>".$this->node_id." ORDER BY node_path DESC LIMIT 1 ")->queryAll();          
        }
        //echo "A";exit;
        if (count($result)==0) $result=false;
        return $result;
    }
    public function getPTree($id){
        //mb_substr_count
        $path=$this->getPath($id);
     $path='"'.$path.'"';
     $z='"%"';
     $query=Yii::$app->db->createCommand("SELECT * 
            FROM ".$this->node_tree."
           WHERE ".$path." LIKE
            CONCAT(node_path,$z) ORDER BY node_path")->query();
            $html="";
            foreach ($query as $row){
                $x=substr_count($row['node_path'],'/');
                $html.=str_repeat("- ".str_repeat(" ",$x-1),$x).ltrim($row['node_id'],'0').". ".$row['node_title']."<br>";
            }
            return $html;
    }
    public static function recoursiveParentTree($node_id,$root=false){
        
        $node=new MaterializedPath();
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
                print_r($item["node_id"]);
                $inner_array[]=self::recoursiveParentTree($item["node_id"],false);
                //print_r($inner_array); echo "<br/>";
            }
            $a["children"]=$inner_array;
        }
        //exit;
        return $a;
    }
    public function formJsonParentFile(){

        $tree=self::recoursiveParentTree($this->node_id,true);
        $file=fopen($_SERVER['DOCUMENT_ROOT'].'/yii2-tree-module/web/d3/mpath_parents.json', 'w');
        fwrite($file, json_encode($tree));
    }
}