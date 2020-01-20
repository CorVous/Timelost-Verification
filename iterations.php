<!DOCTYPE html>
<html>

<head>
	<title>Cor_Vous Corridor Solution Full</title>
	<meta name="viewport" content="initial-scale=1">
	<meta charset="UTF-8">
</head>

<body>
	<div>
		<img id="cauldron" src="cauldron.png" />
		<img id="clover" src="clover.png" />
		<img id="diamond" src="diamond.png" />
		<img id="hex" src="hex.png" />
		<img id="plus" src="plus.png" />
		<img id="snake" src="snake.png" />
	</div>
	<p>@Cor_Vous Corridor Vault Solver</p>
	<h4><a href="">[Refresh]</a></h4>
	<h4><a href="https://docs.google.com/forms/d/e/1FAIpQLSefykUlA4O6gBs3NE1IMwMeTPkH3bFEU0MlFtpcHobdz-RVTA/viewform">[Submit Data Here]</a></h4>
	<form>
		<fieldset>
			<legend>Options</legend>
			<div"><label><input type="radio" id="VaultOnly" name="SelectedData" value="vault" <?php if ($_GET["SelectedData"] == 'vaultOnly' || $_GET["SelectedData"] == '') {echo "checked";} ?>></label> <a href="http://helposiris.com/">HelpOsiris.com</a></div><br><br>
			<div><label><input type="radio" id="VerifiedOnly" name="SelectedData" value="verified" <?php if ($_GET["SelectedData"] == 'verifiedOnly') {echo "checked";} ?>>Explorer's Vault</label> <a href="https://docs.google.com/spreadsheets/d/1Cs-wnM6cJMfL_gpoGzqLiqtQQSEXH_eSR4T-nuyUvNs/edit#gid=0">https://docs.google.com/spreadsheets/d/1Cs-wnM6cJMfL_gpoGzqLiqtQQSEXH_eSR4T-nuyUvNs/edit#gid=0</a></div><br>
			<div><label><input type="radio" id="MasterOnly" name="SelectedData" value="master"<?php if ($_GET["SelectedData"] == 'masterOnly') {echo "checked";} ?>> Bachmanetti/Adam Algaert Compilation</label> <a href="https://docs.google.com/spreadsheets/d/1ykaQALnNF4S33ZrLeXF1RSzygCLyHugoZmgoPwogZI0/edit?usp=sharing">https://docs.google.com/spreadsheets/d/1ykaQALnNF4S33ZrLeXF1RSzygCLyHugoZmgoPwogZI0/edit?usp=sharing</a></div><br>
			<div><label><input type="radio" id="Proof" name="SelectedData" value="proof"<?php if ($_GET["SelectedData"] == 'proof') {echo "checked";} ?>>Proof</label></div><br>
			<div><label><input type="checkbox" id="ExtraSymbols" name="ExtraSymbols" <?php if ($_GET["ExtraSymbols"] == 'true') {echo "checked";} ?>>Show Side Symbols</label></div><br>
			<div style="display: none;">Min Cluster Size <input type="number" id="NodeCount" name="NodeCount" placeholder="1" value="<?php echo $_GET["NodeCount"]; ?>"></div><br>
			<!--<div>JSON Import<br><textarea id="JsonInput" name="JsonInput"></textarea></div><br>-->
			<input id="load-button" type="button" onclick="load()" value="Load Data">
		</fieldset>
	</form>
	<textarea style="display: none;" id="conflicts" cols="100" rows="10"></textarea>
	<br>
	<div id="canvases"></div>
	<script>
		class Node {
			constructor(id, subNodes, openSides, symbol=null, corridorLink) {
				this.id = id;
				this.subNodes = subNodes;
				this.openSides = openSides;
				this.symbol = symbol;
				this.corridorLink = corridorLink;
			}
		}

		class SubNode {
			constructor(code = "") {
				this.code = code;
				// this.linkedNode
			}
		}

		var linkDict = {};
		var hexArray = [];
		var height = 84;
		var width = 60;

		function load() {
			var verifiedOnly = document.getElementById("VerifiedOnly").checked;
			var vaultOnly = document.getElementById("VaultOnly").checked;
			var masterOnly = document.getElementById("MasterOnly").checked;
			var proof = document.getElementById("Proof").checked;
			var selectedData = "";
			var selectedData = verifiedOnly?"verifiedOnly":selectedData;
			var selectedData = vaultOnly?"vaultOnly":selectedData;
			var selectedData = masterOnly?"masterOnly":selectedData;
			var selectedData = proof?"proof":selectedData;

			var sideSymbols = document.getElementById("ExtraSymbols").checked;
			var clusterSize = document.getElementById("NodeCount").value?document.getElementById("NodeCount").value:1;
			window.history.pushState("", "document.title", window.location.href.split('?')[0]+"?SelectedData="+selectedData+"&ExtraSymbols="+sideSymbols+"&NodeCount="+clusterSize);

			document.getElementById("canvases").innerHTML = "";
			document.getElementById("load-button").style.display = "none";
			document.getElementById("conflicts").style.display = "none";
			document.getElementById("conflicts").innerHTML = "";
			var dataTest = [];
			var nodes = [];
			var nodeSignature = {};
			var size = sideSymbols? 80 : 10;
			hexArray = [];
			var conflictErrors = "";
			linkDict = {};

			var xmlhttp = new XMLHttpRequest();
			xmlhttp.onreadystatechange = function() {
			  if (this.readyState == 4 && this.status == 200) {
			    var data = JSON.parse(this.responseText);

			    data.feed.entry.forEach(function(sheetData){
			    	if (sheetData["gs$cell"]["col"] == 1 && sheetData["gs$cell"]["row"] >= 1) {
			    		x = sheetData.content["$t"];
			    		x = x.replace(/\\""/g, '"');
			    		try {
			    			JSON.parse(x);
			    		} catch(e) {
			    			return;
			    		}
			    		dataTest.push([JSON.parse(x), sheetData["gs$cell"]["row"]]);
			    	}
			    });

			    var headNode;
				dataTest.forEach(function(data) {
					let signature = "";
					var newSubNodes = [];
					var newOpenSides = [];

					for(i=0; i<6; i++) {
						// Place sides
						subData = data[0].nodes[i];
						linkCode = subData.join('');
						if (linkCode == "BBBBBBB" || linkCode == "") {
							var newSubNode = new SubNode();
							linkCode = "";
						} else {
							if (linkDict[i+linkCode]) {
								linkDict[i+linkCode]++;
							} else {
								linkDict[i+linkCode] = 1;
							}
							var newSubNode = new SubNode(linkCode);
						}
						signature+=linkCode;
						newSubNodes.push(newSubNode);

						if (!data[0].walls[i]) {
							signature+=i+1;
							newOpenSides.push(i+1);
						}
					}

					var newSymbol;
					switch(data[0].center) {
						case 'T':
							newSymbol = "cauldron";
							break;
						case 'C':
							newSymbol = "clover";
							break;
						case 'H':
							newSymbol = "hex";
							break;
						case 'S':
							newSymbol = "snake";
							break;
						case 'D':
							newSymbol = "diamond";
							break;
						case 'P':
							newSymbol = "plus";
							break;
						case 'B':
							newSymbol = null;
							break;
					}
					signature+=newSymbol;

					var corridors = "https://tjl.co/corridors-of-time/viewer.html#"+btoa(JSON.stringify(data));
					var newNode = new Node(data[0].row?data[0].row:data[1], newSubNodes, newOpenSides, newSymbol, corridors);
					signature = signature.toUpperCase();
					if (!nodeSignature[signature] && (data[0].status == "Verified" || !data[0].status)) {
						nodeSignature[signature] = data[1];
						if (getEdgeType(newNode) == "topRight" || getEdgeType(newNode) == "topLeft" || getEdgeType(newNode) == "bottomRight" || getEdgeType(newNode) == "bottomLeft") {
							console.log(getEdgeType(newNode));
							console.log(newNode);
						}
						if (getEdgeType(newNode) == "topLeft") {
							headNode = newNode;
						} else {
							nodes.push(newNode);
						}
					} else {
						//console.log("Duplicate found at: "+data[1]+". Original at: "+nodeSignature[signature]);
					}
				});

				nodes = shuffle(nodes);

				var canvasNum = 1;
				var cluster = [];

				var head = headNode;
				head.x = 0;
				head.y = 0;

				var preNodeLength = nodes.length;
				var hexagonNode = [];
				var simpleSubNodes = [];
				var hexArrayPiece = [];
				head.subNodes.forEach(function(subNode) {
					simpleSubNodes.push(subNode.code);
				});
				
				head.positionX = 0;
				head.positionY = 0;

				hexArrayPiece[head.positionX + "," + head.positionY] = head;
				hexagonNode.push([head.x, head.y, head.openSides, head.symbol, head.id, head.corridorLink, size, simpleSubNodes]);
				drawSolution(head, nodes, size, hexagonNode, hexArrayPiece);
				cluster.push(hexagonNode);
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
			};
			if (verifiedOnly) {
				xmlhttp.open("GET", "https://spreadsheets.google.com/feeds/cells/1h6hoBoudoR0H5OoKHEqhMKxFxLnL4jagY0aqCs7LiRE/8/public/full?alt=json", true);
				xmlhttp.send();
			} 
			else if (masterOnly) { 
				xmlhttp.open("GET", "https://spreadsheets.google.com/feeds/cells/1ykaQALnNF4S33ZrLeXF1RSzygCLyHugoZmgoPwogZI0/2/public/full?alt=json", true);
				xmlhttp.send();
			} 
			else if (proof) {
				xmlhttp.open("GET", "https://spreadsheets.google.com/feeds/cells/1bYi_-XApwf_avyIuExGULdWa4oxzLr7upelAtRq38TA/7/public/full?alt=json", true);
				xmlhttp.send();
			} else {
				xmlhttp.open("GET", "https://spreadsheets.google.com/feeds/cells/1ooekiWuath-gBkFbdWiD2zv2vXuC1jv7cmIjuyV-zkU/2/public/full?alt=json", true);
				xmlhttp.send();
			}
		}

		function getEdgeType(node) {
			edgeType = "none";
			var edgeData = {
				topLeft: {pattern: [true, true, false, false, true, true], isTrue: true},
				top1: {pattern: [true, false, false, false, false, false], isTrue: true},
				top2: {pattern: [true, true, false, false, false, true], isTrue: true},
				bottomRight: {pattern: [false, true, true, true, true, false], isTrue: true},
				bottom1: {pattern: [false, false, false, true, false, false], isTrue: true},
				bottom2: {pattern: [false, false, true, true, true, false], isTrue: true},
				right: {pattern: [false, true, true, false, false, false], isTrue: true},
				left: {pattern: [false, false, false, false, true, true], isTrue: true},
				bottomLeft: {pattern: [false, false, false, true, true, true], isTrue: true},
				topRight: {pattern: [true, true, true, false, false, false], isTrue: true}
			}
			for (key in edgeData) {
				var sideType = edgeData[key];
				for (var i=0; i < 6; i++) {
					var isBlank = node.subNodes[i].code == "";
					if (isBlank != sideType.pattern[i]) {
						sideType.isTrue = false;
					}
				}
			}
			for (key in edgeData) {
				if (edgeData[key].isTrue) {
					edgeType = key;
				}
			}
			return edgeType;
		}

		function drawCluster(hexGroup, size) {
			var padding = document.getElementById("ExtraSymbols").checked?200:100;
			var furthestRight = 0;
			var furthestLeft = 0;
			var furthestUp = 0;
			var furthestDown = 0;
			for (hexaKey in hexGroup) {
				hexa = hexGroup[hexaKey];
				if (hexa.x > 0 && hexa.x > furthestRight) {
					furthestRight = hexa.x;
				} else if (hexa.x < 0 && hexa.x < furthestLeft) {
					furthestLeft = hexa.x;
				}

				if (hexa.y > 0 && hexa.y > furthestDown) {
					furthestDown = hexa.y;
				} else if (hexa.y < 0 && hexa.y < furthestUp) {
					furthestUp = hexa.y;
				}
			}
			var offsetX = -furthestLeft + (padding/2);
			var offsetY = -furthestUp + (padding/2);
			var canvasWidth = furthestRight - furthestLeft + padding;
			var canvasHeight = furthestDown - furthestUp + padding;

			var c = document.getElementById('myCanvas');
			var canvas = document.createElement('canvas');
			canvas.width = canvasWidth;
			canvas.height = canvasHeight;
			canvas.style.border = "2px solid";
			canvas.classList.add("canvas");

			var canvasElement = document.getElementById("canvases");
			canvasElement.appendChild(canvas);

			var ctx = canvas.getContext('2d');
			ctx.fillStyle = "#ffffff";
			ctx.fillRect(0, 0, canvas.width, canvas.height);

			for (hexaKey in hexGroup) {
				hexa = hexGroup[hexaKey];
				hexa.x += offsetX;
				hexa.y += offsetY;
				//drawHexa(hexa.x+offsetX, hexa.y+offsetY, hexa[2], hexa[3], ctx, hexa[4], hexa[5], hexa[6], hexa[7], hexa[8]);
				drawHexagon(hexa, ctx, size);
			}
			ctx.font = "10px Arial";
			ctx.fillStyle = "#000000";
			//ctx.fillText(hexGroup.length, 10, 10);
		}

		function drawSolution(head, targetNodes, size, hexagonNode, hexArray) {
			for (var i=0; i<targetNodes.length; i++) {
				nodeTested = targetNodes[i];
				newX = head.x;
				newY = head.y;
				var moveSize = size + 2;
				var moveY = moveSize * Math.sqrt(3);
				var moveX = moveSize + moveSize/2;

				var foundMatch = false;
				var headXEven = (head.positionX % 2) == 0;
				var sideConcat;
				if (!head.subNodes[0].linkedNode && (head.subNodes[0].code || nodeTested.subNodes[3].code) && head.subNodes[0].code == nodeTested.subNodes[3].code) {
					newPositionX = head.positionX;
					newPositionY = head.positionY -1;

					head.subNodes[0].linkedNode = targetNodes.splice(i,1);
					nodeTested.subNodes[3].linkedNode = head;
					foundMatch = true;
					newY = newY - moveY;
					sideConcat = 0 + head.subNodes[0].code;
				} else if (!head.subNodes[1].linkedNode && (head.subNodes[1].code || nodeTested.subNodes[4].code)  && head.subNodes[1].code == nodeTested.subNodes[4].code) {
					newPositionX = head.positionX + 1;
					newPositionY = head.positionY - (headXEven?1:0);

					head.subNodes[1].linkedNode = targetNodes.splice(i,1);
					nodeTested.subNodes[4].linkedNode = head;
					foundMatch = true;
					newX = newX + moveX;
					newY = newY - (moveY/2);
					sideConcat = 1 + head.subNodes[1].code;
				} else if (!head.subNodes[2].linkedNode && (head.subNodes[2].code || nodeTested.subNodes[5].code)  && head.subNodes[2].code == nodeTested.subNodes[5].code) {
					newPositionX = head.positionX + 1;
					newPositionY = head.positionY + (!headXEven?1:0);

					head.subNodes[2].linkedNode = targetNodes.splice(i,1);
					nodeTested.subNodes[5].linkedNode = head;
					foundMatch = true;
					newX = newX + moveX;
					newY = newY + (moveY/2);
					sideConcat = 2 + head.subNodes[2].code;
				} else if (!head.subNodes[3].linkedNode && (head.subNodes[3].code || nodeTested.subNodes[0].code)  && head.subNodes[3].code == nodeTested.subNodes[0].code) {
					newPositionX = head.positionX;
					newPositionY = head.positionY + 1;

					head.subNodes[3].linkedNode = targetNodes.splice(i,1);
					nodeTested.subNodes[0].linkedNode = head;
					foundMatch = true;
					newY = newY + moveY;
					sideConcat = 3 + head.subNodes[3].code;
				} else if (!head.subNodes[4].linkedNode && (head.subNodes[4].code || nodeTested.subNodes[1].code)  && head.subNodes[4].code == nodeTested.subNodes[1].code) {
					newPositionX = head.positionX - 1;
					newPositionY = head.positionY + (!headXEven?1:0);

					head.subNodes[4].linkedNode = targetNodes.splice(i,1);
					nodeTested.subNodes[1].linkedNode = head;
					foundMatch = true;
					newX = newX - moveX;
					newY = newY + (moveY/2);
					sideConcat = 4 + head.subNodes[4].code;
				} else if (!head.subNodes[5].linkedNode && (head.subNodes[5].code || nodeTested.subNodes[2].code)  && head.subNodes[5].code == nodeTested.subNodes[2].code) {
					newPositionX = head.positionX - 1;
					newPositionY = head.positionY - (headXEven?1:0);

					head.subNodes[5].linkedNode = targetNodes.splice(i,1);
					nodeTested.subNodes[2].linkedNode = head;
					foundMatch = true;
					newX = newX - moveX;
					newY = newY - (moveY/2);
					sideConcat = 5 + head.subNodes[5].code;
				}

				sideIsUnique = linkDict[sideConcat] == 1;


				if (foundMatch) {
					nodeTested.positionX = newPositionX;
					nodeTested.positionY = newPositionY;
					nodeTested.x = newX;
					nodeTested.y = newY;
					var hexKey = nodeTested.positionX + "," + nodeTested.positionY;
					if (typeof hexArray[hexKey] !== 'undefined') {
						//hexArray[hexKey].push(nodeTested);
						targetNodes.push(nodeTested);
						conflictLog("Conflict: Overlap");

						var sideLayout = "";
						nodeTested.subNodes.forEach(function(side){
							sideLayout+=side.code+" ";
						});
						conflictLog(sideLayout + " " + nodeTested.id);

						sideLayout = "";
						hexArray[hexKey].subNodes.forEach(function(side){
							sideLayout+=side.code+" ";
						});
						conflictLog(sideLayout + " " + hexArray[hexKey].id);
						conflictLog("----------------");
					} else if (arrayCheckSides(nodeTested, hexArray, sideIsUnique)) {
						hexArray[hexKey] = nodeTested;
						try {
							var simpleSubNodes = [];
							nodeTested.subNodes.forEach(function(subNode) {
								simpleSubNodes.push(subNode.code);
							});
							hexagonNode.push([nodeTested.x, nodeTested.y, nodeTested.openSides, nodeTested.symbol, nodeTested.id, nodeTested.corridorLink, size, simpleSubNodes]);
						} catch (e) {}
						drawSolution(nodeTested, targetNodes, size, hexagonNode, hexArray);
					} else {
						nodeTested.subNodes.forEach(function(subNode) {
							subNode.linkedNode = null;
						});
						targetNodes.push(nodeTested);
					}
				}
			}

		}

		// Check sides against Hexagon Graph
		function arrayCheckSides(node, hexArray, sideIsUnique) {
			var headXEven = (node.positionX % 2) == 0;
			var hasTrue = [];
			var hasSide = [];
			var currentNeighbors = 0;
			for (var i=0; i<6; i++) {
				var hexKey;
				var k = (i+3) % 6;
				switch(i) {
					case 0:
						hexKey = node.positionX+","+(node.positionY-1);
						break;
					case 1:
						hexKey = (node.positionX + 1)+","+(node.positionY - (headXEven?1:0));
						break;
					case 2:
						hexKey = (node.positionX + 1)+","+(node.positionY + (!headXEven?1:0));
						break;
					case 3:
						hexKey = node.positionX+","+(node.positionY+1);
						break;
					case 4:
						hexKey = (node.positionX - 1)+","+(node.positionY + (!headXEven?1:0));
						break;
					case 5:
						hexKey = (node.positionX - 1)+","+(node.positionY - (headXEven?1:0));
						break;
				}
				var side = hexArray[hexKey];
				if (hexKey in hexArray) {
					hasSide[i] = true;
					currentNeighbors++;
					hasTrue[i] = false;
					bNode = side;
					if (node.subNodes[i].code == bNode.subNodes[k].code) {
						hasTrue = true;
					} else {
						// conflictLog("Conflict: Matched discrepancy");
						// conflictLog(node.id + " does not match with " + bNode.id);
						// conflictLog(node.subNodes[i].code);
						// conflictLog(bNode.subNodes[k].code);
						// conflictLog("----------------");
						return false;
					}

				}

			}

			if (sideIsUnique) {
				return true;
			} else if (!sideIsUnique && currentNeighbors>2) {
				return true;
			}
			return false;
			
		}

		// hexagon
		function drawHexagon(hexagon, ctx, size) {
			sides = document.getElementById("ExtraSymbols").checked;
			var numberOfSides = 6,
			    Xcenter = hexagon.x,
			    Ycenter = hexagon.y;
			if (hexagon.symbol) {
				if (sides) {
			      	var img = new Image();
			      	img.src = hexagon.symbol+'.png';
			      	var imgSize = 26;
			      	var savectx = ctx;
			      	img.onload = function() {
			      		try {
		  					savectx.drawImage(img, hexagon.x-imgSize/2, hexagon.y-imgSize/2, imgSize, imgSize);
			      		} catch(e) {
			      			return;
			      		}
			      	}
			      } else {
			      	ctx.fillStyle = "#000000";
			      	ctx.font = "12px Arial";
			      	var character = (hexagon.symbol!="cauldron")?hexagon.symbol.substr(0,1):"T";
			      	ctx.fillText(character, hexagon.x-5, hexagon.y+5);
			      }
			}

	     	if (sides) {
	 	     	ctx.font = "10px Arial";
		     	ctx.save();

	 	     	ctx.fillStyle = "#cc4444"
	 			ctx.fillText(hexa.subNodes[0].code, hexagon.x-25, hexagon.y-58);

	 			ctx.restore();
	 			ctx.fillStyle = "#88cc88"
	 			for(var i=0; i<hexagon.subNodes[1].code.length; i++) {
	 				var chara = hexagon.subNodes[1].code[i];
	 				ctx.fillText(chara, hexagon.x+31+(i*5), hexagon.y-60+(i*9));
	 			}

	 			ctx.restore();
	 	     	ctx.fillStyle = "#cc4444"
	  			for(var i=0; i<hexagon.subNodes[2].code.length; i++) {
	 				var chara = hexagon.subNodes[2].code[i];
	 				ctx.fillText(chara, hexagon.x+35+(i*5), hexagon.y+65-(i*9));
	 			}

	 			ctx.restore();
	 			ctx.fillStyle = "#44cc44"
	 			ctx.fillText(hexagon.subNodes[3].code, hexagon.x-25, hexagon.y+65);

	 			ctx.restore();
	 	     	ctx.fillStyle = "#cc4444"
	  			for(var i=0; i<hexagon.subNodes[4].code.length; i++) {
	 				var chara = hexagon.subNodes[4].code[i];
	 				ctx.fillText(chara, hexagon.x-72+(i*5), hexagon.y+10+(i*9));
	 			}

	 			ctx.restore();
	 			ctx.fillStyle = "#44cc44"
	   			for(var i=0; i<hexagon.subNodes[5].code.length; i++) {
	 				var chara = hexagon.subNodes[5].code[i];
	 				ctx.fillText(chara, hexagon.x-70+(i*5), hexagon.y-6-(i*9));
	 			}

	 			ctx.restore();
	     	}

	     	var currentX = Xcenter + size * Math.cos(0);
	     	var currentY = Ycenter + size *  Math.sin(0);

			for (var i = 1; i <= numberOfSides; i+=1) {
				try {
					ctx.beginPath();
				} catch (e) {
					return;
				}

				ctx.moveTo (currentX, currentY);
				var nextX = Xcenter + size * Math.cos(-i * 2 * Math.PI / numberOfSides);
				var nextY = Ycenter + size * Math.sin(i * 2 * Math.PI / numberOfSides);
				ctx.lineTo (nextX, nextY);
				currentX = nextX;
				currentY = nextY;
				ctx.lineWidth = 1;

				if (hexa.subNodes[((i+7) % 6)].code == "") {
					ctx.strokeStyle = "#00ff00";
					if (hexa.openSides.includes(((i+7) % 6) + 1)) {
						ctx.lineWidth = 3;
						ctx.strokeStyle = "#ff0000";
					}
				} else {
					ctx.strokeStyle = "#000000";
					if (hexa.openSides.includes(((i+7) % 6) + 1)) {
						ctx.strokeStyle = "#add8e6";
					}
				}

				ctx.stroke();
			}
			ctx.save();
			ctx.fillStyle = "#ff000";
			ctx.fill();
			ctx.restore();
		}

		function conflictLog(conflict, conflictErrors) {
			document.getElementById("conflicts").innerHTML += conflict + "\n";
			//console.log(conflict);
		}

		function shuffle(array) {
		  var currentIndex = array.length, temporaryValue, randomIndex;

		  // While there remain elements to shuffle...
		  while (0 !== currentIndex) {

		    // Pick a remaining element...
		    randomIndex = Math.floor(Math.random() * currentIndex);
		    currentIndex -= 1;

		    // And swap it with the current element.
		    temporaryValue = array[currentIndex];
		    array[currentIndex] = array[randomIndex];
		    array[randomIndex] = temporaryValue;
		  }

		  return array;
		}
	</script>
</body>

</html>