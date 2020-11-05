# Личный проект «YetiCave»

<img src="https://up.htmlacademy.ru/static/img/intensive/htmlcss/logo-for-github-2.png" align="left" width="120" height="120" alt="HTML Academy">

Выполнен в рамках интенсива<br>
«Профессиональный PHP, уровень 1» от [HTML Academy](https://htmlacademy.ru)<br>

Используемые технологии: PHP 7.2, MySQL 5.7, процедурный стиль c паттерном "Page Controller" + Composer с автозагрузкой и пакетом SwiftMailer для работы с почтой <br>
Фреймворки: не используются - условие обучения<br>

Спецификация: [Specification.md](https://github.com/Avxodiar/yeticave/blob/master/Specification.md)<br>
Детальное задание [tz_detail.pdf](https://github.com/Avxodiar/portfolio/yeticave/blob/master/tz_detail.pdf)<br>
Тех.задание [tz_yeticave.doc](https://github.com/Avxodiar/portfolio/yeticave/blob/master/tz_yeticave.docx)

Примечания:
1. В соответствии с ТЗ п.6, в корне проекта находится файл queries.sql со схемой БД и примерами запросов на выборку/изменение данных. Более логичным в корне хранить только файл со схемой создания БД и ее структурой, а дополнительное описание с примерами запросов к БД вынести в отдельный файл. А еще правильнее было бы использовать инсталятор (install.php или Makefile), но это уже совсем другая история...
2. В соответствии с ТЗ п.9.2, в файле index.php подключается скрипт getwinner.php, занимающийся определением ставок у завершенных лотов и рассылкой писем победителям.
 Правильнее было бы данный скрипт повесить на крон и удалить его вызов в index.php.


## Пользователи для тестирования (e-mail / пароль)

ignat.v@gmail.com / ug0GdVMi<br>
kitty_93@li.ru    / daecNazD<br>
warrior07@mail.ru / oixb3aL8<br>

--

_Не удаляйте и не обращайте внимание на файлы:_<br>
_`.editorconfig`, `.gitattributes`, `.gitignore`, `.travis.yml`, `package.json`._

