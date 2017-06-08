<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\helpers\Html;
	use yii\bootstrap\ActiveForm;
	use app\models\MaterializedPath\MaterializedPath;
	use app\models\NestedSets\NestedSets;
	use app\models\Tree\Node;
	use app\models\String;
class TestController extends Controller
{
public function actionTest1alist(){
		 $model=new \app\models\String();          
        return $this->render('test1\alist',['model'=>$model]);
	}
	public function actionTest1ctable(){
		 $model=new \app\models\String();          
        return $this->render('test1\ctable',['model'=>$model]);
	}
	public function actionTest1mpath(){
		 $model=new \app\models\String();          
        return $this->render('test1\mpath',['model'=>$model]);
	}
	public function actionTest1nsets(){
		 $model=new \app\models\String();          
        return $this->render('test1\nsets',['model'=>$model]);
	}

}