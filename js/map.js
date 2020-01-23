function drawSolution(head, targetNodes, size, hexArray, linkDict) {
  for (let i=0; i<targetNodes.length; i++) {
    nodeTested = targetNodes[i];

    let foundMatch = false;
    let headXEven = (head.x % 2) == 0;
    let sideConcat;
    if (!head.subNodes[0].linkedNode && (head.subNodes[0].code || nodeTested.subNodes[3].code) && head.subNodes[0].code == nodeTested.subNodes[3].code) {
      newx = head.x;
      newy = head.y -1;

      head.subNodes[0].linkedNode = targetNodes.splice(i,1);
      nodeTested.subNodes[3].linkedNode = head;
      foundMatch = true;
      sideConcat = 0 + head.subNodes[0].code;
    } else if (!head.subNodes[1].linkedNode && (head.subNodes[1].code || nodeTested.subNodes[4].code)  && head.subNodes[1].code == nodeTested.subNodes[4].code) {
      newx = head.x + 1;
      newy = head.y - (headXEven?1:0);

      head.subNodes[1].linkedNode = targetNodes.splice(i,1);
      nodeTested.subNodes[4].linkedNode = head;
      foundMatch = true;
      sideConcat = 1 + head.subNodes[1].code;
    } else if (!head.subNodes[2].linkedNode && (head.subNodes[2].code || nodeTested.subNodes[5].code)  && head.subNodes[2].code == nodeTested.subNodes[5].code) {
      newx = head.x + 1;
      newy = head.y + (!headXEven?1:0);

      head.subNodes[2].linkedNode = targetNodes.splice(i,1);
      nodeTested.subNodes[5].linkedNode = head;
      foundMatch = true;
      sideConcat = 2 + head.subNodes[2].code;
    } else if (!head.subNodes[3].linkedNode && (head.subNodes[3].code || nodeTested.subNodes[0].code)  && head.subNodes[3].code == nodeTested.subNodes[0].code) {
      newx = head.x;
      newy = head.y + 1;

      head.subNodes[3].linkedNode = targetNodes.splice(i,1);
      nodeTested.subNodes[0].linkedNode = head;
      foundMatch = true;
      sideConcat = 3 + head.subNodes[3].code;
    } else if (!head.subNodes[4].linkedNode && (head.subNodes[4].code || nodeTested.subNodes[1].code)  && head.subNodes[4].code == nodeTested.subNodes[1].code) {
      newx = head.x - 1;
      newy = head.y + (!headXEven?1:0);

      head.subNodes[4].linkedNode = targetNodes.splice(i,1);
      nodeTested.subNodes[1].linkedNode = head;
      foundMatch = true;
      sideConcat = 4 + head.subNodes[4].code;
    } else if (!head.subNodes[5].linkedNode && (head.subNodes[5].code || nodeTested.subNodes[2].code)  && head.subNodes[5].code == nodeTested.subNodes[2].code) {
      newx = head.x - 1;
      newy = head.y - (headXEven?1:0);

      head.subNodes[5].linkedNode = targetNodes.splice(i,1);
      nodeTested.subNodes[2].linkedNode = head;
      foundMatch = true;
      sideConcat = 5 + head.subNodes[5].code;
    }

    sideIsUnique = linkDict[sideConcat] == 1;

    if (foundMatch) {
      nodeTested.x = newx;
      nodeTested.y = newy;
      let hexKey = nodeTested.x + "," + nodeTested.y;
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
      } else if (arrayCheckSides(nodeTested, hexArray, sideIsUnique) && validPlacement(nodeTested)) {
        hexArray[hexKey] = nodeTested;
        drawSolution(nodeTested, targetNodes, size, hexArray, linkDict);
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
  let headXEven = (node.x % 2) == 0;
  let hasTrue = [];
  let hasSide = [];
  let currentNeighbors = 0;
  for (let i=0; i<6; i++) {
    let hexKey;
    let k = (i+3) % 6;
    switch(i) {
      case 0:
        hexKey = node.x+","+(node.y-1);
        break;
      case 1:
        hexKey = (node.x + 1)+","+(node.y - (headXEven?1:0));
        break;
      case 2:
        hexKey = (node.x + 1)+","+(node.y + (!headXEven?1:0));
        break;
      case 3:
        hexKey = node.x+","+(node.y+1);
        break;
      case 4:
        hexKey = (node.x - 1)+","+(node.y + (!headXEven?1:0));
        break;
      case 5:
        hexKey = (node.x - 1)+","+(node.y - (headXEven?1:0));
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
  } else if (!sideIsUnique && currentNeighbors>1) {
    return true;
  }
  return false;
  
}

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

// Valid placement based on COT rectangle shape
// Assumes placement based on Top Left Corner as (0,0)
function validPlacement(node) {
  let height = 84;
  let width = 60;
  let edgeType = getEdgeType(node);
  let isValid;

  switch(edgeType) {
    case "topLeft":
      isValid = node.x == 0 && node.y == 0;
      break;
    case "bottomRight":
      isValid = node.x == width-1 && node.y == height-1;
      break;
    case "bottomLeft":
      isValid = node.x == 0 && node.y == height-1;
      break;
    case "topRight":
      isValid = node.x == width-1 && node.y == 0;
      break;
    case "top1":
      isValid  = node.x%2 == 1 && node.y == 0;
      break;
    case "top2":
      isValid  = node.x%2 == 0 && node.y == 0;
      break;
    case "bottom1":
      isValid  = node.x%2 == 0 && node.y == height-1;
      break;
    case "bottom2":
      isValid  = node.x%2 == 1 && node.y == height-1;
      break;
    case "right":
      isValid  = node.x == width-1;
      break;
    case "left":
      isValid = node.x == 0;
      break;
    default:
      isValid = node.x > 0 && node.x < width-1 && node.y > 0 && node.y < height-1;
      break;
  }
  return isValid;
}