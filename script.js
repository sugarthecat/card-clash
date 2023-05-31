let signedIn = false;
let selectedCard = null;
let assets = {}
let loadedbool = {};
let players = [];
let logs = []
let prevlogs = "";
let cards = [];
let gameActive = false;
let username = getCookie("un");
let password = getCookie("pw");
try {
    attemptLogin();
    //catch exception for invalid start signins
} catch {
    console.log("login failed")
}
function mouseInRange(x, y, w, h) {
    return (mouseX >= x && mouseY >= y && mouseX <= w + x && mouseY <= h + y);
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
function setup() {
    let canvas = createCanvas(600, 600)
    canvas.parent("game")
}
async function mouseClicked() {
    if (!gameActive && signedIn) {
        if (mouseInRange(325, 350, 150, 170)) {
            fetch("startGame.php?un=" + username + "&pw=" + password).then(x => x.text()).then(x => console.log(x));
        }
    }
    else if (isMyTurn()) {
        if (players.length == 1) {
            if (mouseInRange(150, 300, 300, 100)) {
                fetch("endGame.php?un=" + username + "&pw=" + password)
            }
        } else {
            if (mouseInRange(460, 300, 80, 80)) {
                fetch("takeTurn.php?un=" + username + "&pw=" + password).then(x => x.text()).then(x => console.log(x))
            }
            for (let i = 0; i < cards.length; i++) {
                if (mouseInRange((i % 5) * 100 + 60, 500 - floor(i / 5) * 100, 80, 80)) {
                    if (selectedCard == i) {
                        selectedCard = null;
                    } else {
                        selectedCard = i
                    }
                }
            }
            for (let i = 0; i < players.length; i++) {
                if (i != 0 && selectedCard !== null && dist(mouseX, mouseY, 15, i * 35 + 60) < 25) {
                    //console.log("Use card " + cards[selectedCard].id + " on " + players[i].id);
                    //console.log("takeTurn.php?un=" + username + "&pw=" + password + "&card=" + cards[selectedCard].id + "&target=" + players[i].id)
                    console.log(await fetch("takeTurn.php?un=" + username + "&pw=" + password + "&card=" + cards[selectedCard].id + "&target=" + players[i].id).then(x => x.text()))
                }
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
    if (frameCount % 20 == 0) {
        updateGame();
    }
    background(80)
    noStroke()
    fill(255)
    textAlign(CENTER)
    textFont('Georgia')
    textSize(50)
    if (!signedIn) {
        text("Not Signed in", 300, 525)
    }


    textAlign(LEFT)
    textSize(25)
    for (let i = 0; i < players.length; i++) {
        if (isMyTurn() && i != 0 && selectedCard !== null) {
            push()
            fill(255, 0, 0)
            circle(15, i * 35 + 60, 25)
            pop()
        }
        text(players[i].name + " (" + players[i].health + " hp)", 30, i * 35 + 70)
    }
    if (!gameActive && signedIn) {
        textSize(50)
        textAlign(CENTER)
        text("Game Inactive", 400, 300)
        if (players.length > 1) {
            textSize(40)
            fill(255)
            rect(325, 350, 150, 70)
            fill(0)
            text("Start", 400, 400)
        }
    }
    if (gameActive && isMyTurn() && players.length == 1) {
        textSize(40)
        textAlign(CENTER)
        text("You win!", 300, 200)
        rect(150, 300, 300, 100)
        fill(0)
        textSize(50)
        text("End Game", 300, 370)
        //fetch("endGame.php")
    } else if (gameActive) {
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
            if ((mouseInRange((i % 5) * 100 + 60, 500 - floor(i / 5) * 100, 80, 80) && selectedCard === null) || selectedCard == i) {
                fill(255)
                rect(330, 120, 250, 150)
                fill(0)
                textAlign(CENTER)
                textSize(30)
                text(cards[i].name, 330, 122, 250, 100)
                textLeading(30)
                if (cards[i].description) {
                    text(cards[i].description, 330, 180,250,200)
                } else if (cards[i].damage > 0) {
                    text(cards[i].damage + " Dmg", 455, 180)
                    if (cards[i].health > 0) {
                        text(cards[i].health + " Hp", 455, 210)
                    }
                } else if (cards[i].health > 0) {
                    text(cards[i].health + " Hp", 455, 180)
                }
            }
        }
    }
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
        if (response.active === false) {
            gameActive = false;
        } else if (response.active === true) {
            gameActive = true;
        }
        if (response.logs) {
            logs = response.logs;
            if (JSON.stringify(logs) != prevlogs) {
                prevlogs = JSON.stringify(logs);
                resetLogSidebar()
            }
            //console.log(JSON.stringify(response.logs))
        }
        //console.log(response)
    }
}
function resetLogSidebar() {
    let logsidebar = document.getElementById("logs").children[0]
    while (logsidebar.children.length > 0) {
        logsidebar.removeChild(logsidebar.children[0])
    }
    for (let i = 0; i < logs.length; i++) {
        let newLog = generateLog(logs[i])
        logsidebar.appendChild(newLog)
    }
}
function generateLog(loginfo) {
    //console.log(loginfo)
    let log = document.createElement("tr")
    let imgd = document.createElement('td')
    let img = document.createElement('img')
    img.src = "assets/" + loginfo.img
    imgd.appendChild(img)
    let spand = document.createElement('td')
    let span = document.createElement('span')
    span.innerText = loginfo.msg
    spand.appendChild(span)
    log.appendChild(imgd)
    log.appendChild(spand)
    return log
}
function removeAllChildNodes(parent) {
    while (parent.firstChild) {
        parent.removeChild(parent.firstChild);
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
    await fetch("leaveGame.php?un=" + username + "&pw=" + password).then(x => x.text());
}
