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
        <form action="submit.php" method="POST">
            <input type="text" name="author" placeholder="Votre nom">
            <input type="email" name="email" placeholder="Votre email">
            <textarea name="text" placeholder="Votre avis"></textarea>
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
