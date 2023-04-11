fetch("test.py")
.then(x => x.text())
.then(x => alert(x))