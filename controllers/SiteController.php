<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Tree\Node;
use app\models\User;
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
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

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        //Потестируем json_encode
        //$this->layout = 'loginpage';
        return $this->render('index');
    }
    public function actionLogin()
    {        
        $post=Yii::$app->request->post();
        if ($post){
            $username=$post['User']['username'];
            $password=$post['User']['password'];        
            $user = User::find()->where(['username' => $username, 'password' => $password])->one();       
            if($user){    
                    Yii::$app->user->login($user);                    
                    $this->redirect('/cabinet/index');
                }   
            else{
                $this->redirect('/site/index'); 
            }    
        } 
        else{
            $this->redirect('/site/index'); 
        }   
        // 
    }


    /**
     * Login action.
     *
     * @return string
     */
    /*public function actionShowTree(){
        $model=new \app\models\Tree\Node();
        $model=$model->findOne(2);
        print_r($model);
        return $this->render('show-tree',['model'=>$model]);
    }*/
    /*public function actionTreeTest()
    {   
        $model=new \app\models\Tree\Node();
        $tree=\app\models\Tree\Node::recoursiveTree(1,true);
        echo json_encode($tree);
        print_r($tree);
        
        return $this->render('tree-test',['model'=>$model]);
    }*/
   // public function actionJsonTest(){
        /*$inner=array();
        array_push($inner,array("name"=>"Node Two 1"));
        array_push($inner,array("name"=>"Node Two 2"));
        $array=array();
        
        array_push($array,array("name"=>"NodeOne","children"=>$inner));
        array_push($array,array("name"=>"NodeTwo","children"=>$inner));
        $a["name"]="root";
        $a["children"]=$array;
        echo json_encode($a);
        echo "<hr>";
        $inner[]=array("name"=>"Node Two 1");
        $inner[]=array("name"=>"Node Two 2");
        $arr["name"]="NodeOne";
        $arr["children"]=$inner;
        echo json_encode($arr);
        //Тест перевода
        
        echo json_encode($tree);*/
      //  $model=new \app\models\Tree\Node();
      //  $model=$model->findOne(1);                
        //$tree=$model->getJsonTree();
      //  $model->formJsonFile();
       // return $this->render("json-test");
  //  }




}
