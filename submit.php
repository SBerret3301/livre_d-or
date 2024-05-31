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
    } elseif ($action == 'show') {
        $comments = readComments($filename);
        $lastComments = array_slice($comments, -5);
        include 'index.php';
        exit();
    }
}

// Redirect to the index page
header("Location: index.php");
exit();

?>
