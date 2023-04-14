function isAlphanumeric(str) {
    return str.search(/^[a-zA-Z0-9]+$/) !== -1;
}
async function register() {
    let pw = document.getElementById("pw").value;
    let pw2 = document.getElementById("pw2").value;
    let un = document.getElementById("un").value;
    if (pw != pw2) {
        document.getElementById("errortext").innerHTML = "Passwords don't match"
        return;
    } else if (pw.length < 4) {
        document.getElementById("errortext").innerHTML = "Password must be at least 4 characters"
        return;
    } else if (document.getElementById("un").value.length < 5) {
        document.getElementById("errortext").innerHTML = "Username must be at least 5 characters"
        return;
    } else if ( (!isAlphanumeric(un + pw))) {
        document.getElementById("errortext").innerHTML = "Username and password must be only letters and numbers"
        return;

    } else {
        document.getElementById("errortext").innerHTML = ""
    }
    let response = await fetch("register.php?un=" + un + "&pw=" + pw).then(x => x.json());
    if(response.error){
        
        document.getElementById("errortext").innerHTML = response.error
    }else{
        window.location.href = "../"
    }
}