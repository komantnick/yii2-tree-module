<?php
	use yii\helpers\Html;
	use yii\bootstrap\ActiveForm;
	use app\models\ClosureTable\ClosureTable;
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
			    'action'=>"index.php?r=cabinet%2Fadd-item"
			])
			?>			
			<?= $form->field($model, 'parent_id')->dropdownList(ClosureTable::find()->select(['node_title', 'node_id'])->indexBy('node_id')->column())->label('Выберите Родителя'); ?>
			<?= $form->field($model, 'node_title')->textInput(['placeholder'=>"Название подразделения"])->label('Название подразделения')  ?>
			<input type="hidden" name="item-type" value="Node">
			<input type="hidden" name="item-id" value="<?= $model->node_id;?>">
			<div class="form-group">
			            <?= Html::submitButton("Сохранить изменения", ['class' => 'btn btn-success']) ?>
			 </div>
			<?php ActiveForm::end() ?>
		</div>
	</div>
</div>

