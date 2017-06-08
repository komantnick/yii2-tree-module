<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;

use yii\web\UploadedFile;
use yii\db\Connection;

class CabinetController extends Controller
{
    public function actionIndex(){
        return $this->render('index');
    }
    public function actionAddNode(){
        $model=new \app\models\ClosureTable\ClosureTable();           
        return $this->render('add-node',['model'=>$model]);
    }  
    public function actionTheTree(){
        $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post()); 
        $model2=new \app\models\ClosureTable\ClosureTable();
        
        $model2=$model2->findOne(28); 
        //print_r($model);               
        $model2->formJsonFile();
    }
    public function actionShowTree(){
        $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post()); 
        $model2=new \app\models\ClosureTable\ClosureTable();
        
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
        $model2=new \app\models\ClosureTable\ClosureTable();
        $model2=$model2->findOne($model->form); 
        //print_r($model);               
        $model2->formJsonParentFile();
        return $this->render("parent-tree");
        //print_r($z);exit;
        //return $this->render("show-tree");
    }
    /*public function actionMove(){
        $model=new \app\models\ClosureTable\ClosureTable();
        $model=$model->findOne(1); 
        $model2=new \app\models\ClosureTable\ClosureTable();
        $model2=$model->findOne(1); 
    }*/
    public function actionAddItem(){
        //получим модель которую обрабатывает ActiveForm
        $request = Yii::$app->request;
        $modelName=$request->post("item-type");        
        $model=$this->modelGenerator($modelName,$request->post("item-id"));        
        //Загрузим данные из формы
        $model->load(\Yii::$app->request->post());
        //производим специфические для модели действия     
        $modelSpec=true;
        switch ($modelName) {
            case 'Node':
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
        $this->redirect("index.php?r=cabinet%2Findex");
    }
    public function actionAddItems(){
         //получим модель которую обрабатывает ActiveForm
        $request = Yii::$app->request;
        $modelName=$request->post("item-type");        
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList(); 

        $model->load(\Yii::$app->request->post()); 
          $model2=new \app\models\ClosureTable\ClosureTable();
         $n=$model->form;
         $lev=$model->dom;
         $z=$model2->choose($lev);

        //производим специфические для модели действия     
        $modelSpec=true;

        switch ($modelName) {
            case 'Node':
                break;            
             
            default:
                $modelSpec=false;
                break;
        }
        //проверяем модель и сохраняем
      if ($model->validate()&&$modelSpec){      
            for ($i=1;$i<=$n;$i++){ 

                 $model2=new \app\models\ClosureTable\ClosureTable();
                 $z=$model2->choose($lev);
                 $model2->parent_id=$z;
                 $model2->node_title=$model2->generateName(8);
                 $model2->save();
            }
            //exit;
            
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
    public function actionBlockNode(){
        $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post()); 
        //print_r($model->form);exit; 
        $model2=new \app\models\ClosureTable\ClosureTable();  
        $model2=$this->modelGenerator($modelName); 
        $model2->node_id=$model->form;
        //Загрузим данные из формы
        //$model->load(\Yii::$app->request->post());
        //$model2=$model2->findOne($model->form); 
        //print_r($model);exit;
        //производим специфические для модели действия     
        //print_r($model);exit;
        //print_r($model->form);exit;
        $model2->block($model->form);
        //echo "B";exit;
        return "Узел заблокирован!";
    }
     public function actionNodeUnblock(){
         $model=new \app\models\String();           
        return $this->render('node-unblock',['model'=>$model]);

    }
    public function actionUnblockNode(){
        $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post()); 
        //print_r($model->form);exit; 
        $model2=new \app\models\AdjacencyList\AdjacencyList();  
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
        $z=new \app\models\ClosureTable\ClosureTable();
        $z->deleteNode($model->form);
        return "Узел удален навсегда!";
    }
    public function actionDeleteNode(){
        $model=new \app\models\String();           
        return $this->render('delete-node',['model'=>$model]);

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
        $model3=new \app\models\ClosureTable\ClosureTable();
        $model3->move($model->form,$model->dom);
        return "Узел перемещен!";

    }
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
     public function actionShowTreeIntro(){
         $model=new \app\models\String();           
        return $this->render('show-tree-intro',['model'=>$model]);

    }
    public function actionParentTreeIntro(){
         $model=new \app\models\String();           
        return $this->render('parent-tree-intro',['model'=>$model]);

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
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

}