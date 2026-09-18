<?php

namespace Model;

require_once __DIR__ . '/../Config/configuration.php';

use PDO;
use PDOException;
use RuntimeException;

class Connection
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            try {
                // Montado sem sprintf para evitar erro de quantidade de argumentos.
                $dsn = 'pgsql:host=' . DB_HOST
                    . ';port=' . DB_PORT
                    . ';dbname=' . DB_NAME;

                if (defined('DB_SSLMODE') && DB_SSLMODE !== '') {
                    $dsn .= ';sslmode=' . DB_SSLMODE;
                }

                self::$instance = new PDO($dsn, DB_USER, DB_PASSWORD, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $error) {
                error_log('Erro de conexão com o banco: ' . $error->getMessage());
                throw new RuntimeException('Não foi possível conectar ao banco de dados.');
            }
        }

        return self::$instance;
    }
}
