<?php
//Модель для дерева - Список смежности
namespace app\models\AdjacencyList;
use yii\db\ActiveRecord;
use Yii;
class AdjacencyList extends ActiveRecord
{
public $table="adjacency_list";//имя таблицы
public $parent_id;//идентификатор родителя
public function rules()
{
return [
[['node_ancestor'], 'required'],
[['node_title'],'default','value'=>''],
];
}
public function block($node_id){
$connection=\Yii::$app->db;
$connection->createCommand()
->update('adjacency_list',[
'node_status'=>0,
],'node_id='.$node_id.'')
->execute();
return 'Узел заблокирован!';
}
public function unblock($node_id){
$connection=\Yii::$app->db;
$connection->createCommand()
->update('adjacency_list',[
'node_status'=>1,
],'node_id='.$node_id.'')
->execute();
return 'Узел разблокирован!';
}
public function move($user_id,$user_id_to){
$connection=\Yii::$app->db;
$connection->createCommand()
->update('adjacency_list',[
'node_ancestor'=>$user_id_to,
],'node_id='.$user_id.'')
->execute();
return 'Узел перемещен!';

}
public function Choose($level){
$sql="";
if ($level==1){
$sql="SELECT node_id FROM
adjacency_list WHERE node_ancestor=0";
}
else {
for ($i=1;$i<$level;$i++){
$j=$i+1;
$c="c".$j;
$c2="c".$i;

$sql.=" LEFT JOIN adjacency_list ".$c."
ON ".$c.".node_ancestor=".$c2.".node_id";
}
//print_r($c);
$sql_start="SELECT ".$c.".node_id from adjacency_list
c1 ";
$sql=$sql_start.$sql." WHERE c1.node_ancestor=0
AND ".$c.".node_id>0 ORDER BY RAND() LIMIT 1";
}

//print_r($sql);exit;
$result=Yii::$app->db->createCommand($sql)->queryOne();
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
$result=Yii::$app->db->createCommand("SELECT node_id
FROM ".$this->table." ORDER BY RAND() LIMIT 1 ")->queryOne();
return $result['node_id'];
}
public function getChildTree($depth,$root=true){
//функция возвращает ассоциативный массив - всех потомков текущего узла до глубины $depth
//получаем свой уровень
//выясняем до какого уровня брать
if ($root){
$result=Yii::$app->db->createCommand("SELECT node_id,node_title,
node_ancestor,node_status FROM ".$this->table."
WHERE node_ancestor=".$this->node_id."")->queryAll();
}
else{
$result=Yii::$app->db->createCommand("SELECT node_id,node_title,
node_ancestor,node_status FROM ".$this->table." WHERE
node_ancestor=".$this->node_id."
AND node_id<>".$this->node_id)->queryAll();
}
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
public function deleteNode($id){
$node=new AdjacencyList();
$node=$node->findOne($id);
$d=Yii::$app->db->createCommand("SELECT node_ancestor
FROM ".$this->table."
WHERE node_id=".$id."
")->queryOne();
$qu=$d['node_ancestor'];
$node->delete();
$f=Yii::$app->db->createCommand("UPDATE  ".$this->table."
SET node_ancestor=".$qu." WHERE node_ancestor=".$id."
")->execute();
return 'Узел удален';

}
public static function recoursiveTree($node_id,$root=false){

$node=new AdjacencyList();
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
$file=fopen($_SERVER['DOCUMENT_ROOT'].
'/yii2-tree-module/web/d3/alist-tree.json', 'w');
fwrite($file, json_encode($tree));
}
public function getParentTree($depth,$root=true){
//функция возвращает ассоциативный массив - всех потомков текущего узла до глубины $depth
//получаем свой уровень
//выясняем до какого уровня брать
if ($root){
$result=Yii::$app->db->createCommand("SELECT node_id,node_title,
node_ancestor,node_status FROM ".$this->table." WHERE node_id=".$this->node_id." AND node_ancestor<>0")->queryAll();
}
else{
$result=Yii::$app->db->createCommand("SELECT node_id,node_title,
node_ancestor,node_status FROM ".$this->table."
WHERE node_id=".$this->node_id." AND
node_ancestor<>".$this->node_id." AND node_ancestor<>0")->queryAll();
}
if (count($result)==0) $result=false;
return $result;
}
public static function recoursiveParentTree($node_id,$root=false){
$node=new AdjacencyList();
$node=$node->findOne($node_id);
$a["name"]=$node->node_title;
$a["status"]=$node->node_status;
print_r($a);//exit;
$tree=$node->getParentTree(2,$root);
if (!$tree){
return $a;
}
else{
$inner_array=array();
foreach ($tree as $item){
//print_r($item["node_ancestor"]);
$inner_array[]=self::recoursiveParentTree
($item["node_ancestor"],false);
//print_r($inner_array); echo "<br/>";
}
$a["children"]=$inner_array;
}
return $a;
}
public function formJsonParentFile(){

$tree=self::recoursiveParentTree($this->node_id,true);
$file=fopen($_SERVER['DOCUMENT_ROOT'].
'/yii2-tree-module/web/d3/alist_parent.json', 'w');
fwrite($file, json_encode($tree));
}
}
