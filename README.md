Установка:

1. Копируем файлы из папок admin и system в директорую установленного магазина

2. Добавляем в файл \config.php и \admin\config.php в конец:

	//debug
	define('DEBUG', true);

2. Добавляем в файл \index.php 
	2.1 в начало файла добавляем:

		// рассчет времени работы
		$sysstart = microtime(true);
		// рассчет памяти
		function_exists('memory_get_usage') ? define('MEM_USAGE', memory_get_usage()) : null;

	2.2  после комментария //Application Classes:
	
		require_once(DIR_SYSTEM . 'library/debug.php');

	2.3 в конец:
	
		// вывод итогового лога отлатчика
		echo !DEBUG ? null : Debug::show($sysstart);

3. файл \system\database\mysql.php строчку $resource = mysql_query($sql, $this->link); заменяем на:

		$sysstart = microtime(true);
		$resource = mysql_query($sql, $this->link);
		$row = array();
		
		if(DEBUG){
			$caller = debug_backtrace();
			$row['file'] = $caller[1]['file'];
			$row['line'] = $caller[1]['line'];
			
			$row['Duration'] = round(( microtime(true) - $sysstart), 5);
			$row['Query'] = $sql;
			
			Debug::$_profs[] =  $row;
		}"# opencart3.2" 
