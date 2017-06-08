<?php

echo $data;
?>
<script src="http://d3js.org/d3.v3.min.js"></script>


<h3>Вывод дерева через D3</h3>
<script>


var treeData = [
{"name":"1-random","parent":null,"children":[{"name":"2-randoz","parent":"1-random"},{"name":"3-randoz1","parent":"1-random","children":[{"name":"15-kot","parent":"3-random"},{"name":"11-randoo","parent":"3-random"},{"name":"8-Chat","parent":"3-random"}]},{"name":"4-randoz2","parent":"1-random"},{"name":"5-randoz3","parent":"1-random","children":[{"name":"12-randoo2","parent":"5-random"}]},{"name":"6-randoz4","parent":"1-random"},{"name":"7-randoz5","parent":"1-random","children":[{"name":"13-randoo3","parent":"7-random"}]},{"name":"9-rafg","parent":"1-random"},{"name":"10-randoz","parent":"1-random"}]},{"name":"14-Kalach","parent":null},
 /* {
    "name": "Batman",
    "parent": "null",
    'image':"/yii2-tree-module/images/image1.png",
    "children": [
      {
        "name": "Batman",
        "parent": "Top Level",
        'image':"/yii2-tree-module/images/image1.png",
        "children": [
          {
            "name": "Batman",
            "parent": "Level 2: A",
            'image':"/yii2-tree-module/images/image1.png"
          },
          {
            "name": "Batman",
            "parent": "Level 2: A",
            'image':"/yii2-tree-module/images/image1.png"
          },
          {
            "name": "Batman",
            "parent": "Level 2: A",
            'image':"/yii2-tree-module/images/image1.png"
          },
        ]
      },
      {
        "name": "Batman",
        "parent": "Top Level",
        'image':"/yii2-tree-module/images/image1.png"
      }
    ]
  }*/
];

/*{"0":{"name":"1-random","parent":null,"children":[{"name":"2-randoz","parent":"1-random"},{"name":"3-randoz1","parent":"1-random","children":{"name":"8-Chat","parent":"3-random"}},{"name":"4-randoz2","parent":"1-random"},{"name":"5-randoz3","parent":"1-random","children":{"name":"12-randoo2","parent":"5-random"}},{"name":"6-randoz4","parent":"1-random"},{"name":"7-randoz5","parent":"1-random","children":{"name":"13-randoo3","parent":"7-random"}},{"name":"9-rafg","parent":"1-random"},{"name":"10-randoz","parent":"1-random"}]}//,"10":{"name":"14-Kalach","parent":null}}*/
// ************** Generate the tree diagram	 *****************
var margin = {top: 20, right: 120, bottom: 20, left: 120},
	width = 960 - margin.right - margin.left,
	height = 1000 - margin.top - margin.bottom;
	
var i = 0,
	duration = 750,
	root;

var tree = d3.layout.tree()
	.size([height, width]);

var diagonal = d3.svg.diagonal()
	.projection(function(d) { return [d.y, d.x]; });


  //alert(treeData.length);

for (var z=0; z<treeData.length; z++) {
  var svg = d3.select("body").select("div.container").append("svg")
  .attr("width", width + margin.right + margin.left)
  .attr("height", height + margin.top + margin.bottom)
  .append("g")
  .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

root = treeData[z];
root.x0 = (height / 2)+z*500;
root.y0 = 0;
  
update(root);

d3.select(self.frameElement).style("height", "500px");

}

function update(source) {
  // Compute the new tree layout.
  var nodes = tree.nodes(root).reverse(),
	  links = tree.links(nodes);

  // Normalize for fixed-depth.
  nodes.forEach(function(d) { d.y = d.depth * 180; });

  // Update the nodes…
  var node = svg.selectAll("g.node")
	  .data(nodes, function(d) { return d.id || (d.id = ++i); });

  // Enter any new nodes at the parent's previous position.
  var nodeEnter = node.enter().append("g")
	  .attr("class", "node")
	  .attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; })
	  .on("click", click);

  nodeEnter.append("circle")
	  .attr("r", 1e-6)
	  .style("fill", function(d) { return d._children ? "black" : "#fff"; });

  nodeEnter.append("text")
	  .attr("x", function(d) { return d.children || d._children ? -40 : 40; })
	  .attr("dy", ".35em")
	  .attr("text-anchor", function(d) { return d.children || d._children ? "end" : "start"; })
	  .text(function(d) { return d.name; })
	  .style("fill-opacity", 1e-6);

  // Transition nodes to their new position.
  var nodeUpdate = node.transition()
	  .duration(duration)
	  .attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });

   node.append("image")
      .attr("xlink:href",function(d) { return d.image; })
      .attr("x", -40)
      .attr("y", -40)
      .attr("width", 80)
      .attr("height", 80);

  nodeUpdate.select("text")
	  .style("fill-opacity", 1);

  // Transition exiting nodes to the parent's new position.
  var nodeExit = node.exit().transition()
	  .duration(duration)
	  .attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; })
	  .remove();

  nodeExit.select("circle")
	  .attr("r", 1e-6);

  nodeExit.select("text")
	  .style("fill-opacity", 1e-6);

  // Update the links…
  var link = svg.selectAll("path.link")
	  .data(links, function(d) { return d.target.id; });

  // Enter any new links at the parent's previous position.
  link.enter().insert("path", "g")
	  .attr("class", "link")
	  .attr("d", function(d) {
		var o = {x: source.x0, y: source.y0};
		return diagonal({source: o, target: o});
	  });

  // Transition links to their new position.
  link.transition()
	  .duration(duration)
	  .attr("d", diagonal);

  // Transition exiting nodes to the parent's new position.
  link.exit().transition()
	  .duration(duration)
	  .attr("d", function(d) {
		var o = {x: source.x, y: source.y};

		return diagonal({source: o, target: o});
	  })
	  .remove();
 
  // Stash the old positions for transition.
  nodes.forEach(function(d) {
	d.x0 = d.x;
	d.y0 = d.y;
  });
}

// Toggle children on click.
function click(d) {
  if (d.children) {
	d._children = d.children;
	d.children = null;
  } else {
	d.children = d._children;
	d._children = null;
  }
  update(d);
}


</script>