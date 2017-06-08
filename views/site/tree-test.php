<?php
	use yii\helpers\Html;
	use yii\bootstrap\ActiveForm;
	use app\models\Tree\Node;
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
			    'action'=>"/cabinet/add-item"
			])
			?>			
			<?= $form->field($model, 'parent_id')->dropdownList(Node::find()->select(['node_title', 'node_id'])->indexBy('node_id')->column())->label('Выберите Родителя'); ?>
			<?= $form->field($model, 'node_title')->textInput(['placeholder'=>"Название подразделения"])->label('Название подразделения')  ?>
			<?= $form->field($model2, 'anket_title')->textInput(['placeholder'=>"Название подразделения (полное)"])->label('Название подразделения(полное)')  ?>
			<?= $form->field($model2, 'anket_secondname')->textInput(['placeholder'=>"Фамилия начальника"])->label('Фамилия начальника')  ?>
			<?= $form->field($model2, 'anket_name')->textInput(['placeholder'=>"Имя начальника"])->label('Имя начальника')  ?>			
			<?= $form->field($model2, 'anket_patronymic')->textInput(['placeholder'=>"Отчество начальника"])->label('Отчество начальника')  ?>
			<input type="hidden" name="item-type" value="Node">
			<input type="hidden" name="item-id" value="<?= $model->node_id;?>">
			<div class="form-group">
			            <?= Html::submitButton("Сохранить изменения", ['class' => 'btn btn-success']) ?>
			 </div>
			<?php ActiveForm::end() ?>
		</div>
	</div>
</div>

