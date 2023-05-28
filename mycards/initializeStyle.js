
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
async function initializeStyle() {
    let username = getCookie("un");
    let password = getCookie("pw");
    let deckFolder = await fetch("../getStyle.php?un=" + username + "&pw=" + password).then(x => x.text())

    try {
        deckFolder = JSON.parse(deckFolder).folder
    }
    catch {
        return;
    }
    addStylesheet(deckFolder)
    addBanner(deckFolder)
} 
initializeStyle()
function addStylesheet(folder) {
    let head = document.head;
    let link = document.createElement("link")
    link.rel = "stylesheet"
    link.type = "text/css"
    link.href = "../assets/" + folder + "/style.css"
    head.appendChild(link)
}
function addBanner(folder) {
    document.getElementById("banner").src = "../assets/" + folder + "/banner.png"
}