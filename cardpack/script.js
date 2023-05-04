async function getDeckCards(){
    let sLoc = window.location.search;
    let response = await fetch("getDeck.php"+sLoc)
    try{
        response = await response.json()

    }catch{
        return
    }
    console.log(response)
    if (!response.error) {

        let cards = response.cards;
        console.log(cards)
        for(let i = 0; i<cards.length; i++){
            let strip = getNewStrip(cards[i].name, cards[i].icon);
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
function getNewStrip(name, src, id){
    let div = document.createElement("div");
    div.innerHTML = name;
    div.className = "card-stub"
    let img = document.createElement("img")
    img.src = "../assets/"+src
    div.appendChild(img)
    return div
}