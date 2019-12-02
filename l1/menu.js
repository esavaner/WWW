
var items = [
    {href: "index.html", text: "START"},
    {href: "about.html", text: "O MNIE"},
    {href: "projects.html", text: "PROJEKTY"}
];

var divMain = document.getElementById("divmain");

var div = document.createElement("div");
div.className = "main main-3";

var button1 = document.createElement("a");
button1.href = items[0].href;
var node1 = document.createTextNode(items[0].text);
button1.appendChild(node1);
div.appendChild(button1);

var button2 = document.createElement("a");
button2.href = items[1].href;
var node2 = document.createTextNode(items[1].text);
button2.appendChild(node2);
div.appendChild(button2);

var button3 = document.createElement("a");
button3.href = items[2].href;
var node3 = document.createTextNode(items[2].text);
button3.appendChild(node3);
div.appendChild(button3);

div.children[0].className = "button selected";
div.children[1].className = "button";
div.children[2].className = "button";

var aside = document.createElement("aside");
aside.className = "aside aside-3";

var img = document.createElement("img");
img.src = "pwr.jpg";
img.className = "img2";

aside.appendChild(img);

divMain.appendChild(aside);
divMain.appendChild(div);