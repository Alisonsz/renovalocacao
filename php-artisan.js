#!/usr/bin/env node

/**
 * This wrapper allows Node.js tools (like Vite) to call PHP Artisan commands.
 * It detects the system PHP path and executes artisan with the correct PHP binary.
 */

const { execSync } = require('child_process');
const path = require('path');
const os = require('os');

const isWindows = os.platform() === 'win32';

// Get the PHP binary path
let phpBinary = 'php';

if (isWindows) {
    // Try common Laragon PHP paths
    const possiblePhpPaths = [
        'C:\\laragon\\bin\\php\\php-8.3.30-Win32-vs16-x64\\php.exe',
        'C:\\laragon\\bin\\php\\php-8.3.14\\php.exe',
        'C:\\laragon\\bin\\php\\php-8.1.10-Win32-vs16-x64\\php.exe',
        'C:\\xampp\\php\\php.exe',
        'C:\\wamp64\\bin\\php\\php*.exe', // WAMP
    ];

    for (const phpPath of possiblePhpPaths) {
        try {
            const fs = require('fs');
            if (fs.existsSync(phpPath)) {
                phpBinary = phpPath;
                break;
            }
        } catch (e) {
            // Continue searching
        }
    }
}

try {
    const command = `${phpBinary} artisan ${process.argv.slice(2).join(' ')}`;
    const result = execSync(command, { stdio: 'inherit', cwd: __dirname });
    process.exit(0);
} catch (error) {
    process.exit(error.status || 1);
}
