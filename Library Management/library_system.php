<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "library_system";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Creates the books table with all required fields if it does not already exist
$sql = "CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    year INT(4) NOT NULL,
    status VARCHAR(20) DEFAULT 'Available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);


// Runs when the Add Book form is submitted via POST
$add_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $title  = trim($_POST['title']);
    $author = trim($_POST['author']);
    $year   = intval($_POST['year']);

    // Input validation: no empty fields, year between 1000 and 2025
    if ($title === "" || $author === "" || $year === 0) {
        $add_message = "Error: All fields are required.";
    } elseif ($year < 1000 || $year > 2025) {
        $add_message = "Error: Year must be between 1000 and 2025.";
    } else {
        // Use prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO books (title, author, year) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $title, $author, $year);
        if ($stmt->execute()) {
            $add_message = "Book added successfully.";
        } else {
            $add_message = "Error adding book.";
        }
        $stmt->close();
    }
}

// Fetches the book data when user clicks Edit
$edit_book = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_result = $conn->query("SELECT * FROM books WHERE id = $edit_id");
    $edit_book = $edit_result->fetch_assoc();
}

// Runs when the Edit form is submitted
$update_message = "";
if (isset($_POST['update'])) {
    $id     = intval($_POST['id']);
    $title  = trim($_POST['title']);
    $author = trim($_POST['author']);
    $year   = intval($_POST['year']);

    if ($title === "" || $author === "" || $year === 0) {
        $update_message = "Error: All fields are required.";
    } elseif ($year < 1000 || $year > 2025) {
        $update_message = "Error: Year must be between 1000 and 2025.";
    } else {
        // Prepared statement with WHERE clause to update only the correct row
        $stmt = $conn->prepare("UPDATE books SET title=?, author=?, year=? WHERE id=?");
        $stmt->bind_param("ssii", $title, $author, $year, $id);
        if ($stmt->execute()) {
            $update_message = "Book updated successfully.";
        } else {
            $update_message = "Error updating book.";
        }
        $stmt->close();
    }
}


// Runs when user confirms deletion via the JavaScript dialog
if (isset($_GET['delete'])) {
    $del_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM books WHERE id = ?");
    $stmt->bind_param("i", $del_id);
    $stmt->execute();
    $stmt->close();
    // Redirect to avoid re-deletion on refresh
    header("Location: library_system.php");
    exit();
}


// Toggles a book's status between Available and Borrowed
if (isset($_GET['toggle'])) {
    $toggle_id = intval($_GET['toggle']);
    // Fetch current status first
    $res = $conn->query("SELECT status FROM books WHERE id = $toggle_id");
    $current = $res->fetch_assoc();
    if ($current) {
        $new_status = ($current['status'] === 'Available') ? 'Borrowed' : 'Available';
        $stmt = $conn->prepare("UPDATE books SET status=? WHERE id=?");
        $stmt->bind_param("si", $new_status, $toggle_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: library_system.php");
    exit();
}


// Filters books by title or author using LIKE
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== "") {
    // Use prepared statement to safely handle search input
    $like = "%" . $search . "%";
    $stmt = $conn->prepare("SELECT * FROM books WHERE title LIKE ? OR author LIKE ? ORDER BY id DESC");
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
} else {
    // Default: show all books sorted newest first
    $result = $conn->query("SELECT * FROM books ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Library System</title>
    <style>
        /* General layout */
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
            background: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            padding: 20px 30px;
            border: 1px solid #ccc;
        }
        h1 {
            font-size: 22px;
            margin-bottom: 20px;
        }
        h3 {
            font-size: 16px;
            margin-bottom: 8px;
        }

        /* Forms */
        form {
            margin-bottom: 20px;
        }
        input[type="text"],
        input[type="number"] {
            padding: 6px 10px;
            margin-right: 6px;
            border: 1px solid #aaa;
            font-size: 14px;
        }

        /* Buttons - different colors for Add, Edit, Delete */
        button, .btn {
            padding: 6px 14px;
            font-size: 14px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-block;
        }
        .btn-add {
            background: #3a7d44;
            color: #fff;
        }
        .btn-edit {
            background: #1a5fa8;
            color: #fff;
        }
        .btn-delete {
            background: #b33030;
            color: #fff;
        }
        .btn-search {
            background: #555;
            color: #fff;
        }
        .btn-toggle-available {
            background: #3a7d44;
            color: #fff;
            font-size: 12px;
            padding: 3px 8px;
        }
        .btn-toggle-borrowed {
            background: #c47a00;
            color: #fff;
            font-size: 12px;
            padding: 3px 8px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 16px;
        }
        th {
            background: #333;
            color: #fff;
            padding: 9px 10px;
            text-align: left;
            font-size: 14px;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
            font-size: 14px;
        }
        /* Table hover effect */
        tbody tr:hover {
            background: #eef4fb;
        }

        /* Status badge */
        .status-available {
            color: #2a6e32;
            font-weight: bold;
        }
        .status-borrowed {
            color: #a05c00;
            font-weight: bold;
        }

        /* Messages */
        .msg-success {
            color: #2a6e32;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .msg-error {
            color: #b33030;
            font-size: 14px;
            margin-bottom: 8px;
        }

        /* Search bar area */
        .search-bar {
            margin-bottom: 16px;
        }
        .search-bar a {
            font-size: 13px;
            color: #555;
            margin-left: 8px;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Library Management System</h1>

    <!-- SEARCH FORM -->
    <div class="search-bar">
        <form method="GET">
            <input type="text" name="search" placeholder="Search by title or author..."
                   value="<?php echo htmlspecialchars($search); ?>" style="width:260px;">
            <button type="submit" class="btn btn-search">Search</button>
            <a href="library_system.php">Reset</a>
        </form>
    </div>

    <!-- ADD BOOK FORM -->
    <form method="POST">
        <h3>Add New Book</h3>
        <?php if ($add_message !== ""): ?>
            <p class="<?php echo strpos($add_message, 'Error') === false ? 'msg-success' : 'msg-error'; ?>">
                <?php echo htmlspecialchars($add_message); ?>
            </p>
        <?php endif; ?>
        <input type="text" name="title" placeholder="Title" required>
        <input type="text" name="author" placeholder="Author" required>
        <input type="number" name="year" placeholder="Year (1000-2025)" min="1000" max="2025" required>
        <button type="submit" name="add" class="btn btn-add">Add Book</button>
    </form>

    <!-- EDIT BOOK FORM (only shows when editing a book) -->
    <?php if ($edit_book): ?>
    <form method="POST" style="border:1px solid #ccc; padding:12px; background:#fafafa;">
        <h3>Edit Book</h3>
        <?php if ($update_message !== ""): ?>
            <p class="<?php echo strpos($update_message, 'Error') === false ? 'msg-success' : 'msg-error'; ?>">
                <?php echo htmlspecialchars($update_message); ?>
            </p>
        <?php endif; ?>
        <!-- Hidden ID field to identify which book to update -->
        <input type="hidden" name="id" value="<?php echo $edit_book['id']; ?>">
        <input type="text" name="title" value="<?php echo htmlspecialchars($edit_book['title']); ?>" required>
        <input type="text" name="author" value="<?php echo htmlspecialchars($edit_book['author']); ?>" required>
        <input type="number" name="year" value="<?php echo $edit_book['year']; ?>" min="1000" max="2025" required>
        <button type="submit" name="update" class="btn btn-edit">Update Book</button>
    </form>
    <?php endif; ?>

    <!-- BOOKS TABLE -->
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Author</th>
                <th>Year</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result->num_rows === 0): ?>
            <tr><td colspan="6" style="text-align:center;">No books found.</td></tr>
        <?php endif; ?>
        <?php while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <!-- htmlspecialchars prevents XSS when displaying data -->
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['author']); ?></td>
            <td><?php echo $row['year']; ?></td>
            <td>
                <?php if ($row['status'] === 'Available'): ?>
                    <span class="status-available">Available</span>
                <?php else: ?>
                    <span class="status-borrowed">Borrowed</span>
                <?php endif; ?>
            </td>
            <td>
                <!-- Edit button -->
                <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-edit">Edit</a>

                <!-- Delete button with JavaScript confirmation dialog -->
                <a href="?delete=<?php echo $row['id']; ?>"
                   class="btn btn-delete"
                   onclick="return confirm('Are you sure you want to delete this book?');">Delete</a>

                <!-- BONUS: Status toggle button -->
                <?php if ($row['status'] === 'Available'): ?>
                    <a href="?toggle=<?php echo $row['id']; ?>"
                       class="btn btn-toggle-available">Mark Borrowed</a>
                <?php else: ?>
                    <a href="?toggle=<?php echo $row['id']; ?>"
                       class="btn btn-toggle-borrowed">Mark Available</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
<?php $conn->close(); ?>
