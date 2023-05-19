async function getDeckCards() {
    let sLoc = window.location.search;
    let response = await fetch("getDeck.php" + sLoc)
    try {
        response = await response.json()

    } catch {
        return
    }
    console.log(response)
    if (!response.error) {

        let cards = response.cards;
        console.log(cards)
        for (let i = 0; i < cards.length; i++) {
            let strip = getNewStrip(cards[i]);
            document.getElementById("content").appendChild(strip);
        }
    } else {
        document.getElementById("errortext").innerHTML = response.error
        if (response.error == "Invalid login") {
            console.log(document.cookie)
        }
    }
}
getDeckCards()
function getNewStrip(card) {
    let div = document.createElement("div");
    div.innerHTML = card.name;
    div.className = "card-stub"
    let img = document.createElement("img")
    img.src = "../assets/" + card.icon
    div.appendChild(img)
    let stats = document.createElement("ul");
    stats.className = "statlist"
    if(card.health > 0){}
    let health = createLi("Health: " +card.health);
    let damage = createLi("Damage: "+card.damage);
    stats.appendChild(health)
    stats.appendChild(damage)
    div.appendChild(stats)
    if(card.health == 0){
        health.innerHTML = "";
    }
    if(card.damage == 0){
        damage.innerHTML = "-";
    }
    return div
}

function createLi(innerHTML){
    let li = document.createElement("li");
    li.innerHTML = innerHTML;
    return li;
}