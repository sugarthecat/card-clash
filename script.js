let signedIn = false;
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
function setup() {
    let canvas = createCanvas(600, 600)
    canvas.parent("content")

}
function draw() {
    updateGame();


    background(80)
    noStroke()
    fill(255)
    textAlign(CENTER)
    textFont('Georgia')
    textSize(50)
    text("Card Clash!", 300, 75)
    if(!signedIn){
        text("Not Signed in", 300, 525)

    }
}
let username = getCookie("un");
let password = getCookie("pw");
try {
    attemptLogin();
    //catch exception for invalid start signins
} catch {
    console.log("login failed")
}
async function updateGame() {
    let response = fetch("getGameUpdate.php")
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
        signedIn = true;
    } 
}