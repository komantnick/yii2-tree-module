<?php

namespace app\modules\materialized_path\controllers;

use yii\web\Controller;
use app\modules\materialized_path\models\MaterializedPath;
/**
 * Default controller for the `materialized_path` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function actionCreate()
    {
       $model=new MaterializedPath();
        $data=$model->create('random');
        return $this->render('create',array('data'=>$data));
    }
    public function actionAdd()
    {
       $model=new MaterializedPath();
        $data=$model->add(1,'random');
        $data=$model->add(4,'random');
        $data=$model->add(5,'random');
        $data=$model->add(7,'random');
        $data=$model->add(9,'random');
        return $this->render('add',array('data'=>$data));
    }
    public function actionDeletenode()
    {
       $model=new MaterializedPath();
        $data=$model->deletenode(3);
        return $this->render('deletenode',array('data'=>$data));
    }
    public function actionBlock()
    {
       $model=new MaterializedPath();
        $data=$model->block(3);
        return $this->render('block',array('data'=>$data));
    }
    public function actionMove()
    {
       $model=new MaterializedPath();
        $data=$model->move(3,2);
        return $this->render('move',array('data'=>$data));
    }
    public function actionChildbranch()
    {
       $model=new MaterializedPath();
        $data=$model->child_branch(1);
        return $this->render('childbranch',array('data'=>$data));
    }
    public function actionChild()
    {
       $model=new MaterializedPath();
        $data=$model->child(1);
        return $this->render('child',array('data'=>$data));
    }
    public function actionFloyd(){
         $model=new MaterializedPath();
         $number=6;
        $distance=$model->floyd($number);
        return $this->render('floyd',array('distance'=>$distance,'number'=>$number,'inf'=>999999));
        
    }
    public function actionTree(){
        $model=new MaterializedPath();
        $data=$model->tree();
        return $this->render('tree',array('data'=>$data));

    }
    public function actionParentbranch(){
        $model=new MaterializedPath();
        $data=$model->parent_branch(12);
        return $this->render('parentbranch',array('data'=>$data));
        
    }
}
