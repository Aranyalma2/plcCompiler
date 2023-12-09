<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JSON Editor</title>
</head>

<body>

    <div>
        <h2>JSON Editor</h2>
        <textarea id="jsonEditor" rows="10" cols="80"></textarea>
        <br>
        <button onclick="loadJsonFromFile()">Load JSON from File</button>
        <button onclick="sendJson()">Send JSON</button>
        <div id="responseBox"></div>
    </div>

    <script>
    // Function to load JSON from file
    function loadJsonFromFile() {
        fetch('mockProject.json')
            .then(response => response.json())
            .then(data => {
                // Set the loaded JSON data in the editor
                document.getElementById("jsonEditor").value = JSON.stringify(data, null, 2);
            })
            .catch(error => {
                console.error('Error loading JSON:', error);
            });
    }

    function sendJson() {
        // Get the JSON data from the textarea
        var jsonData = document.getElementById("jsonEditor").value;



        // Send a POST request to request.php with the JSON data
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "request.php", true);
        xhr.setRequestHeader("Content-Type", "application/json");

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Handle the response from request.php
                var responseBox = document.getElementById("responseBox");
                responseBox.innerHTML += '<br><h2>Response:</h2><br>' + xhr.responseText;

            }
        };


        xhr.send(jsonData);
    }

    // Load JSON from file when the page loads
    window.onload = loadJsonFromFile;
    </script>

</body>

</html>