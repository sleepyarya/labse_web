<?php
// Test file upload functionality
echo "<h2>Upload Test</h2>";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    echo "<h3>POST Data:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h3>FILES Data:</h3>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    
    if (isset($_FILES['gambar'])) {
        echo "<h3>File Info:</h3>";
        echo "Name: " . $_FILES['gambar']['name'] . "<br>";
        echo "Type: " . $_FILES['gambar']['type'] . "<br>";
        echo "Size: " . $_FILES['gambar']['size'] . " bytes<br>";
        echo "Error: " . $_FILES['gambar']['error'] . "<br>";
        echo "Tmp Name: " . $_FILES['gambar']['tmp_name'] . "<br>";
        
        if ($_FILES['gambar']['error'] == 0) {
            $upload_path = '../uploads/artikel/test_' . time() . '_' . $_FILES['gambar']['name'];
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $upload_path)) {
                echo "<p style='color: green;'>File uploaded successfully to: $upload_path</p>";
                if (file_exists($upload_path)) {
                    echo "<p>File exists and size: " . filesize($upload_path) . " bytes</p>";
                }
            } else {
                echo "<p style='color: red;'>Failed to move uploaded file</p>";
            }
        }
    }
}
?>

<form method="POST" enctype="multipart/form-data">
    <p>Select image file:</p>
    <input type="file" name="gambar" accept="image/*">
    <br><br>
    <input type="submit" value="Upload Test">
</form>

<p><a href="my_articles.php">Back to Articles</a></p>
