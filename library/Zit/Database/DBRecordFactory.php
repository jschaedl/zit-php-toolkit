<?php

class DBRecordFactory
{
    public static function createUserDao($db) {
        return new DBRecord($db, 'users');
    }

    public static function createFacultyDao($db) {
        return new DBRecord($db, 'faculty');
    }
}