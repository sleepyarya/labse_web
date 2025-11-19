<?php
echo "<h2>PHP Upload Settings</h2>";

echo "<table border='1' cellpadding='5'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";
echo "<tr><td>file_uploads</td><td>" . (ini_get('file_uploads') ? 'On' : 'Off') . "</td></tr>";
echo "<tr><td>upload_max_filesize</td><td>" . ini_get('upload_max_filesize') . "</td></tr>";
echo "<tr><td>post_max_size</td><td>" . ini_get('post_max_size') . "</td></tr>";
echo "<tr><td>max_file_uploads</td><td>" . ini_get('max_file_uploads') . "</td></tr>";
echo "<tr><td>memory_limit</td><td>" . ini_get('memory_limit') . "</td></tr>";
echo "<tr><td>max_execution_time</td><td>" . ini_get('max_execution_time') . "</td></tr>";
echo "<tr><td>upload_tmp_dir</td><td>" . (ini_get('upload_tmp_dir') ?: 'Default') . "</td></tr>";
echo "</table>";

echo "<h3>Directory Permissions</h3>";
$upload_dir = '../uploads/artikel/';
echo "<p>Upload directory: " . realpath($upload_dir) . "</p>";
echo "<p>Directory exists: " . (is_dir($upload_dir) ? 'Yes' : 'No') . "</p>";
echo "<p>Directory writable: " . (is_writable($upload_dir) ? 'Yes' : 'No') . "</p>";

if (is_dir($upload_dir)) {
    $files = scandir($upload_dir);
    echo "<h3>Files in upload directory:</h3>";
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>$file</li>";
        }
    }
    echo "</ul>";
}

echo "<p><a href='my_articles.php'>Back to Articles</a></p>";
?>
