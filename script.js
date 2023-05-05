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
let assets = {}
function setup() {
    let canvas = createCanvas(600, 600)
    canvas.parent("content")
}
function mouseClicked() {
    if (players.length > 0 && username == players[0].name) {
        let j = fetch("takeTurn.php?un=" + username + "&pw=" + password).then(x => x.text()).then(x => console.log(x))
    }
}
function loadItem(iconIndex) {
    loadedbool[iconIndex] = true;
}
function isMyTurn(){
    return players.length > 0 && username == players[0].name
}
function draw() {
    if (frameCount % 10 == 0) {
        updateGame();
    }
    background(80)
    noStroke()
    fill(255)
    textAlign(CENTER)
    textFont('Georgia')
    textSize(50)
    text("Card Clash!", 300, 75)
    if (!signedIn) {
        text("Not Signed in", 300, 525)
    }
    textAlign(LEFT)
    textSize(25)
    for (let i = 0; i < players.length; i++) {
        text(players[i].name + " (" + players[i].health + " hp)", 30, i * 35 + 150)
    }
    if ( isMyTurn()) {
        rect(300, 300, 100, 100)
    }
    for (let i = 0; i < cards.length; i++) {
        if (assets[cards[i].icon] === undefined) {
            console.log("Loading "+ cards[i].icon)
            loadedbool[cards[i].icon] = false;
            assets[cards[i].icon] = loadImage("assets/" + cards[i].icon, function () { loadItem(cards[i].icon) })
        }
        if (loadedbool[cards[i].icon]) {
            fill(255)
            rect((i%5)*100+60,500-floor(i/5)*100,80,80)
            image(assets[cards[i].icon],(i%5)*100+60,500-floor(i/5)*100,80,80)
        } 
        if(mouseX > (i%5)*100+60 && mouseY > 400+floor(i/5)*100 && mouseX <(i%5)*100+140 && mouseY < 480+floor(i/5)*100  ){
            fill(255)
            rect(380,120,200,100)
            fill(0)
            textAlign(CENTER)
            text(cards[i].name,480,150)
            text(cards[i].damage + " Dmg",480,180)
            text(cards[i].health + " Hp",480,210)
        }
    }
}
let loaded = {};
let loadedbool = {};
let players = [];
let cards = [];
let username = getCookie("un");
let password = getCookie("pw");
try {
    attemptLogin();
    //catch exception for invalid start signins
} catch {
    console.log("login failed")
}
async function updateGame() {
    let response = await fetch("getGameUpdate.php?un=" + username + "&pw=" + password).then(x => x.text())
    try {
        response = JSON.parse(response)
    } catch {
        console.error(response)
        return;
    }
    if (response.error) {
        console.error(response.error)
    } else {
        if (response.players) {
            players = response.players
        }
        if (response.cards) {
            cards = response.cards;
            //console.log(JSON.stringify(response))
        }
        //console.log(response)
    }
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