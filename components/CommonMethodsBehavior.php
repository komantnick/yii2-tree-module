<?php
/*
Поведение - создаём класс для общих функций 
*/

namespace app\components;

use yii;
use yii\base\Behavior;
use yii\db\Connection;

class CommonMethodsBehavior extends Behavior
{
	public static function modelGenerator($modelName,$item_id=0){
        //Создаёт объект класса по названию модели, если задан id то подгружает модель из таблицы
        switch ($modelName){
            case 'Node':
                $modelName="app\models\ClosureTable\ClosureTable";
                break;          
                case 'AdjacencyList':
                $modelName="app\models\AdjacencyList\AdjacencyList";
                break;    
                case 'MaterializedPath':
                $modelName="app\models\MaterializedPath\MaterializedPath";
                break;  
                case 'NestedSets';
                $modelName="app\models\NestedSets\NestedSets";
                break; 
            default:
                $modelName="app\models\\".$modelName;
        }
        
        $class = new \ReflectionClass($modelName);
        $obj = $class->newInstance();
        if (is_numeric($item_id)&&$item_id>0){
            $obj=$obj->findOne($item_id);  
            if (is_null($obj)){                
                $obj = $class->newInstance();
            }
        }
        return $obj;
    }    
    
}