<?php

namespace app\modules\adjacency_list;

/**
 * adjacency_list module definition class
 */
use \yii\base\Module as BaseModule;
class AdjacencyListModule extends BaseModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\adjacency_list\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        // custom initialization code goes here
    }
}
