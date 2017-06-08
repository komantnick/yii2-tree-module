<?php
	$tree=$model->getChildTree(3);

	//echo json_encode($tree);
	echo "<hr>";
	print_r($tree);
	foreach ($tree as $leaf){
		print_r($leaf);
		echo "<br>";
	}
?>