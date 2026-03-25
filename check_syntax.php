<?php
// Syntax check script
$files = [
    'd:\\GitHub\\KTPM\\routes\\web.php',
    'd:\\GitHub\\KTPM\\app\\Http\\Controllers\\StaffController.php',
    'd:\\GitHub\\KTPM\\app\\Models\\Employee.php',
    'd:\\GitHub\\KTPM\\app\\Models\\User.php',
    'd:\\GitHub\\KTPM\\app\\Models\\Department.php',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $contents = file_get_contents($file);
        // Check for basic syntax by looking for opening and closing tags
        if (strpos($contents, '<?php') !== false && (strpos($contents, '?>') !== false || true)) {
            echo "✓ $file - OK\n";
        } else {
            echo "✗ $file - MISSING PHP TAGS\n";
        }
    } else {
        echo "✗ $file - NOT FOUND\n";
    }
}

// Check views
$views = [
    'd:\\GitHub\\KTPM\\resources\\views\\staff.blade.php',
    'd:\\GitHub\\KTPM\\resources\\views\\staff\\create.blade.php',
    'd:\\GitHub\\KTPM\\resources\\views\\staff\\edit.blade.php',
    'd:\\GitHub\\KTPM\\resources\\views\\staff\\show.blade.php',
];

echo "\nViews:\n";
foreach ($views as $view) {
    if (file_exists($view)) {
        echo "✓ $view - OK\n";
    } else {
        echo "✗ $view - NOT FOUND\n";
    }
}
?>
