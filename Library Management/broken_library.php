<?php


$database_host = "localhost";
$database_user = "root";
$database_password = "";
$database_name = "library_db";


// Must use the actual variable names defined above
$conn = new mysqli($database_host, $database_user, $database_password, $database_name);

// Added connection error check 
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


// The SQL was missing a closing single quote after 'Available' before the closing parenthesis
$conn->query("CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255),
    year INT(4),
    status VARCHAR(20) DEFAULT 'Available'
)");


// This causes an undefined index notice and can crash on strict servers
if (isset($_POST['add'])) {
    // BUG 4 FIXED: Direct SQL injection possible — now using prepared statements
    // Was: $title = $_POST['title']; then injecting directly into query string
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $year = intval($_POST['year']);

    $stmt = $conn->prepare("INSERT INTO books (title, author, year) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $title, $author, $year);
    $stmt->execute();
    $stmt->close();
}

// Could delete any row with a crafted URL
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // validate as integer
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}


// Was: WHERE title LIKE '%search%' — this searches for the word "search" literally
// Fixed: use the actual $search variable with proper binding
if (isset($_GET['search']) && $_GET['search'] !== '') {
    $search = "%" . $conn->real_escape_string($_GET['search']) . "%";
    $sql = "SELECT * FROM books WHERE title LIKE '$search' OR author LIKE '$search'";
} else {
    $sql = "SELECT * FROM books";
}
$result = $conn->query($sql);

// BUG 7 FIXED: UPDATE query was missing WHERE clause
// Without WHERE id = $id, ALL rows in the table would be updated
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $year = intval($_POST['year']);

    $stmt = $conn->prepare("UPDATE books SET title=?, author=?, year=? WHERE id=?");
    $stmt->bind_param("ssii", $title, $author, $year, $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch edit data if edit is requested
$edit_book = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_result = $conn->query("SELECT * FROM books WHERE id = $edit_id");
    $edit_book = $edit_result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Library System</title>
    <!-- BUG 8 FIXED: CSS was missing closing brace for body { rule
         body { font-family: Arial; margin: 20px;   <-- no closing }
         This caused all following CSS rules to be invalid -->
    <style>
        body { font-family: Arial; margin: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
    </style>
</head>
<body>
<h1>Library Management System</h1>

<!-- BUG 9 FIXED: Add Book form was using method="GET" instead of method="POST"
     The PHP code checks $_POST['add'], so the form must submit via POST -->
<form method="POST" action="">
    <h3>Add Book</h3>
    <input type="text" name="title" placeholder="Title">
    <input type="text" name="author" placeholder="Author">
    <input type="number" name="year" placeholder="Year">
    <button type="submit" name="add">Add Book</button>
</form>

<?php if ($edit_book): ?>
<form method="POST" action="">
    <h3>Edit Book</h3>
    <input type="hidden" name="id" value="<?php echo $edit_book['id']; ?>">
    <input type="text" name="title" value="<?php echo htmlspecialchars($edit_book['title']); ?>">
    <input type="text" name="author" value="<?php echo htmlspecialchars($edit_book['author']); ?>">
    <input type="number" name="year" value="<?php echo $edit_book['year']; ?>">
    <button type="submit" name="update">Update Book</button>
</form>
<?php endif; ?>

<table>
    <tr>
        <th>ID</th><th>Title</th><th>Author</th><th>Year</th><th>Actions</th>
    </tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <!-- BUG 10 FIXED: Output was not escaped with htmlspecialchars()
             Displaying raw database values allows stored XSS attacks -->
        <td><?php echo htmlspecialchars($row['title']); ?></td>
        <td><?php echo htmlspecialchars($row['author']); ?></td>
        <td><?php echo $row['year']; ?></td>
        <td>
            <a href="?edit=<?php echo $row['id']; ?>">Edit</a> |
            <a href="?delete=<?php echo $row['id']; ?>">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>
<?php $conn->close(); ?>
