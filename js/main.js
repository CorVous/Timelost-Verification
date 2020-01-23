class Node {
	constructor(id, subNodes, openSides, symbol=null, img) {
		this.id = id;
		this.subNodes = subNodes;
		this.openSides = openSides;
		this.symbol = symbol;
		this.img = img;
	}
}

class SubNode {
	constructor(code = "") {
		this.code = code;
		// this.linkedNode
	}
}

function load() {
	let sideSymbols = document.getElementById("ExtraSymbols").checked;
	document.getElementById("canvases").innerHTML = "";
	document.getElementById("load-button").style.display = "none";
	document.getElementById("conflicts").style.display = "none";
	document.getElementById("conflicts").innerHTML = "";
	let size = sideSymbols? 80 : 15;
	let spacing = 2;

	Papa.parse("cot.csv", {
		download: true,
		complete: function(data) {

		let hexArrayFull = [];

		let hexArrayLength = 0;
		while(hexArrayLength < 5040) {
		    let processedData = processData(data);
		    let head = processedData[1];
		    let linkDict = processedData[2];
		    
	    	let nodes = processedData[0].sort(() => Math.random() - 0.5);
	    	let hexArrayPiece = [];

			hexArrayPiece[head.x + "," + head.y] = head;
			drawSolution(head, nodes, size, hexArrayPiece, linkDict);
			
			hexArrayFull = Object.assign({}, hexArrayFull, hexArrayPiece);

			hexArrayLength = 0;
			for (let key in hexArrayFull) {
				hexArrayLength++;
			}
			console.log("Current Hex Array Length: "+hexArrayLength);
	    }

		// Draw
		let canvasHexagonArray = [];
		for (let key in hexArrayFull) {
			let node = hexArrayFull[key];
			let hexagon = {
				id: node.id,
				symbol: node.symbol,
				openSides: node.openSides,
				subNodes: [node.subNodes[0].code, node.subNodes[1].code, node.subNodes[2].code, node.subNodes[3].code, node.subNodes[4].code, node.subNodes[5].code],
				img: node.img,
				x: node.x,
				y: node.y
			}
			canvasHexagonArray.push(hexagon);
		}

		drawCluster(canvasHexagonArray, size, spacing);

		//download(JSON.stringify(canvasHexagonArray), 'map-data.json', 'text/plain');

		document.getElementById("load-button").style.display = "block";
		document.getElementById("conflicts").style.display = "block";
		}
	});
}
