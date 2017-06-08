<?php
	use yii\helpers\Html;
	use yii\bootstrap\ActiveForm;
	use app\models\MaterializedPath\MaterializedPath;
	use app\models\String;
	use app\models\Anket;
	$model2=new Anket();
?>
<div class="col-md-4">
	<div class="panel panel-info">
		<div class="panel-heading">
		Получить дерево потомков узла
		</div>
		<div class="panel-body">
			<?php 
				$form = ActiveForm::begin([
			    'id' => 'block-calculator',
			    'options' => ['class' => 'form', 'enctype' => 'multipart/form-data'],    
			    'action'=>"index.php?r=materialized-path%2Fshow-tree"
			])
			?>			
			<?= $form->field($model, 'form')->dropdownList(MaterializedPath::find()->select(['node_title', 'node_id'])->where(['node_status'=>1])->indexBy('node_id')->column())->label('Выберите Узел'); ?>
			<?= $form->field($model, 'dom')->textInput(['placeholder'=>"Уровень вложенности"])->label('Уровень вложенности(полное дерево-0 или пустая строка)')   ?>

			
			<input type="hidden" name="item-type" value="AdjacencyList">
			<input type="hidden" name="item-id" value="<?= $model->form;?>">
			<div class="form-group">
			            <?= Html::submitButton("Сохранить изменения", ['class' => 'btn btn-success']) ?>
			 </div>
			<?php ActiveForm::end() ?>
		</div>
	</div>
</div>