Данный модуль позволяет создавать красивые адреса для страниц сайта.
Модуль написан для фреймворка «Kohana».

Подробное описание работы модуля можете прочитать на <a href="https://ifmo.su/alias-system">сайте</a>

Для того, чтобы подключить модуль, вам нужно скопировать репозиторий в папку «modules» и добавить свои настройки.
В файле «bootstrap.php» добавляем название этого модуля и указываем путь к папке.
<code>
<pre>
    Kohana::modules(array(
        'aliases'      => MODPATH.'aliases',    // alias system
        'oauth'      => MODPATH.'oauth',       // Basic authentication
        'cache'      => MODPATH.'cache',      // Caching with multiple backends
        // 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
        'database'   => MODPATH.'database',   // Database access
        'image'      => MODPATH.'image',      // Image manipulation
        'minion'     => MODPATH.'minion',     // CLI Tasks
        'orm'        => MODPATH.'orm',        // Object Relationship Mapping
        'unittest'   => MODPATH.'unittest',   // Unit testing
        // 'userguide'  => MODPATH.'userguide',  // User guide and API documentation
        // 'email'         => MODPATH.'email',
        // 'messages'      => MODPATH.'messages',

    ));
</pre>
</code>
Ссылка на модуль: https://github.com/codex-team/kohana-aliases/

