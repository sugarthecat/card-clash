function register() {
    fetch("register.py", {
        method: "POST",
        body: { password: document.getElementById('pw') }
    })
    .then(x => x.text())
    .then(x => console.log(x))
}