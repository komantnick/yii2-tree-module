<?php
	use yii\helpers\Html;
	use yii\bootstrap\ActiveForm;
	use app\models\MaterializedPath\MaterializedPath;
	use app\models\Anket;
	$model2=new Anket();
?>
<div class="col-md-4">
	<div class="panel panel-info">
		<div class="panel-heading">
		Добавить узел
		</div>
		<div class="panel-body">
			<?php 
				$form = ActiveForm::begin([
			    'id' => 'block-calculator',
			    'options' => ['class' => 'form', 'enctype' => 'multipart/form-data'],    
			    'action'=>"index.php?r=materialized-path%2Fadd-items"
			])
			?>			
			<?= $form->field($model, 'form')->textInput(['placeholder'=>"Уровень узла"])->label('Количество узлов')  ?>
			<?= $form->field($model, 'dom')->textInput(['placeholder'=>"Имя узла"])->label('Название подразделения')  ?>
			
			<input type="hidden" name="item-type" value="MaterializedPath">
			<input type="hidden" name="item-id" value="<?= $model->form;?>">
			<input type="hidden" name="count" value="6">
			<div class="form-group">
			            <?= Html::submitButton("Сохранить изменения", ['class' => 'btn btn-success']) ?>
			 </div>
			<?php ActiveForm::end() ?>
		</div>
	</div>
</div>
