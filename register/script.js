function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function isAlphanumeric(str) {
    return str.search(/^[a-zA-Z0-9]+$/) !== -1;
}
async function register() {
    let pw = document.getElementById("pw").value;
    let pw2 = document.getElementById("pw2").value;
    let un = document.getElementById("un").value;
    if (pw != pw2) {
        document.getElementById("errortext").innerHTML = "Passwords don't match"
        return;
    } else if ( (!isAlphanumeric(un + pw))) {
        document.getElementById("errortext").innerHTML = "Username and password must be only letters and numbers"
        return;

    } else {
        document.getElementById("errortext").innerHTML = ""
    }
    let response = await fetch("register.php?un=" + un + "&pw=" + pw).then(x => x.text());
    try{
        response = JSON.parse(response)
    }catch{
        console.log(response)
        return;
    }
    if(response.error){
        document.getElementById("errortext").innerHTML = response.error
    }else{
        setCookie("un",un,10);
        setCookie("pw",pw,10);
        window.location.href = "../"
    }
}