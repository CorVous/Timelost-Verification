class Node {
	constructor(id, subNodes, openSides, symbol=null, corridorLink) {
		this.id = id;
		this.subNodes = subNodes;
		this.openSides = openSides;
		this.symbol = symbol;
	}
}

class SubNode {
	constructor(code = "") {
		this.code = code;
		// this.linkedNode
	}
}

let linkDict = {};
let hexArray = [];

function load() {
	let sideSymbols = document.getElementById("ExtraSymbols").checked;
	let clusterSize = document.getElementById("NodeCount").value?document.getElementById("NodeCount").value:1;
	document.getElementById("canvases").innerHTML = "";
	document.getElementById("load-button").style.display = "none";
	document.getElementById("conflicts").style.display = "none";
	document.getElementById("conflicts").innerHTML = "";
	let dataTest = [];
	let nodes = [];
	let nodeSignature = {};
	let size = sideSymbols? 80 : 15;
	hexArray = [];
	let conflictErrors = "";
	linkDict = {};

	Papa.parse("cot.csv", {
		download: true,
		complete: function(data) {

	    let processedData = processData(data);

	    let head = processedData[2];
	    nodes = shuffle(processedData[0]);
	    nodeSignature = processedData[1];

			var canvasNum = 1;
			
			head.x = 0;
			head.y = 0;

			var preNodeLength = nodes.length;
			var simpleSubNodes = [];
			var hexArrayPiece = [];
			head.subNodes.forEach(function(subNode) {
				simpleSubNodes.push(subNode.code);
			});
			
			head.positionX = 0;
			head.positionY = 0;

			hexArrayPiece[head.positionX + "," + head.positionY] = head;
			drawSolution(head, nodes, size, hexArrayPiece);
			hexArray.push(hexArrayPiece);
			//console.log(hexArray);
			canvasNum++;

			// Draw
			hexArray.forEach(function(hexGroup){
				if (!clusterSize && hexGroup.length > 1 || hexArray.length >= clusterSize) {
					drawCluster(hexGroup, size);
				}
			});

			document.getElementById("load-button").style.display = "block";
			document.getElementById("conflicts").style.display = "block";
		}
	});
}
