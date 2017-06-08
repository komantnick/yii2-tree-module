<?php
//Модель - описание узла дерева
namespace app\models;
use yii\db\ActiveRecord;
use Yii;
class Anket extends ActiveRecord
{	
	public function rules()
	{
	    return [	       
	    	[['anket_title','anket_name','anket_secondname','anket_patronymic'],'default','value'=>''],	        
	    ];
	}	
}

