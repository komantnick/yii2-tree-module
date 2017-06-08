<?php
namespace tests\models;
use app\models\MaterializedPath\MaterializedPath;
class MaterializedPathTest extends \Codeception\Test\Unit
{

    use \Codeception\Specify;
    private $model;
    /**
     * @var \UnitTester
     */
    public $tester;
    protected function _before()
    {
    }
    protected function _after()
    {
    }
      public function testTrue()
    {
        $this->assertTrue(true);
    }
  public function testGetParentsTree()
    {
        for ($i=1;$i<=100;$i++){
            $node=new MaterializedPath();
            $rand=$node->selectRand();
            $node=MaterializedPath::findOne($rand);
     $z=$node->recoursiveParentTree($node['node_id'],true);
     $k=json_encode($z);
        }   
    }
    //получение подветки где корень-данный узел
    public function testGetChildrenTree()
    {
         for ($i=1;$i<=100;$i++){
            $node=new MaterializedPath();
            $rand=$node->selectRand();
            $node=MaterializedPath::findOne($rand);
     $z=$node->recoursiveTree($node['node_id'],true);
     $k=json_encode($z);
         }
     //
    }
    public function testGetParent(){
        for ($i=1;$i<=100;$i++){
            $node=new MaterializedPath();
            $rand=$node->selectRand();
             $node=MaterializedPath::findOne($rand);
     $z=$node->getParentTree($node['node_id'],true);
     $k=json_encode($z);
         }
     
    }
     public function testGetChildren()
    {
        for ($i=1;$i<=100;$i++){
            $node=new MaterializedPath();
            $rand=$node->selectRand();
             $node=MaterializedPath::findOne($rand);
     $z=$node->getChildTree($node['node_id'],true);
     $k=json_encode($z);
         }
     //$node=new AdjacencyList();
    
    }
    public function testAddNodes(){
        for ($i=1;$i<=100;$i++){
            $node=new MaterializedPath();
            $z=rand(1,4);
            $k=$node->Choose($z);
        if (isset($z)) {$node->node_path=$node->getpath($z).'/';}
        else {$node->node_path='/';}
        $node->node_title=$node->generateName(8);
        $node->save();
        }
    }
    public function testAddLevel3(){
        $node=new MaterializedPath();
        $anc=$node->Choose(3);
        for ($i=1;$i<=100;$i++){
             $node=new MaterializedPath();
            $z=rand(1,4);
            $k=$node->Choose($z);
        if (isset($z)) {$node->node_path=$node->getpath($z).'/';}
        else {$node->node_path='/';}
            $node->node_title=$node->generateName(8);
            $node->save();
        }
    }
    public function testAddLevel5(){
        $node=new MaterializedPath();
        $anc=$node->Choose(5);
        for ($i=1;$i<=100;$i++){
             $node=new MaterializedPath();
            $z=rand(1,4);
            $k=$node->Choose($z);
        if (isset($z)) {$node->node_path=$node->getpath($z).'/';}
        else {$node->node_path='/';}
            $node->node_title=$node->generateName(8);
            $node->save();
        }
    }
    public function testMoveNode(){
        $node=new MaterializedPath();
        $id=$node->selectRand();
        for ($i=1;$i<=100;$i++){
            $node=new MaterializedPath();
            $rand=$node->selectRand();
            $node->move($rand,$id);
        }
    }
        public function testBlockNode(){
            $node=new MaterializedPath();
            for ($i=1;$i<=100;$i++){
            $node=new MaterializedPath();
            $rand=$node->selectRand();
            $node->block($rand);
        }
    }*/
         public function testDeleteNode(){
            $node=new MaterializedPath();
            for ($i=1;$i<=100;$i++){
            $node=new MaterializedPath();
            $rand=$node->selectRand();
             $z=\app\models\MaterializedPath\MaterializedPath::
             deleteNode($rand);
        }
        }       
}