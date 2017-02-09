<?php

namespace app\modules\adjacency_list\controllers;


use yii\web\Controller;
use app\modules\adjacency_list\models\AdjacencyList;

/**
 * Default controller for the `AdjacencyList` module
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
