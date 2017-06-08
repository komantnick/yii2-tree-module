<?php

namespace app\modules\nested_sets\controllers;

use yii\web\Controller;

/**
 * Default controller for the `nested_sets` module
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
