function getEdgeType(node) {
  edgeType = "none";
  let edgeData = {
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
    let sideType = edgeData[key];
    for (let i=0; i < 6; i++) {
      let isBlank = node.subNodes[i].code == "";
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
  let padding = document.getElementById("ExtraSymbols").checked?200:100;
  let furthestRight = 0;
  let furthestLeft = 0;
  let furthestUp = 0;
  let furthestDown = 0;
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
  let offsetX = -furthestLeft + (padding/2);
  let offsetY = -furthestUp + (padding/2);
  let canvasWidth = furthestRight - furthestLeft + padding;
  let canvasHeight = furthestDown - furthestUp + padding;

  let c = document.getElementById('myCanvas');
  let canvas = document.createElement('canvas');
  canvas.width = canvasWidth;
  canvas.height = canvasHeight;
  canvas.style.border = "2px solid";
  canvas.classList.add("canvas");

  let canvasElement = document.getElementById("canvases");
  canvasElement.appendChild(canvas);

  let ctx = canvas.getContext('2d');
  ctx.fillStyle = "#ffffff";
  ctx.fillRect(0, 0, canvas.width, canvas.height);

  for (hexaKey in hexGroup) {
    hexa = hexGroup[hexaKey];
    hexa.x += offsetX;
    hexa.y += offsetY;
    drawHexagon(hexa, ctx, size);
  }
  // ctx.font = "10px Arial";
  // ctx.fillStyle = "#000000";
  //ctx.fillText(hexGroup.length, 10, 10);
}


function drawSolution(head, targetNodes, size, hexArray) {
  for (let i=0; i<targetNodes.length; i++) {
    nodeTested = targetNodes[i];
    newX = head.x;
    newY = head.y;
    let moveSize = size + 2;
    let moveY = moveSize * Math.sqrt(3);
    let moveX = moveSize + moveSize/2;

    let foundMatch = false;
    let headXEven = (head.positionX % 2) == 0;
    let sideConcat;
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
      let hexKey = nodeTested.positionX + "," + nodeTested.positionY;
      if (typeof hexArray[hexKey] !== 'undefined') {
        //hexArray[hexKey].push(nodeTested);
        targetNodes.push(nodeTested);
        conflictLog("Conflict: Overlap");

        let sideLayout = "";
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
          let simpleSubNodes = [];
          nodeTested.subNodes.forEach(function(subNode) {
            simpleSubNodes.push(subNode.code);
          });
        } catch (e) {}
        drawSolution(nodeTested, targetNodes, size, hexArray);
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
  let headXEven = (node.positionX % 2) == 0;
  let hasTrue = [];
  let hasSide = [];
  let currentNeighbors = 0;
  for (let i=0; i<6; i++) {
    let hexKey;
    let k = (i+3) % 6;
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
    let side = hexArray[hexKey];
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
  let numberOfSides = 6,
      Xcenter = hexagon.x,
      Ycenter = hexagon.y;
  if (hexagon.symbol) {
    if (sides) {
          let img = new Image();
          img.src = hexagon.symbol+'.png';
          let imgSize = 26;
          let savectx = ctx;
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
          let character = (hexagon.symbol!="cauldron")?hexagon.symbol.substr(0,1):"T";
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
      for(let i=0; i<hexagon.subNodes[1].code.length; i++) {
        let chara = hexagon.subNodes[1].code[i];
        ctx.fillText(chara, hexagon.x+31+(i*5), hexagon.y-60+(i*9));
      }

      ctx.restore();
        ctx.fillStyle = "#cc4444"
      for(let i=0; i<hexagon.subNodes[2].code.length; i++) {
        let chara = hexagon.subNodes[2].code[i];
        ctx.fillText(chara, hexagon.x+35+(i*5), hexagon.y+65-(i*9));
      }

      ctx.restore();
      ctx.fillStyle = "#44cc44"
      ctx.fillText(hexagon.subNodes[3].code, hexagon.x-25, hexagon.y+65);

      ctx.restore();
        ctx.fillStyle = "#cc4444"
      for(let i=0; i<hexagon.subNodes[4].code.length; i++) {
        let chara = hexagon.subNodes[4].code[i];
        ctx.fillText(chara, hexagon.x-72+(i*5), hexagon.y+10+(i*9));
      }

      ctx.restore();
      ctx.fillStyle = "#44cc44"
      for(let i=0; i<hexagon.subNodes[5].code.length; i++) {
        let chara = hexagon.subNodes[5].code[i];
        ctx.fillText(chara, hexagon.x-70+(i*5), hexagon.y-6-(i*9));
      }

      ctx.restore();
  }

  let currentX = Xcenter + size * Math.cos(0);
  let currentY = Ycenter + size *  Math.sin(0);

  for (let i = 1; i <= numberOfSides; i+=1) {
    try {
      ctx.beginPath();
    } catch (e) {
      return;
    }

    ctx.moveTo (currentX, currentY);
    let nextX = Xcenter + size * Math.cos(-i * 2 * Math.PI / numberOfSides);
    let nextY = Ycenter + size * Math.sin(i * 2 * Math.PI / numberOfSides);
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

function shuffle(array) {
  let currentIndex = array.length, temporaryValue, randomIndex;

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

function conflictLog(conflict, conflictErrors) {
  document.getElementById("conflicts").innerHTML += conflict + "\n";
  //console.log(conflict);
}