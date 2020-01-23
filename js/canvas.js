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

// hexagon
function drawHexagon(hexagon, ctx, size) {
  sides = document.getElementById("ExtraSymbols").checked;
  let numberOfSides = 6,
      Xcenter = hexagon.x,
      Ycenter = hexagon.y;
  if (hexagon.symbol) {
    let img = new Image();
    img.src = "/cot/img/"+hexagon.symbol+".png";
    let imgSize = sides?26:15;
    let savectx = ctx;
    img.onload = function() {
      try {
        savectx.drawImage(img, hexagon.x-imgSize/2, hexagon.y-imgSize/2, imgSize, imgSize);
      } catch(e) {
        return;
      }
    }
  }
  // ctx.fillStyle = "#000000";
  // ctx.font = "10px Arial";
  // ctx.fillText("(" + hexagon.positionX + "," + hexagon.positionY + ")", hexagon.x-10, hexagon.y);

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

    if (hexagon.subNodes[((i+7) % 6)].code == "") {
      ctx.strokeStyle = "#00ff00";
      if (hexagon.openSides.includes(((i+7) % 6) + 1)) {
        ctx.lineWidth = 3;
        ctx.strokeStyle = "#ff0000";
      }
    } else {
      ctx.strokeStyle = "#000000";
      if (hexagon.openSides.includes(((i+7) % 6) + 1)) {
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