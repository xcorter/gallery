// Стандарный input для файлов
var fileInput = document.getElementById('file-field');

// ul-список, содержащий миниатюрки выбранных файлов
var imgList = document.getElementById('img-list');

// Контейнер, куда можно помещать файлы методом drag and drop
var dropBox = document.getElementById('img-container');

var url = "upload.php";

// отправка файлов
function sendImages() { 
    var ul = document.getElementById("img-list");
    for (i = 0; i < ul.childNodes.length; i++) {
        //alert(1)
        var li = ul.childNodes[i];
        uploadFile(li, url);
    }
}

// очистить список файлов
function clearAll() {
    document.getElementById('img-list').innerHTML = "";
}


if (fileInput) {
    fileInput.addEventListener("change", function(e) {
        displayFiles(this.files);
    });
}

// отображение списка файлов
function displayFiles(files) {
    for (i = 0; i < files.length; i++){
        if (!files[i].type.match(/image.*/)) { // check type of file
            return true;
        }           
        // Создаем элемент li и помещаем в него название и progress bar,
        // а также создаем ему свойство file, куда помещаем объект File (при загрузке понадобится)
        var li=document.createElement("li");
        imgList.appendChild(li)
        var div = document.createElement("div");
        var filename = document.createTextNode(files[i].name)
        div.appendChild(filename)
        li.appendChild(div);
        var div_progress = document.createElement("div");
        div_progress.setAttribute('class', 'progress');
        var progressText = document.createTextNode("0%");
        div_progress.appendChild(progressText)
        li.appendChild(div_progress);
        li.file = files[i];
    
        var reader = new FileReader();
        reader.onload = (function(aImg) {
            return function(e) {
                aImg.setAttribute('src', e.target.result);
                aImg.setAttribute('width', 150);
            };
        });
    
        reader.readAsDataURL(files[i]);
    }
}

// обработка ajax ответа
function resultProcess(resp, li) {
    resp = eval(resp);
    if (resp[0]["error"]) {
        console.log(li.lastChild);
        li.lastChild.innerHTML = resp[0]["error"];
    } else {
        var gallery = document.getElementById("gallery");
        var a = document.createElement("a");
        a.setAttribute("href", resp[0]["response"]);
        a.setAttribute("target", "_blank");
        var img = document.createElement("img");
        img.setAttribute("src", resp[0]["response"]);
        img.setAttribute("width", 150);
        a.appendChild(img);
        gallery.appendChild(a);
        li.lastChild.innerHTML = "Complete";
    }
}

// загрузить файл
function uploadFile(li, url) {
    var file = li.file
    var reader = new FileReader();
 
    reader.onload = function() {    
        var xhr = new XMLHttpRequest();    
    
        xhr.upload.addEventListener("progress", function(e) {
            if (e.lengthComputable) {
                var progress = (e.loaded * 100) / e.total;
                
                li.lastChild.innerHTML = Number(progress).toFixed(2);
            }
        }, false);
 
        xhr.onreadystatechange = function () {
            if (this.readyState == 4) {
                if(this.status == 200) {
                    resultProcess(this.responseText, li);
                } else {
                    /* error */
                }
            }
        };
    
        xhr.open("POST", url);
        var boundary = "xxxxxxxxx";    
        xhr.setRequestHeader("Content-Type", "multipart/form-data, boundary="+boundary);
        xhr.setRequestHeader("Cache-Control", "no-cache");    
        // Формируем тело запроса
        var body = "--" + boundary + "\r\n";
        body += "Content-Disposition: form-data; name='image'; filename='" + file.name + "'\r\n";
        body += "Content-length: "+file.size+"\r\n";
        body += "Content-Type: "+file.type+"\r\n\r\n";
        body += reader.result + "\r\n";
        body += "--" + boundary + "--";
 
        if(xhr.sendAsBinary) {
            // только для firefox
            xhr.sendAsBinary(body);
        } else {
            // chrome (так гласит спецификация W3C)
            xhr.send(body);
        }
    };
    // Читаем файл
    reader.readAsBinaryString(file);
}