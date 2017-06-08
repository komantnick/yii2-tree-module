<?php
	//Интерфейс производит какое то действие с узлом дерева, узел идентифицируется по id узла
	namespace app\models\NodeOperation;
	use app\models\Tree\node;
	use Yii;
	abstract class NodeOperation{
		public function execute($node_id){
			//для начала проверим валидность узла а уже потом применим метод, который реализован в потомке  - doExecute($node_id);
			if (is_numeric($node_id)){
				$node=Yii::$app->db->createCommand("SELECT COUNT(node_id) AS count_node FROM node WHERE node_id=".$node_id)->queryOne();	
				if ($node['count_node']==1){
					$this->doExecute($node_id);	
				}
				else{
					//выбрасываем исключение - неправильный id получен!
					throw new \Exception("Wrong node_id. node not exists");
				}
			}
			else{
				//выбрасываем исключение - неправильный id получен!
				throw new \Exception("Wrong node_id. node id is not number");
			}
		}
		public abstract function doExecute($node_id);
	}
?>