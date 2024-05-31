I'll break down and explain each section of the code for both `index.php` and `submit.php`.

 `index.php`

 1. Reading Comments from the Text File

```php
<?php
$filename = 'comments.txt';

// Function to read comments from the text file
function readComments($filename) {
    $comments = [];
    if (file_exists($filename)) {
        $file = fopen($filename, 'r');
        while (($line = fgets($file)) !== false) {
            $comments[] = json_decode($line, true);
        }
        fclose($file);
    }
    return $comments;
}

// Read the comments
$comments = readComments($filename);

// Get the last 5 comments
$lastComments = array_slice($comments, -5);
?>
```

**Explanation**:
- **Variable Declaration**: `$filename` stores the name of the text file where comments are saved.
- **Function `readComments`**: This function reads the content of `comments.txt` line by line.
    - It checks if the file exists.
    - Opens the file for reading.
    - Reads each line, decodes the JSON data, and stores it in the `$comments` array.
    - Closes the file.
- **Read Comments**: The `$comments` array is populated by calling `readComments($filename)`.
- **Last 5 Comments**: `array_slice($comments, -5)` retrieves the last five comments from the array.

#### 2. HTML Structure and CSS Styling

```html
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Livre d'or</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            color: #333;
        }
        .guestbook {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .entry {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }
        .entry:last-child {
            border-bottom: none;
        }
        form {
            margin-top: 20px;
        }
        form input, form textarea {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        form button {
            display: inline-block;
            padding: 10px 20px;
            border: none;
            background-color: #5cb85c;
            color: #fff;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
        }
        form button:last-child {
            background-color: #007bff;
        }
        form button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="guestbook">
        <h1>Livre d'or</h1>
```

Explanation:
- HTML Structure:
    - `<!DOCTYPE html>` declares the document type.
    - `<html lang="fr">` sets the language to French.
    - `<head>` contains metadata and includes the character set, viewport settings, and the title.
    - `<style>` section contains CSS for styling the page.
        - Basic styling for the body, container, and form elements.
        - Classes for the guestbook entries and form inputs/buttons.

 3. Displaying Comments and Form

```html
        <form action="submit.php" method="POST">
            <input type="text" name="author" placeholder="Votre nom" required>
            <input type="email" name="email" placeholder="Votre email" required>
            <textarea name="text" placeholder="Votre avis" required></textarea>
            <button type="submit" name="action" value="submit">Envoyer</button>
            <button type="submit" name="action" value="show">Afficher les derniers avis</button>
        </form>
        <div id="entries">
            <?php
            if (!empty($lastComments)) {
                foreach ($lastComments as $comment) {
                    echo "<div class='entry'>";
                    echo "<p>" . htmlspecialchars($comment['text']) . "</p>";
                    echo "<p><em>- " . htmlspecialchars($comment['author']) . " (" . htmlspecialchars($comment['email']) . ")</em></p>";
                    echo "<p><small>" . htmlspecialchars($comment['created_at']) . "</small></p>";
                    echo "</div>";
                }
            } else {
                echo "Aucun avis pour le moment.";
            }
            ?>
        </div>
    </div>
</body>
</html>
```

Explanation:
- **Form**:
    - Contains input fields for the user's name (`author`), email (`email`), and comment (`text`).
    - Two buttons: one for submitting the comment (`Envoyer`) and one for displaying the latest comments (`Afficher les derniers avis`).
    - `action="submit.php"` directs form submissions to `submit.php`.
- Display Comments:
    - The PHP block within the `div` with `id="entries"` loops through the last five comments and displays them.
    - Each comment is displayed within a `div` with class `entry`.
    - `htmlspecialchars` is used to prevent XSS attacks by escaping special characters.

 `submit.php`

 1. Handling Form Submission

```php
$filename = 'comments.txt';

// Function to read comments from the text file
function readComments($filename) {
    $comments = [];
    if (file_exists($filename)) {
        $file = fopen($filename, 'r');
        while (($line = fgets($file)) !== false) {
            $comments[] = json_decode($line, true);
        }
        fclose($file);
    }
    return $comments;
}
```

Explanation:
- **Variable Declaration**: `$filename` stores the name of the text file where comments are saved.
- **Function `readComments`**: Same as in `index.php`, reads comments from `comments.txt`.

 2. Processing the Form Data

```php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];

    if ($action == 'submit') {
        $author = htmlspecialchars($_POST['author']);
        $email = htmlspecialchars($_POST['email']);
        $text = htmlspecialchars($_POST['text']);
        
        $newComment = [
            'author' => $author,
            'email' => $email,
            'text' => $text,
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Append the new comment to the text file
        $file = fopen($filename, 'a');
        fwrite($file, json_encode($newComment) . PHP_EOL);
        fclose($file);

        // Redirect to the index page
        header("Location: index.php");
        exit();
    } elseif ($action == 'show') {
        $comments = readComments($filename);
        $lastComments = array_slice($comments, -5);
        include 'index.php';
        exit();
    }
}
```

Explanation:
- Form Handling:
    - Checks if the request method is POST.
    - Reads the action from the form submission to determine which button was clicked.
- Submitting a Comment (`action == 'submit'`):
    - Reads and sanitizes the input fields (`author`, `email`, `text`).
    - Creates a new comment array with the current timestamp.
    - Appends the new comment to the `comments.txt` file as a JSON string.
    - Redirects back to `index.php` to show the updated list of comments.
- Showing Comments (`action == 'show'`):
    - Reads the comments from the file.
    - Extracts the last five comments.
    - Includes `index.php` to display the comments directly.

 Conclusion

This setup provides a basic guestbook application using PHP and a text file for storage. The `index.php` file is responsible for displaying the form and the last five comments, while `submit.php` handles form submissions and decides whether to append a new comment or display the latest comments. This simple implementation avoids the need for a database, making it easy to set up and maintain for small-scale applications.
