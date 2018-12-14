<?php
	
	require_once 'config/DatabaseConfig.class.php';

	class DBUTils {

		private $connection;

		function __construct() {
			$dbConfig = new DatabaseConfig();
			//$this->connection = pg_connect($dbConfig->getHost(),$dbConfig->getPort(),$dbConfig->getDataBase(),$dbConfig->getUser(),$dbConfig->getPassword());

			$this->connection = pg_connect("host=177.105.6.5 port=5432 dbname=sigaa user=desenvolvedor password=d3s3nvs1g");
		}

		public function executarConsulta($sql) {
			//pg_query($this->connection,'SET CHARACTER SET utf8');
			$query = pg_query($this->connection,$sql);
			while ($aux = pg_fetch_array($query)) {
				$rows[] = $aux;
			}
			return @$rows;
		}

	}

?>