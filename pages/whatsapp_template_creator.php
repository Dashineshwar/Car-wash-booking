<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Composer</title>
    <!-- Include Quill.js CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .editor {
            height: 300px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Compose Message</h2>
        <div id="editor" class="editor"></div>
        <input type="file" id="imageUpload" style="display:none">
        <button onclick="sendMessage()" class="btn">Send Message</button>
    </div>

    <!-- Include Quill.js -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'color': [] }, { 'background': [] }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    ['link', 'image'],
                    ['clean']
                ],
                clipboard: {
                    matchVisual: false
                }
            }
        });

        function uploadImage() {
            document.getElementById('imageUpload').click();
        }

        document.getElementById('imageUpload').addEventListener('change', function() {
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var range = quill.getSelection(true);
                    quill.insertEmbed(range.index, 'image', e.target.result);
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        function sendMessage() {
            var message = quill.root.innerHTML;
            // Here you can send the message to your backend for processing and sending
            console.log('Message:', message);
            // Example: You can send the message using fetch API
            /*
            fetch('your-backend-url', {
                method: 'POST',
                body: JSON.stringify({ message: message }),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response:', data);
                // Handle response from backend
            })
            .catch(error => {
                console.error('Error:', error);
                // Handle error
            });
            */
        }
    </script>
</body>
</html>
