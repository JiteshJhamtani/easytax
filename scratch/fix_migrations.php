<?php

$files = glob(__DIR__ . '/../database/migrations/*.php');
$count = 0;
foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Only process if it has Schema::create and hasn't been wrapped in hasTable yet
    if (strpos($content, 'Schema::create(') !== false && strpos($content, 'hasTable') === false) {
        
        // 1. Replace the start of Schema::create with the wrapper
        $content = preg_replace(
            '/Schema::create\(\s*\'([^\']+)\'\s*,\s*(function\s*\([^\)]+\)\s*(?:use\s*\([^\)]+\)\s*)?\{)/', 
            'if (!\Illuminate\Support\Facades\Schema::hasTable(\'$1\')) { try { Schema::create(\'$1\', $2', 
            $content
        );
        
        // 2. Replace the end of the Blueprint closure with the closing tags and try-catch
        $content = preg_replace(
            '/(\$table->timestamps\(\);\s*\});/', 
            '$1 } catch (\Exception $e) { if (strpos($e->getMessage(), \'already exists\') === false) { throw $e; } } }', 
            $content
        );
        
        file_put_contents($file, $content);
        $count++;
    }
}

echo "Fixed $count migrations.\n";
