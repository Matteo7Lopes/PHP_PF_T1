<?php
namespace App\Models;

use App\Core\Database;

class User {
    private \PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    /**
     * Créer un nouvel utilisateur
     * @return array|false Retourne ['user_id' => int, 'token' => string] ou false en cas d'erreur
     */
    public function create(string $email, string $password, string $firstname, string $lastname, int $roleId = 2): array {
        try {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+24 hours'));

            $this->pdo->beginTransaction();

            // Insérer l'utilisateur
            $sql = 'INSERT INTO public."user" (firstname, lastname, email, pwd, date_created, role_id)
                    VALUES (:firstname, :lastname, :email, :pwd, CURRENT_DATE, :role_id) RETURNING id';

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                "firstname" => $firstname,
                "lastname" => $lastname,
                "email" => $email,
                "pwd" => $hashedPassword,
                "role_id" => $roleId
            ]);

            $userId = $stmt->fetchColumn();

            // Créer le token de validation
            $stmtToken = $this->pdo->prepare(
                "INSERT INTO public.user_tokens (user_id, token, type, expiry)
                 VALUES (:user_id, :token, 'validation', :expiry)"
            );
            $stmtToken->execute([
                "user_id" => $userId,
                "token" => $token,
                "expiry" => $expiry
            ]);

            $this->pdo->commit();

            // Retourner l'ID et le token pour l'envoi d'email
            return [
                'user_id' => $userId,
                'token' => $token
            ];
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("Erreur création utilisateur: " . $e->getMessage());
            }
            return [];
        }
    }

    /**
     * Trouver un utilisateur par email
     */
    public function findByEmail(string $email): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM "user" WHERE email = ?');
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Trouver un utilisateur par ID
     */
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM "user" WHERE id = ?');
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Vérifier les credentials
     */
    public function checkCredentials(string $email, string $password): ?array {
        $user = $this->findByEmail($email);

        if ($user && password_verify($password, $user['pwd'])) {
            return $user;
        }

        return null;
    }

    /**
     * Activer un compte utilisateur
     */
    public function activateAccount(string $token): bool {
        try {
            $this->pdo->beginTransaction();

            // Vérifier le token
            $stmt = $this->pdo->prepare(
                "SELECT user_id FROM user_tokens
                 WHERE token = ? AND type = 'validation'
                 AND expiry > NOW()"
            );
            $stmt->execute([$token]);
            $result = $stmt->fetch();

            if (!$result) {
                $this->pdo->rollBack();
                return false;
            }

            $userId = $result['user_id'];

            // Activer l'utilisateur
            $stmtUpdate = $this->pdo->prepare(
                'UPDATE "user" SET is_active = true, date_updated = CURRENT_DATE WHERE id = ?'
            );
            $stmtUpdate->execute([$userId]);

            // Supprimer le token utilisé
            $stmtDelete = $this->pdo->prepare(
                "DELETE FROM user_tokens WHERE token = ?"
            );
            $stmtDelete->execute([$token]);

            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            if (DEBUG_MODE) {
                error_log("Erreur activation compte: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Générer un token de reset password
     */
    public function createResetToken(string $email): ?string {
        $user = $this->findByEmail($email);
        if (!$user) {
            return null;
        }

        try {
            $token = bin2hex(random_bytes(32));

            // Supprimer les anciens tokens de reset
            $stmtDelete = $this->pdo->prepare(
                "DELETE FROM user_tokens WHERE user_id = ? AND type = 'reset'"
            );
            $stmtDelete->execute([$user['id']]);

            // Créer le nouveau token
            $stmt = $this->pdo->prepare(
                "INSERT INTO user_tokens (user_id, token, type, expiry)
                 VALUES (?, ?, 'reset', NOW() + INTERVAL '1 hour')"
            );
            $stmt->execute([$user['id'], $token]);

            return $token;
        } catch (\PDOException $e) {
            if (DEBUG_MODE) {
                error_log("Erreur création token reset: " . $e->getMessage());
            }
            return null;
        }
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function resetPassword(string $token, string $newPassword): bool {
        try {
            $this->pdo->beginTransaction();

            // Vérifier le token
            $stmt = $this->pdo->prepare(
                "SELECT user_id FROM user_tokens
                 WHERE token = ? AND type = 'reset'
                 AND expiry > NOW()"
            );
            $stmt->execute([$token]);
            $result = $stmt->fetch();

            if (!$result) {
                $this->pdo->rollBack();
                return false;
            }

            $userId = $result['user_id'];
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

            // Mettre à jour le mot de passe
            $stmtUpdate = $this->pdo->prepare(
                'UPDATE "user" SET pwd = ?, date_updated = CURRENT_DATE WHERE id = ?'
            );
            $stmtUpdate->execute([$hashedPassword, $userId]);

            // Supprimer le token
            $stmtDelete = $this->pdo->prepare(
                "DELETE FROM user_tokens WHERE token = ?"
            );
            $stmtDelete->execute([$token]);

            $this->pdo->commit();
            return true;
        } catch (\PDOException $e) {
            $this->pdo->rollBack();
            if (DEBUG_MODE) {
                error_log("Erreur reset password: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Récupérer tous les utilisateurs (pour le BO)
     */
    public function findAll(): array {
        $stmt = $this->pdo->query(
            'SELECT u.*, STRING_AGG(r.name, \', \') as roles
             FROM "user" u
             LEFT JOIN user_roles ur ON u.id = ur.user_id
             LEFT JOIN roles r ON ur.role_id = r.id
             GROUP BY u.id
             ORDER BY u.date_created DESC'
        );
        return $stmt->fetchAll();
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(int $id, array $data): bool {
        try {
            $fields = [];
            $values = [];

            if (isset($data['firstname'])) {
                $fields[] = 'firstname = ?';
                $values[] = $data['firstname'];
            }
            if (isset($data['lastname'])) {
                $fields[] = 'lastname = ?';
                $values[] = $data['lastname'];
            }
            if (isset($data['email'])) {
                $fields[] = 'email = ?';
                $values[] = $data['email'];
            }
            if (isset($data['is_active'])) {
                $fields[] = 'is_active = ?';
                $values[] = $data['is_active'];
            }
            if (isset($data['role_id'])) {
                $fields[] = 'role_id = ?';
                $values[] = $data['role_id'];
            }
            $fields[] = 'date_updated = CURRENT_DATE';
            $values[] = $id;

            $sql = 'UPDATE "user" SET ' . implode(', ', $fields) . ' WHERE id = ?';
            $stmt = $this->pdo->prepare($sql);

            return $stmt->execute($values);
        } catch (\PDOException $e) {
            if (DEBUG_MODE) {
                error_log("Erreur update user: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function delete(int $id): bool {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM "user" WHERE id = ?');
            return $stmt->execute([$id]);
        } catch (\PDOException $e) {
            if (DEBUG_MODE) {
                error_log("Erreur delete user: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Vérifier si l'utilisateur a un rôle
     */
    public function hasRole(int $userId, string $roleName): bool {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM user_roles ur
             JOIN roles r ON ur.role_id = r.id
             WHERE ur.user_id = ? AND r.name = ?"
        );
        $stmt->execute([$userId, $roleName]);
        return $stmt->fetchColumn() > 0;
    }
}