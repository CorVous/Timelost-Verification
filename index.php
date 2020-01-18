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
			<div style="display: none;"><label><input type="radio" id="VaultOnly" name="SelectedData" value="vault" <?php if ($_GET["SelectedData"] == 'vaultOnly') {echo "checked";} ?>>Vault Data</label> <a href="https://docs.google.com/spreadsheets/d/1bYi_-XApwf_avyIuExGULdWa4oxzLr7upelAtRq38TA/edit#gid=710933392">https://docs.google.com/spreadsheets/d/1bYi_-XApwf_avyIuExGULdWa4oxzLr7upelAtRq38TA/edit#gid=710933392</a><br></div>
			<div><label><input type="radio" id="VerifiedOnly" name="SelectedData" value="verified" <?php if ($_GET["SelectedData"] == 'verifiedOnly' || $_GET["SelectedData"] == '') {echo "checked";} ?>>Cor's Compiled Masterlist</label> <a href="https://docs.google.com/spreadsheets/d/1Z16lZS_Uo-Go9hyxxY3oZlFqObkGpgmThSvJUrwSBFk/edit?usp=sharing">https://docs.google.com/spreadsheets/d/1Z16lZS_Uo-Go9hyxxY3oZlFqObkGpgmThSvJUrwSBFk/edit?usp=sharing</a></div><br>
			<div style="display: none;"><label><input type="radio" id="MasterOnly" name="SelectedData" value="master"<?php if ($_GET["SelectedData"] == 'masterOnly') {echo "checked";} ?>>Unfiltered Compiled Data (1/17/2020 3:27 AM PST)</label> <a href="https://docs.google.com/spreadsheets/d/1bYi_-XApwf_avyIuExGULdWa4oxzLr7upelAtRq38TA/edit#gid=1755434390">https://docs.google.com/spreadsheets/d/1bYi_-XApwf_avyIuExGULdWa4oxzLr7upelAtRq38TA/edit#gid=1755434390</a><br></div>
			<div><label><input type="radio" id="Proof" name="SelectedData" value="proof"<?php if ($_GET["SelectedData"] == 'Proof') {echo "checked";} ?>>Proof</label></div><br>
			<div><label><input type="checkbox" id="ExtraSymbols" name="ExtraSymbols" <?php if ($_GET["ExtraSymbols"] == 'true') {echo "checked";} ?>>Show Side Symbols</label></div><br>
			<div>Min Cluster Size <input type="number" id="NodeCount" name="NodeCount" placeholder="2" value="<?php echo $_GET["NodeCount"]; ?>"></div><br>
			<!--<div>JSON Import<br><textarea id="JsonInput" name="JsonInput"></textarea></div><br>-->
			<input id="load-button" type="button" onclick="load()" value="Load Data">
		</fieldset>
	</form>
	<textarea id="json-export" style="display: none;" cols="100" rows="10"></textarea>
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
			}
		}

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
			var clusterSize = document.getElementById("NodeCount").value?document.getElementById("NodeCount").value:2;
			window.history.pushState("", "document.title", window.location.href.split('?')[0]+"?SelectedData="+selectedData+"&ExtraSymbols="+sideSymbols+"&NodeCount="+clusterSize);

			document.getElementById("canvases").innerHTML = "";
			document.getElementById("load-button").style.display = "none";
			document.getElementById("json-export").style.display = "none";
			var dataTest = [];
			var nodes = [];
			var nodeSignature = {};
			var size = sideSymbols? 80 : 30;

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

				dataTest.forEach(function(data) {
					let signature = "";
					var newSubNodes = [];
					try {
						data[0].nodes.forEach(function(subData){
							if (subData.join('') == "BBBBBBB") {
								var newSubNode = new SubNode();
							} else {
								var newSubNode = new SubNode(subData.join(''));
							}
							signature+=subData.join('');
							newSubNodes.push(newSubNode);
						});
					} catch(e) {
						return;
					}
					
					var newOpenSides = [];
					for(i=0; i<6; i++) {
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
					var newNode = new Node(data[1], newSubNodes, newOpenSides, newSymbol, corridors);
					signature = signature.toUpperCase();
					if (!nodeSignature[signature]) {
						nodeSignature[signature] = data[1];
						nodes.push(newNode);
					} else {
						//console.log("Duplicate found at: "+data[1]+". Original at: "+nodeSignature[signature]);
					}
				});

				var canvasNum = 1;
				var cluster = [];
				while(nodes.length > 0) {
					var head = nodes.shift();
					head.x = 0;
					head.y = 0;

					var preNodeLength = nodes.length;
					var hexagonNode = [];
					var simpleSubNodes = [];
					head.subNodes.forEach(function(subNode) {
						simpleSubNodes.push(subNode.code);
					});
					hexagonNode.push([head.x, head.y, head.openSides, head.symbol, head.id, head.corridorLink, size, simpleSubNodes]);
					drawSolution(head, nodes, size, hexagonNode);
					cluster.push(hexagonNode);
					canvasNum++;
				}

				// Draw
				cluster.forEach(function(clust) {
					if ((!clusterSize && clust.length > 1) || clust.length >= clusterSize) {
						//var canvasLength = (sideSymbols?1000:300) + ((sideSymbols?90:70) * Math.log(clust.length) * 9) + ((clust.length>200 && sideSymbols)?1000:0) + ((clust.length>400 && sideSymbols)?2000:0);

						var padding = document.getElementById("ExtraSymbols").checked?200:100;
						var furthestRight = 0;
						var furthestLeft = 0;
						var furthestUp = 0;
						var furthestDown = 0;
						clust.forEach(function(hexa) {
							if (hexa[0] > 0 && hexa[0] > furthestRight) {
								furthestRight = hexa[0];
							} else if (hexa[0] < 0 && hexa[0] < furthestLeft) {
								furthestLeft = hexa[0];
							}

							if (hexa[1] > 0 && hexa[1] > furthestDown) {
								furthestDown = hexa[1];
							} else if (hexa[1] < 0 && hexa[1] < furthestUp) {
								furthestUp = hexa[1];
							}
						});

						var offsetX = -furthestLeft + (padding/2);
						var offestY = -furthestUp + (padding/2);
						var canvasWidth = furthestRight - furthestLeft + padding;
						var canvasHeight = furthestDown - furthestUp + padding;

						var c = document.getElementById('myCanvas');
						var canvas = document.createElement('canvas');
						canvas.width = canvasWidth;
						canvas.height = canvasHeight;
						canvas.style.border = "1px solid";
						canvas.classList.add("canvas");
						canvas.classList.add("nodes-"+(clust.length));


						var canvasElement = document.getElementById("canvases");
						canvasElement.appendChild(canvas);

						var ctx = canvas.getContext('2d');
						ctx.fillStyle = "#ffffff";
						ctx.fillRect(0, 0, canvas.width, canvas.height);

						clust.forEach(function(hexa) {
							drawHexagon(hexa[0]+offsetX, hexa[1]+offestY, hexa[2], hexa[3], ctx, hexa[4], hexa[5], hexa[6], hexa[7], hexa[8]);
						});
					}

				});

				document.getElementById("load-button").style.display = "block";
				//document.getElementById("json-export").style.display = "block";
				document.getElementById("json-export").innerHTML = JSON.stringify(cluster);
			  }
			};
			// if (document.getElementById("JsonInput").value) {
			// 	// Draw from JSON Text
			// 	cluster = JSON.parse(document.getElementById("JsonInput").value);
			// 	cluster.forEach(function(clust) {
			// 		if ((!clusterSize && clust.length > 1) || clust.length >= clusterSize) {
			// 			var canvasLength = (sideSymbols?1000:300) + ((sideSymbols?80:50) * (clusterSize-2));
			// 			var c = document.getElementById('myCanvas');
			// 			var canvas = document.createElement('canvas');
			// 			canvas.width = canvasLength;
			// 			canvas.height = canvasLength;
			// 			canvas.style.border = "1px solid";
			// 			canvas.classList.add("canvas");
			// 			canvas.classList.add("nodes-"+(clust.length));

			// 			var canvasElement = document.getElementById("canvases");
			// 			canvasElement.appendChild(canvas);

			// 			var ctx = canvas.getContext('2d');

			// 			clust.forEach(function(hexa) {
			// 				drawHexagon(hexa[0]+(canvasLength/2), hexa[1]+(canvasLength/2), hexa[2], hexa[3], ctx, hexa[4], hexa[5], hexa[6], hexa[7], hexa[8]);
			// 			});
			// 		}

			// 	});
			// } else 
			if (verifiedOnly) {
				xmlhttp.open("GET", "https://spreadsheets.google.com/feeds/cells/1Z16lZS_Uo-Go9hyxxY3oZlFqObkGpgmThSvJUrwSBFk/4/public/full?alt=json", true);
				xmlhttp.send();
			} 
			else if (masterOnly) { 
				xmlhttp.open("GET", "https://spreadsheets.google.com/feeds/cells/1bYi_-XApwf_avyIuExGULdWa4oxzLr7upelAtRq38TA/5/public/full?alt=json", true);
				xmlhttp.send();
			} 
			else if (proof) {
				xmlhttp.open("GET", "https://spreadsheets.google.com/feeds/cells/1bYi_-XApwf_avyIuExGULdWa4oxzLr7upelAtRq38TA/6/public/full?alt=json", true);
				xmlhttp.send();
			} else {
				xmlhttp.open("GET", "https://spreadsheets.google.com/feeds/cells/1bYi_-XApwf_avyIuExGULdWa4oxzLr7upelAtRq38TA/1/public/full?alt=json", true);
				xmlhttp.send();
			}
		}
		function drawSolution(head, targetNodes, size, hexagonNode) {
			for (var i=0; i<targetNodes.length; i++) {
				nodeTested = targetNodes[i];
				newX = head.x;
				newY = head.y;
				var moveSize = size + 2;
				var moveY = moveSize * Math.sqrt(3);
				var moveX = moveSize + moveSize/2;
				var foundMatch = false;
				if (!head.subNodes[0].linkedNode && (head.subNodes[0].code || nodeTested.subNodes[3].code) && head.subNodes[0].code == nodeTested.subNodes[3].code) {
					head.subNodes[0].linkedNode = targetNodes.splice(i,1);
					nodeTested.subNodes[3].linkedNode = head;
					foundMatch = true;
					newY = newY - moveY;
				} else if (!head.subNodes[1].linkedNode && (head.subNodes[1].code || nodeTested.subNodes[4].code)  && head.subNodes[1].code == nodeTested.subNodes[4].code) {
					head.subNodes[1].linkedNode = targetNodes.splice(i,1);
					nodeTested.subNodes[4].linkedNode = head;
					foundMatch = true;
					newX = newX + moveX;
					newY = newY - (moveY/2);
				} else if (!head.subNodes[2].linkedNode && (head.subNodes[2].code || nodeTested.subNodes[5].code)  && head.subNodes[2].code == nodeTested.subNodes[5].code) {
					head.subNodes[2].linkedNode = targetNodes.splice(i,1);
					nodeTested.subNodes[5].linkedNode = head;
					foundMatch = true;
					newX = newX + moveX;
					newY = newY + (moveY/2);
				} else if (!head.subNodes[3].linkedNode && (head.subNodes[3].code || nodeTested.subNodes[0].code)  && head.subNodes[3].code == nodeTested.subNodes[0].code) {
					head.subNodes[3].linkedNode = targetNodes.splice(i,1);
					nodeTested.subNodes[0].linkedNode = head;
					foundMatch = true;
					newY = newY + moveY;
				} else if (!head.subNodes[4].linkedNode && (head.subNodes[4].code || nodeTested.subNodes[1].code)  && head.subNodes[4].code == nodeTested.subNodes[1].code) {
					head.subNodes[4].linkedNode = targetNodes.splice(i,1);
					nodeTested.subNodes[1].linkedNode = head;
					foundMatch = true;
					newX = newX - moveX;
					newY = newY + (moveY/2);
				} else if (!head.subNodes[5].linkedNode && (head.subNodes[5].code || nodeTested.subNodes[2].code)  && head.subNodes[5].code == nodeTested.subNodes[2].code) {
					head.subNodes[5].linkedNode = targetNodes.splice(i,1);
					nodeTested.subNodes[2].linkedNode = head;
					foundMatch = true;
					newX = newX - moveX;
					newY = newY - (moveY/2);
				}

				if (foundMatch) {
					nodeTested.x = newX;
					nodeTested.y = newY;
					try {
						var simpleSubNodes = [];
						nodeTested.subNodes.forEach(function(subNode) {
							simpleSubNodes.push(subNode.code);
						});
						hexagonNode.push([nodeTested.x, nodeTested.y, nodeTested.openSides, nodeTested.symbol, nodeTested.id, nodeTested.corridorLink, size, simpleSubNodes]);
					} catch (e) {}
					drawSolution(nodeTested, targetNodes, size, hexagonNode);
				}
			}


		}

		// hexagon
		function drawHexagon(x, y, omittedSides, symbol, ctx, id, link, size, subNodes) {
			sides = document.getElementById("ExtraSymbols").checked;
			var numberOfSides = 6,
			    Xcenter = x,
			    Ycenter = y;
			if (symbol) {
		      	var img = new Image();
		      	img.src = symbol+'.png';
		      	var imgSize = 26;
		      	var savectx = ctx;
		      	img.onload = function() {
		      		try {
	  					savectx.drawImage(img, x-imgSize/2, y-imgSize/2, imgSize, imgSize);
		      		} catch(e) {
		      			return;
		      		}
		      	}
			}

			try {
				ctx.fillStyle = "#000000"
				ctx.font = "12px Arial";
				ctx.fillText(id, x-10, y-14);
			} catch(e) {
				return;
			}

	     	var currentX = Xcenter + size * Math.cos(0);
	     	var currentY = Ycenter + size *  Math.sin(0);

	     	if (sides) {
	 	     	ctx.font = "10px Arial";
		     	ctx.save();

	 	     	ctx.fillStyle = "#cc4444"
	 			ctx.fillText(subNodes[0], x-25, y-58);

	 			ctx.restore();
	 			ctx.fillStyle = "#88cc88"
	 			for(var i=0; i<subNodes[1].length; i++) {
	 				var chara = subNodes[1][i];
	 				ctx.fillText(chara, x+31+(i*5), y-60+(i*9));
	 			}

	 			ctx.restore();
	 	     	ctx.fillStyle = "#cc4444"
	  			for(var i=0; i<subNodes[2].length; i++) {
	 				var chara = subNodes[2][i];
	 				ctx.fillText(chara, x+35+(i*5), y+65-(i*9));
	 			}

	 			ctx.restore();
	 			ctx.fillStyle = "#44cc44"
	 			ctx.fillText(subNodes[3], x-25, y+65);

	 			ctx.restore();
	 	     	ctx.fillStyle = "#cc4444"
	  			for(var i=0; i<subNodes[4].length; i++) {
	 				var chara = subNodes[4][i];
	 				ctx.fillText(chara, x-72+(i*5), y+10+(i*9));
	 			}

	 			ctx.restore();
	 			ctx.fillStyle = "#44cc44"
	   			for(var i=0; i<subNodes[5].length; i++) {
	 				var chara = subNodes[5][i];
	 				ctx.fillText(chara, x-70+(i*5), y-6-(i*9));
	 			}

	 			ctx.restore();
	     	}

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
				ctx.strokeStyle = "#000000";

				ctx.lineWidth = 1;
				if ( omittedSides.includes(((i+7) % 6) + 1) ) {
					ctx.strokeStyle = "#add8e6";
				}
				ctx.stroke();
			}
		}


	</script>
</body>

</html>