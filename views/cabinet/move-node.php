<?php
	use yii\helpers\Html;
	use yii\bootstrap\ActiveForm;
	use app\models\ClosureTable\ClosureTable;
	use app\models\String;
	$model2=new String();
?>
<div class="col-md-4">
	<div class="panel panel-info">
		<div class="panel-heading">
		Переместить узел
		</div>
		<div class="panel-body">
			<?php 
				$form = ActiveForm::begin([
			    'id' => 'block-calculator',
			    'options' => ['class' => 'form', 'enctype' => 'multipart/form-data'],    
			    'action'=>"index.php?r=cabinet%2Fnode-move"
			])
			?>			
			<?= $form->field($model, 'form')->dropdownList(ClosureTable::find()->select(['node_title', 'node_id'])->indexBy('node_id')->column())->label('Выберите Узел'); ?>
			<?= $form->field($model, 'dom')->dropdownList(ClosureTable::find()->select(['node_title', 'node_id'])->indexBy('node_id')->column())->label('Выберите Новый Родительский Узел');?>
			
			<input type="hidden" name="item-type" value="Node">
			<input type="hidden" name="item-id" value="<?= $model->form;?>">
			<div class="form-group">
			            <?= Html::submitButton("Сохранить изменения", ['class' => 'btn btn-success']) ?>
			 </div>
			<?php ActiveForm::end() ?>
		</div>
	</div>
</div>

