<?php

class DB
{
    private $dbHost;
    private $dbName;
    private $dbUser;
    private $dbPassword;

    public function __construct($dbHost, $dbName, $dbUser, $dbPassword) {
        $this->dbHost = $dbHost;
        $this->dbName = $dbName;
        $this->dbUser = $dbUser;
        $this->dbPassword = $dbPassword;
    }

    abstract public function createConnexion();
}
