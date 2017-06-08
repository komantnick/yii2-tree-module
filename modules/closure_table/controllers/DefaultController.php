<?php

namespace app\modules\closure_table\controllers;

use yii\web\Controller;

/**
 * Default controller for the `closure_table` module
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
