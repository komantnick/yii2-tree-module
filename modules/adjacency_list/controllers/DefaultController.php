<?php

namespace app\modules\adjacency_list\controllers;

use yii\web\Controller;
use app\modules\adjacency_list\models\AdjacencyList;
/**
 * Default controller for the `adjacency_list` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    public function actionIndex()
    {
        $model=new AdjacencyList();
        $data=$model->get(3);
        return $this->render('index',array('data'=>$data));


        //return $this->render('index');
    }
    public function actionCreate(){
        $model=new AdjacencyList();
        $data=$model->create('random');
        return $this->render('create',array('data'=>$data));
    }
    public function actionAdd(){
        $model=new AdjacencyList();
        $data=$model->add(1,'randoz');
        return $this->render('create',array('data'=>$data));
    }
    public function actionDeleteNodes(){
        $model=new AdjacencyList();
        $data=$model->delete(5);
        return $this->render('deletenodes',array('data'=>$data));

    }
    public function actionBlock(){
        $model=new AdjacencyList();
        $data=$model->block(5);
        return $this->render('block',array('data'=>$data));

    }
    public function actionMove(){
        $model=new AdjacencyList();
        $data=$model->move(5,2);
        return $this->render('move',array('data'=>$data));
        
    }
    public function actionRandom(){
        return $this->render('random');
        
    }
     public function actionRand1(){
        return $this->render('rand1');
        
    }
    public function actionTree(){
          $model=new AdjacencyList();
        $data=$model->tree();
        return $this->render('tree',array('data'=>$data));
        
    }
    public function actionChildbranch(){
          $model=new AdjacencyList();
        $data=$model->child_branch(3);
        return $this->render('childbranch',array('data'=>$data));
        
    }
    public function actionChild(){
          $model=new AdjacencyList();
        $data=$model->child(3);
        return $this->render('child',array('data'=>$data));
        
    }

}
