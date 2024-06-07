
// Button re-directions
if(document.getElementById("startReadingBtn")){
    document.getElementById("startReadingBtn").addEventListener("click", function() {
        window.location.href = "blogs.php";
    });
}

if(document.getElementById("signUpButton")){
    document.getElementById("signUpButton").addEventListener("click",function(){
        window.location.href = "register.php";
    });
}
if(document.getElementById("createButton")){
    document.getElementById("createButton").addEventListener("click",function(){
        window.location.href = "create_blog.php"
    });
}
if(document.getElementById("readMore")){
    document.getElementById("readMore").addEventListener("click", function() {
        window.location.href = "blogs.php";
    });
}
//Image re-directions
if(document.getElementById("accountLogo")){
    document.getElementById("accountLogo").addEventListener("click",function(){
        window.location.href = "settings.php"
    })
}

if(document.getElementById("logo-header")){
    document.getElementById("logo-header").addEventListener("click",function(){
        window.location.href = "index.php"
    })
}

if(document.getElementById("facebook")){
    document.getElementById("facebook").addEventListener("click",function(){
        window.location.href = "https://facebook.com"
    })
}

if(document.getElementById("instagram")){
    document.getElementById("instagram").addEventListener("click",function(){
        window.location.href = "https://instagram.com"
    })
}

if(document.getElementById("telegram")){
    document.getElementById("telegram").addEventListener("click",function(){
        window.location.href = "https://telegram.org"
    })
}