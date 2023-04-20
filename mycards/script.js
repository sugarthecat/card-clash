
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
let username = getCookie("un");
let password = getCookie("pw");
async function attemptGetCards() {
    let response = await fetch("getCards.php?un=" + username + "&pw=" + password)
        .then(x => x.text())
    try {
        response = JSON.parse(response)
    } catch {
        console.error("Server sent invalid input: " + response)
        return;
    }
    if (!response.error) {
        let decks = response.decks;
        for(let i = 0; i<decks.length; i++){
            let strip = getNewStrip(decks[i].name, decks[i].icon);
            document.getElementById("content").appendChild(strip);
        }
    } else {
        document.getElementById("errortext").innerHTML = response.error
        if (response.error == "Invalid login") {
            console.log(document.cookie)
        }
    }
}
attemptGetCards();

function getNewStrip(name, src){
    let div = document.createElement("div");
    div.innerHTML = name;
    div.className = "deck-stub"
    let img = document.createElement("img")
    img.src = "../assets/"+src
    console.log(src)
    div.appendChild(img)
    return div
}