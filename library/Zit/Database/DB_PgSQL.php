<?php
class DB_PgSQL
{
    public function createConnexion() {
        return new PDO("mysql:host=$this->dbHost;dbname=$this->dbName", 
            $this->dbUser, 
            $this->dbPassword);
    }
}