function processData(dataTest) {
  let hexNodes = [];
  let hexSignatures = [];
  let linkDict = {};
  let head;
  let id = 0;
  dataTest.data.forEach(function(data) {
    if (data.length == 11) {
      let convertedNode = convertToNode(id, data, linkDict);
      let newNode = convertedNode[0];
      let signature = convertedNode[1];
      linkDict = convertedNode[2];
      if (!hexSignatures[signature]) {
        hexSignatures[signature] = id;
        if (getEdgeType(newNode) == "topLeft") {
          head = newNode;
          head.x = 0;
          head.y = 0;
        } else {
          hexNodes.push(newNode);
        }
      } else {
        conflictLog("Duplicate found at: "+id+". Original at: "+hexSignatures[signature]);
        conflictLog("----------------");
      }

      id++;
    }
  });
  return [hexNodes, head, linkDict];
}

function convertToNode(id, data, linkDict) {
  let signature = "";
  let newSubNodes = [];

  let newOpenSides = data[2].split(',');
  for (let i in newOpenSides) {
    newOpenSides[i] = parseInt(newOpenSides[i]);
  }
  signature+=newOpenSides.sort().join('');

  for(i=0; i<6; i++) {
    // Place sides
    linkCode = data[i+3];
    let newSubNode = new SubNode();
    if (linkCode == "BBBBBBB" || linkCode == "") {
      linkCode = "";
      signature+="BBBBBBB";
    } else {
      if (linkDict[i+linkCode]) {
        linkDict[i+linkCode]++;
      } else {
        linkDict[i+linkCode] = 1;
      }
      newSubNode.code = linkCode;
    }
    signature+=linkCode;
    newSubNodes.push(newSubNode);
  }

  let dataSymbol = data[1].toLowerCase().trim()
  let newSymbol = dataSymbol == "blank"?null:dataSymbol;
  signature+=newSymbol;

  let newNode = new Node(id, newSubNodes, newOpenSides, newSymbol, data[0]);
  signature = signature.toUpperCase();
  return [newNode, signature, linkDict]
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

function conflictLog(conflict) {
  document.getElementById("conflicts").innerHTML += conflict + "\n";
  //console.log(conflict);
}

function download(content, fileName, contentType) {
    var a = document.createElement("a");
    var file = new Blob([content], {type: contentType});
    a.href = URL.createObjectURL(file);
    a.download = fileName;
    a.click();
}