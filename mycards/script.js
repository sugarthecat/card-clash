
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
let decks;
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

        decks = response.decks;
        console.log(decks)
        for (let i = 0; i < decks.length; i++) {
            let strip = getNewStrip(decks[i].name, decks[i].icon, decks[i].id);
            if (decks[i].selected == '1') {
                strip.classList.add("selected-deck")
            }
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

function getNewStrip(name, src, id) {
    let div = document.createElement("div");
    div.innerHTML = name;
    div.className = "deck-stub"
    let img = document.createElement("img")
    img.src = "../assets/" + src
    let a = document.createElement('a')
    a.href = "../cardpack?id=" + id
    a.appendChild(img)
    let button = document.createElement("button");
    button.innerHTML = "Select Deck"
    button.onclick = function () { selectDeck(id) };
    div.appendChild(a)
    div.appendChild(button)
    return div
}

function selectDeck(deck) {
    let content = document.getElementById("content")
    for (let i = 0; i < decks.length; i++) {
        content.children[i + 1].classList.remove("selected-deck")
        if (decks[i].id == deck) {
            content.children[i + 1].classList.add("selected-deck")
        }
    }
    fetch("selectDeck.php?un=" + username + "&pw=" + password + "&deck=" + deck);
} 