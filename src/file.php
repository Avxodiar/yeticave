<?php
namespace yeticave\file;

/**
 * Перевод едениц измерения информации в сокращенном классическом байтовом виде (1Mb = 1024kb)
 * Важно! $ext и $lang должны быть на одном языке!
 * @param int    $size  - размер в битах
 * @param int    $round - кол-во точек после запятой при округлении результата
 * @param string $ext   - вывод в указанной степени представления, например чтобы вместо 1000Kb вывести 0.977Mb
 * @param string $lang  - язык представления
 * @return string
 */
function getShortSize(int $size, int $round = 2, string $ext = 'auto', $lang = 'ru') {
    $suffix = [
        'eng' => ['Byte', 'Kb', 'Mb', 'Gb', 'Tb', 'Pb', 'Eb', 'Zb', 'Yb'],
        'ru' => ['Байт', 'Кб', 'Мб', 'Гб', 'Тб', 'Пб', 'Эб', 'Зб', 'Йб']
    ];

    $ext = (empty($ext) || !in_array($ext, $suffix[$lang], false)) ? 'auto' : $ext;

    foreach ($suffix[$lang] as $key => $abbr) {
        if($abbr === $ext || ($ext === 'auto' && $size < 1024)) {
            $ext = $abbr;
            break;
        }
        $size /= 1024;
    }

    return round($size, $round) . $ext;
}

/**
 * Проверка допустимых форматов изображений
 * @param string $fileUrl - ссылка на файл от корня сайта
 * @return string
 */
function checkSupportImageType(string $fileUrl) {
    $fMimeType = mime_content_type($fileUrl);

    $error = '';
    if( strpos($fMimeType, 'image/') === false ) {
        $error = 'Файл не является изображением';
    } else {
        $type = substr($fMimeType, 6);
        if (!in_array($type, SUPPORTED_IMAGES, false)) {
            $error = 'Формат изображения не поддерживается. Допускаются только ' . implode(', ', SUPPORTED_IMAGES);
        }
    }
    return $error;
}

/**
 * Проверка каталога на существование
 * создает каталог при его отсуствии
 * @param string $dir
 * @return bool
 */
function checkDir(string $dir) {
    $dirPath = ROOT . $dir;
    return !(!is_dir($dirPath) && !mkdir($dirPath) && !is_dir($dirPath));
}

/**
 * Загрузка файла из форме
 * @param array  $upload - массив полученный из POST для input[type=file]
 * @param string $dir - каталог для загрузки файлов
 * @return array
 * @todo добавить логирование ошибок
 */
function upload(array $upload, string $dir) {
    $error = '';
    if (empty($dir)) {
        // 'Не указан каталог для загрузки файлов';
        $error = 'Серверная ошибка загрузки изображения. Попробуйте позже';
    }
    elseif ( !checkDir($dir)) {
        // "Указанный каталог {$dir} не существует или нет доступа";
        $error = 'Серверная ошибка загрузки изображения. Попробуйте позже';
    }
    elseif ( empty($upload) || !is_array($upload)) {
        $error = 'Не указан загружаемый файл';
    }
    elseif ( $upload['size'] > UPLOAD_MAX_SIZE) {
        $error = 'Превышен максимальный размер файла: '.  getShortSize(UPLOAD_MAX_SIZE);
    }

    $file_name = '';
    if (empty($error)) {
        $err = checkSupportImageType($upload['tmp_name']);
        if(!empty($err)) {
            $error = $err;
        } else {
            $file_name = $upload['name'];
            move_uploaded_file( $upload['tmp_name'], ROOT . $dir . $file_name );
        }
    }

    return [$file_name, $error];
}

