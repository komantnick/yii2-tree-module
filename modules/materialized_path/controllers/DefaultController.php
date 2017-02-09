<?php

namespace app\modules\materialized_path\controllers;


use yii\web\Controller;
use app\modules\materialized_path\models\MaterializedPath;

/**
 * Default controller for the `MaterializedPath` module
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
}
