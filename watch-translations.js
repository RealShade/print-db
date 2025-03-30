const fs = require('fs');
const { exec } = require('child_process');
const path = require('path');

// Шлях до директорії з мовними файлами
const langDir = path.resolve(process.cwd(), 'resources/lang');
console.log(`Відстеження змін у директорії: ${langDir}`);

// Компілюємо переклади при запуску
// compileTranslations();

// Функція для пошуку всіх PHP-файлів у директорії
function findPhpFiles(dir, fileList = []) {
    const files = fs.readdirSync(dir);

    files.forEach(file => {
        const filePath = path.join(dir, file);
        const stat = fs.statSync(filePath);

        if (stat.isDirectory()) {
            findPhpFiles(filePath, fileList);
        } else if (file.endsWith('.php')) {
            fileList.push(filePath);
        }
    });

    return fileList;
}

// Отримуємо список всіх PHP-файлів
const phpFiles = findPhpFiles(langDir);
console.log(`Знайдено ${phpFiles.length} PHP файлів для відстеження`);

// Відстежуємо кожен файл індивідуально
phpFiles.forEach(file => {
    console.log(`Відстеження файлу: ${file}`);
    fs.watchFile(file, { interval: 1000 }, (curr, prev) => {
        if (curr.mtime > prev.mtime) {
            console.log(`Файл змінено: ${file}`);
            compileTranslations();
        }
    });
});

// Функція компіляції перекладів
function compileTranslations() {
    console.log('Компіляція перекладів...');
    exec('php artisan translations:compile', (error, stdout) => {
        if (error) {
            console.error(`Помилка компіляції: ${error.message}`);
            return;
        }
        console.log(stdout);
    });
}

console.log('Відстеження запущено...');

// Додайте в кінець поточного скрипту
// setInterval(() => {
//     const newFiles = findPhpFiles(langDir);
//     const currentWatchedFiles = phpFiles.slice();
//
//     // Перевірка на нові файли
//     newFiles.forEach(file => {
//         if (!currentWatchedFiles.includes(file)) {
//             console.log(`Виявлено новий файл: ${file}`);
//             phpFiles.push(file);
//             fs.watchFile(file, { interval: 1000 }, (curr, prev) => {
//                 if (curr.mtime > prev.mtime) {
//                     console.log(`Файл змінено: ${file}`);
//                     compileTranslations();
//                 }
//             });
//         }
//     });
// }, 30000); // Перевірка кожні 30 секунд

