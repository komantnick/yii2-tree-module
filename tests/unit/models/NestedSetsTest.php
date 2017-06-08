<?php

namespace tests\models;

use app\models\NestedSets\NestedSets;

class NestedSetsTest extends \Codeception\Test\Unit
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
    public function testNewRoot()
    {

    }
    public function testGetParentsTree()
    {
        for ($i=1;$i<=100;$i++){
            $node=new NestedSets();
            $rand=$node->selectRand();
            $node=NestedSets::findOne($rand);
     $z=$node->recoursiveParentTree($node['node_id'],true);
     $k=json_encode($z);
        }   
    }
    public function testGetChildrenTree()
    {
         for ($i=1;$i<=100;$i++){
            $node=new NestedSets();
            $rand=$node->selectRand();
            $node=NestedSets::findOne($rand);
     $z=$node->recoursiveTree($node['node_id'],true);
     $k=json_encode($z);
         }
    }
    public function testGetParent(){
        for ($i=1;$i<=100;$i++){
            $node=new NestedSets();
            $rand=$node->selectRand();
             $node=NestedSets::findOne($rand);
     $z=$node->getParentTree($node['node_id'],true);
     $k=json_encode($z);
         }
    }
     public function testGetChildren()
    {
        for ($i=1;$i<=100;$i++){
            $node=new NestedSets();
            $rand=$node->selectRand();
             $node=NestedSets::findOne($rand);
     $z=$node->getChildTree($node['node_id'],true);
     $k=json_encode($z);
         }
    
    }
     public function testAddNodes(){
        for ($i=1;$i<=100;$i++){
            $node=new NestedSets();
            $z=rand(1,4);
            $k=$node->Choose($z);
        $node->level=$k;
        $node->node_title=$node->generateName(8);
        $node->save();
        }
    }
    public function testAddLevel3(){
        $node=new NestedSets();
        $anc=$node->Choose(3);
        for ($i=1;$i<=100;$i++){
             $node=new NestedSets();
            $z=rand(1,4);
            $k=$node->Choose($z);
         $node->level=$k;
            $node->node_title=$node->generateName(8);
            $node->save();
        }
    }
    public function testAddLevel5(){
        $node=new NestedSets();
        $anc=$node->Choose(5);
        for ($i=1;$i<=100;$i++){
             $node=new NestedSets();
            $z=rand(1,4);
            $k=$node->Choose($z);
         $node->level=$k;
            $node->node_title=$node->generateName(8);
            $node->save();
        }

    }
    public function testMoveNode(){
        $node=new NestedSets();
        $id=$node->selectRand();
        for ($i=1;$i<=100;$i++){
            $node=new NestedSets();
            $rand=$node->selectRand();
            $node->move($rand,$id);
        }
    }
        public function testBlockNode(){
            $node=new NestedSets();
            for ($i=1;$i<=100;$i++){
            $node=new NestedSets();
            $rand=$node->selectRand();
            $node->block($rand);
        }
    }
         public function testDeleteNode(){
            $node=new NestedSets();
            for ($i=1;$i<=100;$i++){
            $node=new NestedSets();
            $rand=$node->selectRand();
             $z=\app\models\NestedSets\NestedSets::
             deleteAll('node_id = :id', [':id' => $rand]);
        }
        }
}