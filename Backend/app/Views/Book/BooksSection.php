<?php 
        if (!isset($books) || empty($books)) {
            echo "<p>No books available.</p>";
        }
        foreach ($books as $book): ?>
<?php include __DIR__ . '/../Book/BookPostComponent.php'; ?>
<?php endforeach; ?>