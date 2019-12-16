const canv = document.getElementById('canvas');
const ctx = canv.getContext('2d');

var sizeX = 4;
var sizeY = 4;

var img;
var pieces;
var width;
var height;
var pX;
var pY;
var current;
var _currentDropPiece;  

var mouse;

var m = false;
function mouseStatus(n) {
     m = n;
}

function reset() {
    sizeX = 4;
    sizeY = 4;
    onImage();
}

function setImg(nr) {
    img.src = "photos/" + nr + ".jpg";
    document.onmousemove = null;
    setSize();
}

function setSize() {
    var x = document.getElementById('x').value;
    var y = document.getElementById('y').value;
    sizeX = x != '' ? sizeX = x : sizeX = 4;
    sizeY = y != '' ? sizeY = y : sizeY = 4;
    onImage();
}

function init(){
    img = new Image();
    img.addEventListener('load', onImage, false);
    img.src = "photos/1.jpg";
    Promise.all([
        loadIMG("photos/1.jpg", "gallery", 1),
        loadIMG("photos/2.jpg", "gallery", 2),
        loadIMG("photos/3.jpg", "gallery", 3),
        loadIMG("photos/4.jpg", "gallery", 4),
        loadIMG("photos/5.jpg", "gallery", 5),
        loadIMG("photos/6.jpg", "gallery", 6),
        loadIMG("photos/7.jpg", "gallery", 7),
        loadIMG("photos/8.jpg", "gallery", 8),
        loadIMG("photos/9.jpg", "gallery", 9),
        loadIMG("photos/10.jpg", "gallery", 10),
        loadIMG("photos/11.jpg", "gallery", 11),
        loadIMG("photos/12.jpg", "gallery", 12)
    ]).then(function() {
        console.log('Wszystko z równoleglej sie załadowało!');
    }).catch(function() {
        console.log('Blad ładowania galerii rownoleglej');
    });

}

function onImage(e){
    document.onmousemove = null;
    pX = Math.floor(img.width / sizeX)
    pY = Math.floor(img.height / sizeY)
    width = pX * sizeX;
    height = pY * sizeY;
    setCanvas();
    initPuzzle();
}

function setCanvas(){
    canv.width = width;
    canv.height = height;
    canv.style.border = "1px solid black";
}

function initPuzzle(){
    pieces = [];
    mouse = {x:0,y:0};
    current = null;
    _currentDropPiece = null;
    ctx.drawImage(img, 0, 0, width, height, 0, 0, width, height);
    createTitle("Click to Start Puzzle");
    buildPieces();
}

function createTitle(msg){
    ctx.fillStyle = "#000000";
    ctx.globalAlpha = .4;
    ctx.fillRect(100,height - 40,width - 200,40);
    ctx.fillStyle = "#FFFFFF";
    ctx.globalAlpha = 1;
    ctx.textAlign = "center";
    ctx.textBaseline = "middle";
    ctx.font = "20px Arial";
    ctx.fillText(msg,width / 2,height - 20);
}

function buildPieces(){
    var i;
    var piece;
    var xPos = 0;
    var yPos = 0;
    for(i = 0; i < sizeX * sizeY; i++){
        piece = {};
        piece.sx = xPos;
        piece.sy = yPos;
        pieces.push(piece);
        xPos += pX;
        if(xPos >= width){
            xPos = 0;
            yPos += pY;
        }
    }
    document.onmousedown = shufflePuzzle;
}

function shufflePuzzle(){
    if(m) {
    pieces = shuffleArray(pieces);
    ctx.clearRect(0,0,width,height);
    var i;
    var piece;
    var xPos = 0;
    var yPos = 0;
    for(i = 0;i < pieces.length;i++){
        piece = pieces[i];
        piece.xPos = xPos;
        piece.yPos = yPos;
        ctx.drawImage(img, piece.sx, piece.sy, pX, pY, xPos, yPos, pX, pY);
        ctx.strokeRect(xPos, yPos, pX,pY);
        xPos += pX;
        if(xPos >= width){
            xPos = 0;
            yPos += pY;
        }
    }
    document.onmousedown = onPuzzleClick;
    document.onmousemove = updatePuzzle;
    current = pieces[0];
    ctx.save();
    ctx.fillStyle = '#cc0000';
    ctx.fillRect(current.xPos, current.yPos,pX, pY);
    ctx.restore();
    }
}
function onPuzzleClick(e){
    mouse.x = e.layerX * canv.width / document.getElementById("around").clientWidth;
    mouse.y = e.layerH * canv.height / document.getElementById("around").clientHeight;
    if(!current) {
        current = checkPieceClicked();
        ctx.save();
        ctx.fillStyle = '#cc0000';
        ctx.fillRect(current.xPos, current.yPos, pX, pY);
        ctx.restore();
    } else {
        pieceDropped();
    }
}

function checkPieceClicked(){
    var i;
    var piece;
    for(i = 0;i < pieces.length;i++){
        piece = pieces[i];
        if(mouse.x < piece.xPos || mouse.x > (piece.xPos + pX) || mouse.y < piece.yPos || mouse.y > (piece.yPos + pY)){
            //PIECE NOT HIT
        }
        else{
            return piece;
        }
    }
    return null;
}

function updatePuzzle(e){
    if(m) {
    _currentDropPiece = null;
    if(e.layerX || e.layerX == 0){
        mouse.x = e.layerX * canv.width / document.getElementById("around").clientWidth;
        mouse.y = e.layerY * canv.height / document.getElementById("around").clientHeight;
    }
    ctx.clearRect(0,0,width,height);
    var i;
    var piece;
    for(i = 0;i < pieces.length;i++){
        piece = pieces[i];
        ctx.drawImage(img, piece.sx, piece.sy, pX, pY, piece.xPos, piece.yPos, pX, pY);
        ctx.strokeRect(piece.xPos, piece.yPos, pX,pY);
        if(_currentDropPiece == null){
            if(mouse.x < piece.xPos || mouse.x > (piece.xPos + pX) || mouse.y < piece.yPos || mouse.y > (piece.yPos + pY)) {
                //NOT OVER
            }
            else  if (Math.sqrt(Math.pow(piece.xPos - current.xPos, 2) + Math.pow(piece.yPos - current.yPos, 2)) <= Math.max(pX, pY)){
                _currentDropPiece = piece;
                ctx.save();
                ctx.globalAlpha = .5;
                ctx.fillStyle = '#0033cc';
                ctx.fillRect(_currentDropPiece.xPos, _currentDropPiece.yPos, pX, pY);
                ctx.restore();
            }
        }
    }
    ctx.save();
    ctx.fillStyle = '#cc0000';
    ctx.fillRect(current.xPos, current.yPos,pX, pY);
    ctx.restore();
    }
}

function pieceDropped(e){
    if(_currentDropPiece != null){
        var tmp = {xPos:current.xPos,yPos:current.yPos};
        current.xPos = _currentDropPiece.xPos;
        current.yPos = _currentDropPiece.yPos;
        _currentDropPiece.xPos = tmp.xPos;
        _currentDropPiece.yPos = tmp.yPos;
    }
    resetPuzzleAndCheckWin();
}

function resetPuzzleAndCheckWin(){
    ctx.clearRect(0,0,width,height);
    var gameWin = true;
    var i;
    var piece;
    for(i = 0;i < pieces.length; i++){
        piece = pieces[i];
        ctx.drawImage(img, piece.sx, piece.sy, pX, pY, piece.xPos, piece.yPos, pX, pY);
        ctx.strokeRect(piece.xPos, piece.yPos, pX,pY);
        if(piece.xPos != piece.sx || piece.yPos != piece.sy){
            gameWin = false;
        }
    }
    if(gameWin){
        setTimeout(gameOver,500);
    } else {
        ctx.save();
        ctx.fillStyle = '#cc0000';
        ctx.fillRect(current.xPos, current.yPos,pX, pY);
        ctx.restore();
    }
}

function gameOver(){
    document.onmousedown = null;
    document.onmousemove = null;
    document.onmouseup = null;
    initPuzzle();
}

function shuffleArray(o){
    for(var j, x, i = o.length; i; j = parseInt(Math.random() * i), x = o[--i], o[i] = o[j], o[j] = x);
    return o;
}

function loadIMG(url, id, nr) {
    var P = new Promise( function (resolve, reject) {
    var parent = document.getElementById(id);
    var element = document.createElement('img');
    element.setAttribute("src", url);
    element.setAttribute("alt", url);
    element.setAttribute("style", "width: 90px; height: 90px;")
    element.setAttribute("class", "img-responsive")
    element.setAttribute("onclick", "setImg(" + nr +")")
    parent.appendChild(element);
    element.onload = function() { resolve(url); };
    element.onerror = function() { reject(url) ; };
    }
    );
    return P;
}
    