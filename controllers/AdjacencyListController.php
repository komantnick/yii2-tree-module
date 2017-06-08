<?php
namespace app\controllers;
use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use app\models\AdjacencyList\AdjacencyList;
use yii\web\UploadedFile;
use yii\db\Connection;
class AdjacencyListController extends Controller{
	public function actionIndex(){
		return $this->render('index');
	}
	public function actionAddNode(){
		 $model=new \app\models\AdjacencyList\AdjacencyList();           
        return $this->render('add-node',['model'=>$model]);
	}

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
            case 'AdjacencyList':
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
        $this->redirect("index.php?r=adjacency-list%2Findex");
    }
    public function actionAddItems(){
         //получим модель которую обрабатывает ActiveForm
        $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList\AdjacencyList(); 

        $model->load(\Yii::$app->request->post()); 
         $model2=new \app\models\AdjacencyList\AdjacencyList();
         $n=$model->form;
         $lev=$model->dom;
         $z=$model2->choose($lev);
         //print_r($z);exit;
        //производим специфические для модели действия     
        $modelSpec=true;
        switch ($modelName) {
            case 'AdjacencyList':
                break;            
             
            default:
                $modelSpec=false;
                break;
        }

        //проверяем модель и сохраняем
        if ($model->validate()&&$modelSpec){      
            for ($i=1;$i<=$n;$i++){ 
                 $model2=new \app\models\AdjacencyList\AdjacencyList();
                 $z=$model2->choose($lev);
                 $model2->node_ancestor=$z;
                 $model2->node_title=$model2->generateName(8);
                 $model2->save();
            }
            
            
        }
          $this->redirect("index.php?r=adjacency-list%2Findex");

    }
    public function actionNodeBlock(){
         $model=new \app\models\String();           
        return $this->render('node-block',['model'=>$model]);

    }
    public function actionBlockNode(){
        $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList\AdjacencyList();       
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
        $model2->block($model->form);
        $this->redirect("index.php?r=adjacency-list%2Fblock-index");
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
        //$model=new \app\models\AdjacencyList\AdjacencyList\AdjacencyList();       
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
       $this->redirect("index.php?r=adjacency-list%2Funblock-index");
    }
    public function actionDelete(){
         $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post()); 
        //print_r($model->form);exit; 
        //$model2=new \app\models\AdjacencyList\AdjacencyList\AdjacencyList();  
        //$model2=$this->modelGenerator($modelName); 
        //$model2->node_id=$model->form;
        $z=new \app\models\AdjacencyList\AdjacencyList();
        $z->deleteNode($model->form);
    }
    public function actionDeleteNode(){
        $model=new \app\models\String();           
        return $this->render('delete-node',['model'=>$model]);

    }
    public function actionAddThousandNodes(){
        for ($i=1;$i<=100;$i++){
            $node=new AdjacencyList();
            $z=rand(1,4);
        $node->node_ancestor=$node->Choose($z);
        $node->node_title=$node->generateName(8);
        $node->save();
        }

    }
    public function actionGetParent(){
     for ($i=1;$i<=100;$i++){
            $node=new AdjacencyList();
            $rand=$node->selectRand();
             $node=AdjacencyList::findOne($rand);
     $z=$node->getParentTree($node['node_id'],true);
     $k=json_encode($z);
         }
    }
     public function actionGetChildren()
    {
     for ($i=1;$i<=100;$i++){
            $node=new AdjacencyList();
            $rand=$node->selectRand();
             $node=AdjacencyList::findOne($rand);
     $z=$node->getChildTree($node['node_id'],true);
     $k=json_encode($z);
         }

    }
     public function actionMoveNodes(){
        for ($i=1;$i<=100;$i++){
            $node=new AdjacencyList();
            $rand=$node->selectRand();
            $id=$node->selectRand();
            $node->move($rand,$id);
        }
    }
     public function actionShowTree(){
         $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post()); 
        $model2=new \app\models\AdjacencyList\AdjacencyList\AdjacencyList();
        $model2=$model2->findOne($model->form); 
        //print_r($model);               
        $model2->formJsonFile();
        exit;
        //print_r($z);
        return $this->render("show-tree");
    }
    public function actionParentTree(){
        $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post()); 
        $model2=new \app\models\AdjacencyList\AdjacencyList\AdjacencyList();
        $model2=$model2->findOne($model->form); 
        //print_r($model);               
        $z=$model2->formJsonParentFile();
        exit;
        return $this->render("parent-tree");
        //print_r($z);exit;
        //return $this->render("show-tree");
    }
    public function actionMoveNode(){
         $model=new \app\models\String();           
        return $this->render('move-node',['model'=>$model]);
    }
    public function actionNodeMove(){
        $request = Yii::$app->request;
        $modelName=$request->post("item-type"); 
        $model=new \app\models\string();
        //$model=new \app\models\AdjacencyList\AdjacencyList\AdjacencyList();       
        $model->load(\Yii::$app->request->post());
        $model3=new \app\models\AdjacencyList\AdjacencyList\AdjacencyList();
        $model3->move($model->form,$model->dom);
        $this->redirect("index.php?r=adjacency-list%2Fmove-index");

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

}

?>