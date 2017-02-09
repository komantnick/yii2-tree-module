<?php

namespace app\modules\nested_sets\controllers;


use yii\web\Controller;
use app\modules\nested_sets\models\NestedSets;

/**
 * Default controller for the `NestedSets` module
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
