<?php

namespace App\Services;

use Illuminate\Database\Connectors\SqlServerConnector as BaseSqlServerConnector;
use PDO;

class CustomSqlServerConnector extends BaseSqlServerConnector
{
    protected $options = [
        PDO::ATTR_CASE => PDO::CASE_NATURAL,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL,
        // 🚨 eliminamos esto:
        // PDO::ATTR_STRINGIFY_FETCHES => false,
    ];
}