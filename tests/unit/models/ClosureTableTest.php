<?php
namespace tests\models;
use app\models\ClosureTable;
class ClosureTableTest extends \Codeception\Test\Unit
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
     public function testGetParentsTree()
    {
        for ($i=1;$i<=100;$i++){
            $node=new Node();
            $rand=$node->selectRand();
            $node=Node::findOne($rand);
     $z=$node->recoursiveParentTree($node['node_id'],true);
     $k=json_encode($z);
        }   
    }
    //получение подветки где корень-данный узел
    public function testGetChildrenTree()
    {
         for ($i=1;$i<=100;$i++){
            $node=new Node();
            $rand=$node->selectRand();
            $node=Node::findOne($rand);
     $z=$node->recoursiveTree($node['node_id'],true);
     $k=json_encode($z);
         }
     //
    }
    public function testGetParent(){
        for ($i=1;$i<=100;$i++){
            $node=new Node();
            $rand=$node->selectRand();
             $node=Node::findOne($rand);
     $z=$node->getParentTree($node['node_id'],true);
     $k=json_encode($z);
         }
     
    }
     public function testGetChildren()
    {
        for ($i=1;$i<=100;$i++){
            $node=new Node();
            $rand=$node->selectRand();
             $node=Node::findOne($rand);
     $z=$node->getChildTree($node['node_id'],true);
     $k=json_encode($z);
         }
    }
    public function testAddNodes(){
        for ($i=1;$i<=100;$i++){
            $node=new Node();
            $z=rand(1,4);
        $node->parent_id=$node->Choose($z);
        $node->node_title=$node->generateName(8);
        $node->save();
        }
    }
    public function testAddLevel3(){
        $node=new Node();
        $anc=$node->Choose(3);
        for ($i=1;$i<=100;$i++){
            $node=new Node();
            $node->parent_id=$anc;
            $node->node_title=$node->generateName(8);
            $node->save();
        }
    }
    public function testAddLevel5(){
        $node=new Node();
        $anc=$node->Choose(5);
        for ($i=1;$i<=100;$i++){
            $node=new Node();
            $node->parent_id=$anc;
            $node->node_title=$node->generateName(8);
            $node->save();
        }
    }
    public function testMoveNode(){
        $node=new Node();
        $id=$node->selectRand();
        for ($i=1;$i<=100;$i++){
            $node=new Node();
            $rand=$node->selectRand();
            $node->move($rand,$id);
        }
    }
        public function testBlockNode(){
            $node=new Node();
            for ($i=1;$i<=100;$i++){
            $node=new Node();
            $rand=$node->selectRand();
            $node->block($rand);
        }
    }
         public function testDeleteNode(){
            $node=new Node();
            for ($i=1;$i<=100;$i++){
            $node=new Node();
            $rand=$node->selectRand();
             $z=new ClosureTable();
             $z->deleteNode($rand);
        }
        }
}