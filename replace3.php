<?php
$files = [];
$dir = new RecursiveDirectoryIterator(__DIR__);
foreach (new RecursiveIteratorIterator($dir) as $file) {
    if ($file->isFile() && in_array($file->getExtension(), ["php", "md", "env"]) && !str_contains($file->getPathname(), "vendor") && !str_contains($file->getPathname(), "storage") && !str_contains($file->getPathname(), "node_modules")) {
        $content = file_get_contents($file->getPathname());
        $original = $content;
        
        $content = str_ireplace("rpms.local", "rpms.local", $content);
        $content = str_ireplace("Rural Region", "Rural Region", $content);
        $content = str_ireplace("National Park", "National Park", $content);
        $content = str_ireplace("System President", "System President", $content);
        $content = str_ireplace("System", "System", $content);
        $content = str_replace("Research Project Management System", "Research Project Management System", $content);
        $content = str_replace("rpms", "rpms", $content);
        
        if ($content !== $original) {
            file_put_contents($file->getPathname(), $content);
            echo "Updated: " . $file->getPathname() . "\n";
        }
    }
}

