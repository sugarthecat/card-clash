
//cookies code
function deleteAllCookies() {
    const cookies = document.cookie.split(";");

    for (let i = 0; i < cookies.length; i++) {
        const cookie = cookies[i];
        const eqPos = cookie.indexOf("=");
        const name = eqPos > -1 ? cookie.substring(0, eqPos) : cookie;
        document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/";
    }
}
function getCookie(cname) {
    let name = cname + "=";
    let ca = document.cookie.split(';');
    for (let i = 0; i < ca.length; i++) {
        let c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}
function setCookie(cname, cvalue, exdays) {
    const d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    let expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}


let username = getCookie("un");
let password = getCookie("pw");
let autoAttempt = true;
try {
    attemptLogin();
    //catch exception for invalid start signins
} catch {
    console.log("login failed")
}
async function attemptLogin() {
    let response = await fetch("login.php?un=" + username + "&pw=" + password)
        .then(x => x.text())
    try {
        response = JSON.parse(response)
    } catch {
        console.error("Server sent invalid input: " + response)
        return;
    }
    if (!response.error) {
        
        document.getElementById("content").innerHTML = "<p> Welcome back, " + username + "</p>"
        let logOutBtn = document.createElement("button")
        logOutBtn.innerHTML = "Log Out"
        logOutBtn.onclick = logout;
        document.getElementById("content").appendChild(logOutBtn)
        return true
    } else if(!autoAttempt) {
        document.getElementById("errortext").innerHTML = response.error
        if (response.error == "Invalid login") {
            console.log(document.cookie)
        }
    }
    autoAttempt = false;
}
function logout() {
    deleteAllCookies();
    location.reload();
}
async function pageLogin(){
    username = document.getElementById('un').value;
    password = document.getElementById('pw').value;
    let success = await attemptLogin()
    if(success){
        setCookie("un", username,10)
        setCookie("pw", password,10)
    }
}