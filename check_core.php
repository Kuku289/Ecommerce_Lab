<?php
echo "<h2>Checking core.php file contents:</h2>";
echo "<pre>";
echo htmlspecialchars(file_get_contents('settings/core.php'));
echo "</pre>";

echo "<hr>";
echo "File size: " . filesize('settings/core.php') . " bytes<br>";
echo "File exists: " . (file_exists('settings/core.php') ? 'YES' : 'NO') . "<br>";
?>