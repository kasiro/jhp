# jhp

jhp Это php Препроцессор Написанный на php, Что бы улучшить Стандартный php код,
Но Юзать его можно к сожалению только для полностью php файлов,
Но пожете попробовать и не только на них, Уже сами.

"Что бы улучшить Стандартный php код" (ну или просто для ленивых ¯ \ _ (ツ) _ / ¯)

## Минусы
- Но увы надо знать Регулярные выражения без них никак

## Плюсы

- Свобода / Простор для действий
- Вы можете создавать свои модули и свой функционал / Синтаксис который вам нужен
- Модульность

## О модулях

Модулями являются обычные php файлы в папке modules

## Установка
Просто скачай и распакуй Архив
И распакуй его куда надо

## Как всё работает
##### Ты берёшь handleMain.php
##### И запускаешь вот так: php `<your_path>`/handleMain.php `<your_path_to_file>`/main_file.jhp
##### в директории `<your_path_to_file>` создасться php файл с таким же именем

## Примеры кода
### nl (new line) - Что бы не писать постоянно . "\n" (Модуль: echo_new_line)
```php
nl text; -> echo text . "\n";
```
### Стрелочные функции (Модуль: arrow_func)
```php
$console_log = $command => {
	nl '$ ' . $command;
};
->
$console_log = function($command) {
	echo '$ ' . $command . "\n";
};
```
```php
$var = fn($var1, $var2) use ($console_log) => {
    $console_log('text');
};
->
$var = function($var1, $var2) use ($console_log) {
    $console_log('text');
};
```
```php
$var = fn($var1, $var2) {
    
};
->
$var = function($var1, $var2) {
    
};
```

### Методы Классов можно писать без function | __con - Алиас (Модуль: class_no_func)
```php
class Main {
	public __con(){
		# code
	}
}
->
class Main {
	public function __construct(){
		# code
	}
}
________________
class Main {
	public static __con(){
		# code
	}
}
->
class Main {
	public static function __construct(){
		# code
	}
}
________________
class Main {
	static public __con(){
		# code
	}
}
->
class Main {
	static public function __construct(){
	    # code
	}
}
```

### Модуль catch:
#### (\Throwable) Можно настроить в модуле что тут будет юзаться по умолчанию
```php
try {
	# The Code
} catch ($th) {
	# the null
}
->
try {
	# The Code
} catch (\Throwable $th) {
	# the null
}
```

### Модуль js_else: (mini Режим) Настраивается в модуле
```php
$mainString = $var | 'the_string';
->
(mini Режим) == true
$mainString = @$var ? $var : (@'the_string' ? 'the_string' : null);
(mini Режим) == false (для понятливости)
$mainString = @$var ? $var : (
	@'the_string' ? 'the_string' : null
);
```
### Модуль classes:
```php
ClassName::__vnames() -> array_keys(get_class_vars('ClassName'))
ClassName::__vars() -> get_class_vars('ClassName')
```

### Модуль global:
```php
<g $var, $vars> -> global $var, $vars;
```

## Конфиг: jhp.config
### Создаётся кода тебе нужно использовать определённые модули,
### Которые нужно использовать в этой папке
### Или Указать Алиасы в нём

### Стандартные Алиасы
jhp  | php
------------- | -------------
__con()  | __construct()