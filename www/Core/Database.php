<?php
namespace App\Core;

class Database {
    /**
     * Tableau stockant les instances de chaque classe Singleton
     * Permet le support de l'héritage
     */
    private static array $instances = [];

    private \PDO $pdo;

    /**
     * Le constructeur doit être protected pour permettre l'héritage
     * mais empêcher l'instanciation directe avec new
     */
    protected function __construct() {
        try {

            $this->pdo = new \PDO( 'pgsql:dbname=devdb;host=db' , 'devuser', 'devpass');

        } catch (\PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    /**
     * Méthode statique pour accéder à l'instance unique
     * Utilise Late Static Binding (static::class) pour supporter l'héritage
     */
    public static function getInstance(): static {
        $cls = static::class;
        if (!isset(self::$instances[$cls])) {
            self::$instances[$cls] = new static();
        }

        return self::$instances[$cls];
    }

    /**
     * Récupérer l'objet PDO
     */
    public function getPdo(): \PDO {
        return $this->pdo;
    }

    /**
     * Empêcher le clonage
     */
    protected function __clone() {}

    /**
     * Empêcher la désérialisation
     */
    public function __wakeup() {
        throw new \Exception("Cannot unserialize a singleton.");
    }
}