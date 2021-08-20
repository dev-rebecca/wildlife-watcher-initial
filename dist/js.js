
// Register
function register (evt) {
    evt.preventDefault();
    const formData = new FormData();

    formData.append(evt.target[0].name, evt.target[0].value);
    formData.append(evt.target[1].name, evt.target[1].value);
    formData.append(evt.target[2].name, evt.target[2].value);
    formData.append(evt.target[3].name, evt.target[3].value);
    fetch(evt.target.action, 
        {
            method: 'POST',
            body: formData
        }
    ) 
    .then (
        function(headers) {
            if (headers.status === 201) {
                console.log('success');
            }
            headers.text().then(function(body) {
                document.getElementById('output').innerHTML = body;
            })
        }
    );
}

// Login
function login (evt) {
    evt.preventDefault();
    const formData = new FormData();

    formData.append(evt.target[0].name, evt.target[0].value);
    formData.append(evt.target[1].name, evt.target[1].value);
    fetch(evt.target.action, 
        {
            method: 'POST',
            body: formData
        }
    ) 
    .then (
        function(headers) {
            if (headers.status === 201) {
                console.log('success');
            }
            headers.text().then(function(body) {
                document.getElementById('output').innerHTML = body;
            })
        }
    );
}


// Logout
function logout (evt) {
    evt.preventDefault();
    fetch(evt.target.action)
    .then (
        function(headers) {
            if (headers.status === 200) {
                console.log('success');
            }
            headers.json().then(function(body) {
                document.getElementById('output').innerHTML = JSON.stringify(body.msg);
            })
        }
    );
}

// Update reg
// To do: values in update reg form are automatically the set values in DB
function updateReg (evt) {
    evt.preventDefault();
    const formData = new FormData();

    formData.append(evt.target[0].name, evt.target[0].value);
    formData.append(evt.target[1].name, evt.target[1].value);
    formData.append(evt.target[2].name, evt.target[2].value);
    formData.append(evt.target[3].name, evt.target[3].value);
    formData.append(evt.target[4].name, evt.target[4].value);
    fetch(evt.target.action, 
        {
            method: 'POST',
            body: formData
        }
    ) 
    .then (
        function(headers) {
            if (headers.status === 201) {
                console.log('success');
            }
            headers.text().then(function(body) {
                document.getElementById('output').innerHTML = body;
            })
        }
    );
}

// Get category
function getCategory (evt) {
    evt.preventDefault();
    var selid = evt.target[0].value;
    var url = evt.target.action + '&id=' + selid;
    fetch(url)
    .then (
        function(headers) {
            if (headers.status === 200) {
                console.log('success');
            }
            headers.json().then(function(body) {
                document.getElementById('output').innerHTML = JSON.stringify(body);
            })
        }
    );
}

// Select all
function getAllCategories (evt) {
    evt.preventDefault();
    fetch(evt.target.action)
    .then (
        function(headers) {
            if (headers.status === 200) {
                console.log('success');
            }
            headers.json().then(function(body) {
                document.getElementById('output').innerHTML = JSON.stringify(body);
            })
        }
    );
}

// Delete animal
function getDelete (evt) {
    evt.preventDefault();
    var delid = evt.target[0].value;
    var url = evt.target.action + '&id=' + delid;
    fetch(url)
    .then (
        function(headers) {
            if (headers.status === 202) {
                console.log('success');
            }
            headers.text().then(function(body) {
                document.getElementById('output').innerHTML = body;
            })
        }
    );
}

function showAlert(msgtype, msg) {
    document.getElementById('alertbox').removeAttribute('hidden');
    document.getElementById('alertmsg').innerHTML = msg;
    var timeOut = window.setTimeout(function() {hideAlert ()}, 10000);
    if (msgtype == 'good') {
        document.getElementById('alertbox').style.backgroundColor = 'rgb(216, 250, 216)';
    }
    if (msgtype == 'bad') {
        document.getElementById('alertbox').style.backgroundColor = 'rgb(250, 216, 245)';
    }
    if (msgtype == 'meh') {
        document.getElementById('alertbox').style.backgroundColor = 'rgb(250, 238, 216)';
    }
}
function hideAlert (msg) {
    document.getElementById('alertbox').setAttribute('hidden', 'hidden');
}