<?php

	class DataBaseConfig{

		private $host = "177.105.6.5";
		private $dataBase = "postgres";
		private $port = 5432;
		private $user = "desenvolvedor";
		private $password = "d3s3nvs1g";

		public function getHost() {
			return $this->host;
		}

		public function getPort(){
			return $this->port;
		}

		public function getDataBase() {
			return $this->dataBase;
		}

		public function getUser() {
			return $this->user;
		}

		public function getPassword() {
			return $this->password;
		}

	}

?>