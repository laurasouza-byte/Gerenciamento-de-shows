<?php

define("DB_HOST", "db.wlxhecabdgyxronivanf.supabase.co");
define("DB_NAME", "postgres");                    
define("DB_USER", "postgres");                     
define("DB_PASSWORD", "La@K1AE6MB7");              
define("DB_PORT", "5432");                           

function getConnection(): PDO
{
    try {
        $pdo = new PDO(
            "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";sslmode=require",
            DB_USER,
            DB_PASSWORD
        );
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Erro na conexão com o banco de dados: " . $e->getMessage());
    }
}
