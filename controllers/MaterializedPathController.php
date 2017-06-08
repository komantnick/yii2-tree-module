<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

use yii\web\UploadedFile;
use yii\db\Connection;
class MaterializedPathController extends Controller{
	public function actionIndex(){
		return $this->render('index');
	}
	public function actionAddNode(){
		 $model=new \app\models\MaterializedPath\MaterializedPath();           
        return $this->render('add-node',['model'=>$model]);
	}
	public function actionNodeAdd(){
		 //получим модель которую обрабатывает ActiveForm
        $request = Yii::$app->request;
        $modelName=$request->post("item-type");
        //print_r($modelName);exit;        
        $model=$this->modelGenerator($modelName);
        $model->load(\Yii::$app->request->post());
        $model2=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList();       
        $model2->load(\Yii::$app->request->post());         
        //Загрузим данные из формы
        if (isset($model2->form)) {$model->node_path=$model->getpath($model2->form).'/';}
        else {$model->node_path='/';}
        //производим специфические для модели действия     
        $modelSpec=true;
        switch ($modelName) {
            case 'MaterializedPath':
                break;            
             
            default:
                $modelSpec=false;
                break;
        }
        //проверяем модель и сохраняем
        if ($model->validate()&&$modelSpec){
            $model->save();
        }
        //делаем редирект
        $this->redirect("index.php?r=materialized-path%2Findex");
	}
    public function actionAddItems(){
         $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList(); 

        $model->load(\Yii::$app->request->post()); 
         $model2=new \app\models\MaterializedPath\MaterializedPath();
         $n=$model->form;
         $lev=$model->dom;
         $z=$model2->choose($lev);
         $modelSpec=true;
        switch ($modelName) {
            case 'MaterializedPath':
                break;            
             
            default:
                $modelSpec=false;
                break;
        }
        //проверяем модель и сохраняем
        if ($model->validate()&&$modelSpec){      
            for ($i=1;$i<=$n;$i++){ 
                 $model2=new \app\models\MaterializedPath\MaterializedPath();
                 $z=$model2->choose($lev);
                 $model2->node_path=$z;
                 $model2->node_title=$model2->generateName(8);
                 $model2->save();
            }
           // exit;
            
    }
        $z=Yii::getLogger()->getElapsedTime();
       // echo 'console Works!'."\n";
    print_r(sprintf('%0.3f',$z));
    echo "<br>";
    print_r(round(memory_get_peak_usage()/(1024*1024),2));
}
    public function actionNodeBlock(){
         $model=new \app\models\String();           
        return $this->render('node-block',['model'=>$model]);

    }
    public function actionNodeUnblock(){
         $model=new \app\models\String();           
        return $this->render('node-unblock',['model'=>$model]);

    }
     public function actionDeleteNode(){
        $model=new \app\models\String();           
        return $this->render('delete-node',['model'=>$model]);

    }
    public function actionUnblockNode(){
        $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post()); 
        //print_r($model->form);exit; 
        $model2=new \app\models\MaterializedPath\MaterializedPath(); 
        $model2=$this->modelGenerator($modelName); 
        $model2->node_id=$model->form;
        //Загрузим данные из формы
        //$model->load(\Yii::$app->request->post());
        //$model2=$model2->findOne($model->form); 
        //print_r($model);exit;
        //производим специфические для модели действия     
        //print_r($model);exit;
        $model2->unblock($model->form);
        return "Узел разблокирован!";
    }
    public function actionBlockNode(){
        $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post()); 
        //print_r($model->form);exit; 
        $model2=new \app\models\MaterializedPath\MaterializedPath();  
        $model2=$this->modelGenerator($modelName); 
        $model2->node_id=$model->form;
        //Загрузим данные из формы
        //$model->load(\Yii::$app->request->post());
        //$model2=$model2->findOne($model->form); 
        //print_r($model);exit;
        //производим специфические для модели действия     
        //print_r($model);exit;
        $model2->block($model->form);
        return "Узел заблокирован!";
    }
    public function actionDelete(){
          $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post()); 
        //print_r($model->form);exit; 
        //$model2=new \app\models\AdjacencyList\AdjacencyList();  
        //$model2=$this->modelGenerator($modelName); 
        //$model2->node_id=$model->form;
        $z=new \app\models\MaterializedPath\MaterializedPath();
        $z->deleteNode($model->form);
        return "Узел удален навсегда!";

    }
     public function actionMoveNode(){
         $model=new \app\models\String();           
        return $this->render('move-node',['model'=>$model]);
    }
    public function actionNodeMove(){
        $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post());
        $model3=new \app\models\MaterializedPath\MaterializedPath();
        $model3->move($model->form,$model->dom);
        return "Узел перемещен!";

    }
    public function actionGTree(){
        $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post()); 
     $model2=new \app\models\MaterializedPath\MaterializedPath();
        //$model=$model->findOne(1); 
        //print_r($model);               
        $z=$model2->getTree($model->form);
         return $this->render('g-tree',['z'=>$z]);
        return $z;
        //print_r($z);
        //return $z;
    }
    public function actionPTree(){

        $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post()); 
     $model2=new \app\models\MaterializedPath\MaterializedPath();
        //$model=$model->findOne(1); 
        //print_r($model);               
        $z=$model2->getPTree($model->form);
         return $this->render('p-tree',['z'=>$z]);
        return $z;
        //print_r($z);
        //return $z;
    }
     public function actionShowTree(){
        $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post()); 
        $model2=new \app\models\MaterializedPath\MaterializedPath();
        $model2=$model2->findOne($model->form); 
        //print_r($model);               
        $model2->formJsonFile();
        //print_r($z);
        return $this->render("show-tree");
    }
    public function actionParentTree(){
        $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post()); 
        $model2=new \app\models\MaterializedPath\MaterializedPath();
        $model2=$model2->findOne($model->form); 
        //print_r($model);               
        $model2->formJsonParentFile();
        //print_r($z);
        return $this->render("parent-tree");
    }
     public function actionShowTreeIntro(){
         $model=new \app\models\String();           
        return $this->render('show-tree-intro',['model'=>$model]);

    }
    public function actionParentTreeIntro(){
         $model=new \app\models\String();           
        return $this->render('parent-tree-intro',['model'=>$model]);

    }
     public function actionPTreeIntro(){
         $model=new \app\models\String();           
        return $this->render('p-tree-intro',['model'=>$model]);

    }
    public function actionGTreeIntro(){
         $model=new \app\models\String();           
        return $this->render('g-tree-intro',['model'=>$model]);

    }



	 public function behaviors()
    {
        return [
           /* 'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],*/
            'common' => [
                'class' => \app\components\CommonMethodsBehavior::classname(),
                 ],
        ];
    }


}