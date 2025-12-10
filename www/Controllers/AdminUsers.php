<?php

namespace App\Controllers;

use App\Core\Render;
use App\Models\User;

class AdminUsers
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Liste de tous les utilisateurs
     */
    public function list(): void
    {
        Auth::requireAdmin();

        $users = $this->userModel->findAll();

        $render = new Render("admin/users-list", "backoffice");
        $render->assign("users", json_encode($users));
        $render->render();
    }

    /**
     * Formulaire d'édition d'un utilisateur
     */
    public function edit(): void
    {
        Auth::requireAdmin();

        $userId = $_GET['id'] ?? null;
        $errors = [];
        $success = false;
        if (!$userId) {
            die("ID utilisateur manquant");
        }

        $user = $this->userModel->findById($userId);

        if (!$user) {
            die("Utilisateur introuvable");
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST'
            && (count($_POST) == 4 || count($_POST) == 5)
            && isset($_POST["firstname"])
            && isset($_POST["lastname"])
            && isset($_POST["email"])
            && isset($_POST["role_id"])) {



            // Traitement du formulaire
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $data = [
                    'firstname' => trim($_POST['firstname'] ?? ''),
                    'lastname' => trim($_POST['lastname'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'is_active' => isset($_POST['is_active']),
                    'role_id' => (int)($_POST['role_id'] ?? 2)
                ];

                // Validation
                if (empty($data['firstname'])) {
                    $errors[] = "Le prénom est requis";
                }
                if (empty($data['lastname'])) {
                    $errors[] = "Le nom est requis";
                }
                if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Email invalide";
                }

                // Mise à jour
                if (empty($errors)) {
                    if ($this->userModel->update($userId, $data)) {
                        $success = true;
                        $user = $this->userModel->findById($userId); // Recharger les données
                    } else {
                        $errors[] = "Erreur lors de la mise à jour";
                    }
                }
            }
        }

        $render = new Render("admin/users-edit", "backoffice");
        $render->assign("user", json_encode($user));
        $render->assign("errors", json_encode($errors));
        $render->assign("success", $success ? "true" : "false");
        $render->render();
    }

    /**
     * Supprimer un utilisateur
     */
    public function delete(): void
    {
        Auth::requireAdmin();

        $userId = $_GET['id'] ?? null;

        if (!$userId) {
            die("ID utilisateur manquant");
        }

        // Ne pas permettre de se supprimer soi-même
        if ($userId == $_SESSION['user_id']) {
            die("Vous ne pouvez pas supprimer votre propre compte");
        }

        if ($this->userModel->delete($userId)) {
            header('Location: /admin/users');
            exit;
        } else {
            die("Erreur lors de la suppression");
        }
    }
}
