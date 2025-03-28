const fs = require('fs');
const { exec } = require('child_process');
const path = require('path');

// Путь к директории с языковыми файлами
const langDir = path.resolve(process.cwd(), 'resources/lang');
console.log(`Отслеживание изменений в директории: ${langDir}`);

// Компилируем переводы при запуске
compileTranslations();

// Функция для поиска всех PHP-файлов в директории
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

// Получаем список всех PHP-файлов
const phpFiles = findPhpFiles(langDir);
console.log(`Найдено ${phpFiles.length} PHP файлов для отслеживания`);

// Отслеживаем каждый файл индивидуально
phpFiles.forEach(file => {
    console.log(`Отслеживание файла: ${file}`);
    fs.watchFile(file, { interval: 1000 }, (curr, prev) => {
        if (curr.mtime > prev.mtime) {
            console.log(`Файл изменен: ${file}`);
            compileTranslations();
        }
    });
});

// Функция компиляции переводов
function compileTranslations() {
    console.log('Компиляция переводов...');
    exec('php artisan translations:compile', (error, stdout) => {
        if (error) {
            console.error(`Ошибка компиляции: ${error.message}`);
            return;
        }
        console.log(stdout);
    });
}

console.log('Отслеживание запущено...');

// Добавьте в конец текущего скрипта
// setInterval(() => {
//     const newFiles = findPhpFiles(langDir);
//     const currentWatchedFiles = phpFiles.slice();
//
//     // Проверка на новые файлы
//     newFiles.forEach(file => {
//         if (!currentWatchedFiles.includes(file)) {
//             console.log(`Обнаружен новый файл: ${file}`);
//             phpFiles.push(file);
//             fs.watchFile(file, { interval: 1000 }, (curr, prev) => {
//                 if (curr.mtime > prev.mtime) {
//                     console.log(`Файл изменен: ${file}`);
//                     compileTranslations();
//                 }
//             });
//         }
//     });
// }, 30000); // Проверка каждые 30 секунд
