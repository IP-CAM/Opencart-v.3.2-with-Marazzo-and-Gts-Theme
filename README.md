���������:

1. �������� ����� �� ����� admin � system � ���������� �������������� ��������

2. ��������� � ���� \config.php � \admin\config.php � �����:

	//debug
	define('DEBUG', true);

2. ��������� � ���� \index.php 
	2.1 � ������ ����� ���������:

		// ������� ������� ������
		$sysstart = microtime(true);
		// ������� ������
		function_exists('memory_get_usage') ? define('MEM_USAGE', memory_get_usage()) : null;

	2.2  ����� ����������� //Application Classes:
	
		require_once(DIR_SYSTEM . 'library/debug.php');

	2.3 � �����:
	
		// ����� ��������� ���� ���������
		echo !DEBUG ? null : Debug::show($sysstart);

3. ���� \system\database\mysql.php ������� $resource = mysql_query($sql, $this->link); �������� ��:

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
