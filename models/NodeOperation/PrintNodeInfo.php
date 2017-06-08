<?php
	//класс распечатывает информацио об узле - тестовый класс
	namespace app\models\NodeOperation;
	use app\models\NodeOperation\NodeOperation;	
	use app\models\Tree\Node;
	class PrintNodeInfo extends NodeOperation{		
		public function doExecute($node_id){
			$node=Customer::findOne($node_id);
			echo $node->node_nickname."[".$node->node_id."/".$node->node_parent_id."] ||";
		}
	}
?>