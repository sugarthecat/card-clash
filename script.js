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
async function mouseClicked() {
    if (isMyTurn()) {
        if (mouseX > 460 && mouseY > 300 && mouseX < 540 && mouseY < 380) {
            fetch("takeTurn.php?un=" + username + "&pw=" + password).then(x => x.text()).then(x => console.log(x))
        }
        for (let i = 0; i < cards.length; i++) {
            if ((mouseX > (i % 5) * 100 + 60 && mouseY > 500 - floor(i / 5) * 100 && mouseX < (i % 5) * 100 + 140 && mouseY < 580 - floor(i / 5) * 100)) {
                selectedCard = i
            }
        }
        for (let i = 0; i < players.length; i++) {
            if ( i != 0 && selectedCard !== null && dist (mouseX,mouseY, 15, i * 35 + 140) < 25) {
                console.log("Use card " + cards[selectedCard].id + " on " + players[i].id);
                console.log("takeTurn.php?un="+username+"&pw="+password+"&card="+cards[selectedCard].id+"&target="+players[i].id)
                console.log(await fetch("takeTurn.php?un="+username+"&pw="+password+"&card="+cards[selectedCard].id+"&target="+players[i].id).then(x => x.text()))
            }
        }
    }
}
function loadItem(iconIndex) {
    loadedbool[iconIndex] = true;
}
function isMyTurn() {
    return players.length > 0 && username == players[0].name
}
function draw() {
    if (!isMyTurn()) {
        selectedCard = null
    }
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
        if (isMyTurn() && i != 0 && selectedCard !== null) {
            push()
            fill(255, 0, 0)
            circle(15, i * 35 + 140, 25)
            pop()
        }
        text(players[i].name + " (" + players[i].health + " hp)", 30, i * 35 + 150)
    }
    if (isMyTurn()) {
        rect(460, 300, 80, 80)
        fill(0)
        text("SKIP TURN", 465, 310, 60, 80)
    }
    for (let i = 0; i < cards.length; i++) {
        if (assets[cards[i].icon] === undefined) {
            console.log("Loading " + cards[i].icon)
            loadedbool[cards[i].icon] = false;
            assets[cards[i].icon] = loadImage("assets/" + cards[i].icon, function () { loadItem(cards[i].icon) })
        }
        if (loadedbool[cards[i].icon]) {
            if (selectedCard == i) {
                fill(100, 200, 200)
                rect((i % 5) * 100 + 50, 490 - floor(i / 5) * 100, 100, 100)
            }
            fill(255)
            rect((i % 5) * 100 + 60, 500 - floor(i / 5) * 100, 80, 80)
            image(assets[cards[i].icon], (i % 5) * 100 + 60, 500 - floor(i / 5) * 100, 80, 80)
        }
        if ((mouseX > (i % 5) * 100 + 60 && mouseY > 500 - floor(i / 5) * 100 && mouseX < (i % 5) * 100 + 140 && mouseY < 580 - floor(i / 5) * 100 && selectedCard === null) || selectedCard == i) {
            fill(255)
            rect(380, 120, 200, 100)
            fill(0)
            textAlign(CENTER)
            text(cards[i].name, 480, 150)
            text(cards[i].damage + " Dmg", 480, 180)
            text(cards[i].health + " Hp", 480, 210)
        }
    }
}
let selectedCard = null;
let assets = {}
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
let gameActive = false;
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
        if(response.active === false){
            gameActive = true;
        }else if(response.active === true){
            gameActive = false;
        }
        console.log(response)
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