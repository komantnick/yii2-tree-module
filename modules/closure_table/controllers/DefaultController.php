<?php

namespace app\modules\closure_table\controllers;

use yii\web\Controller;
use app\modules\closure_table\models\ClosureTable;
use app\modules\closure_table\models\ClosureTableInfo;
/**
 * Default controller for the `ClosureTableModule` module
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
    public function actionSave(){
        $model=new ClosureTable();
        $model_info=new ClosureTableInfo();
        $model_info->user_name='1';
        $model->save();
        $model_info->save();
    }
}
