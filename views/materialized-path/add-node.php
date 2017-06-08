<?php
	use yii\helpers\Html;
	use yii\bootstrap\ActiveForm;
	use app\models\MaterializedPath\MaterializedPath;
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
			    'action'=>"index.php?r=materialized-path%2Fnode-add"
			])
			?>			
			<?= $form->field($model2, 'form')->dropdownList(MaterializedPath::find()->select(['node_title', 'node_id'])->indexBy('node_id')->column())->label('Выберите Родительский Узел'); ?>
			<?= $form->field($model, 'node_title')->textInput(['placeholder'=>"Имя узла"])->label('Имя узла');?>
			
			<input type="hidden" name="item-type" value="MaterializedPath">
			<input type="hidden" name="item-id" value="<?= $model2->form;?>">
			<div class="form-group">
			            <?= Html::submitButton("Сохранить изменения", ['class' => 'btn btn-success']) ?>
			 </div>
			<?php ActiveForm::end() ?>
		</div>
	</div>
</div>