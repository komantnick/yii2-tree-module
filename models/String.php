<?php
/**
 * Created by PhpStorm.
 * User: Abhimanyu
 * Date: 18-02-2015
 * Time: 22:07
 */

namespace app\models;

use yii\base\Model;
use Yii;
class String extends Model
{
    public $form;
    public $dom;

    public function rules()
    {
        return [           
            //[['form'], 'required'],
            [['form'],'string','max'=>150],  
            [['dom'],'string','max'=>150],         
        ];
    }

    public function attributeLabels()
    {
        return [
            'form' => 'String'
        ];
    }
}