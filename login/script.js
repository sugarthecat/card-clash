let cookieObj;
try {
    cookieObj = JSON.parse(document.cookie)
    attemptLogin();
} catch {
    console.log("not logged in")
}
async function attemptLogin() {
    let response = await fetch("login.php?un=" + cookieObj.un + "&pw=" + cookieObj.pw)
        .then(x => x.json())
    if (!response.error) {
        document.getElementById("content").innerHTML = "<p> Welcome back, " + cookieObj.un + "</p>"
        let logOutBtn = document.createElement("button")
        logOutBtn.innerHTML = "Log Out"
        logOutBtn.onclick = logout;
        document.getElementById("content").appendChild(logOutBtn)
    }else{
        document.getElementById("errortext").innerHTML = response.error
    }
}
function logout() {
    try {
        let cookieObj = JSON.parse(document.cookie)
        cookieObj.un = undefined;
        cookieObj.pw = undefined;
        document.cookie = JSON.stringify(cookieObj);
    } catch {
        console.log("could not clear cookie")
    }
    location.reload();
}

async function login(){

}