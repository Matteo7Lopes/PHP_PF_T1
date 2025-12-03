<?php
namespace App\Controllers;

use App\Core\Render;
use App\Models\User;
use App\Helpers\Mailer;

class Auth
{
    private User $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Vérifier si l'utilisateur est connecté
     * Redirige vers /login si non connecté
     */
    public static function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    /**
     * Vérifier si l'utilisateur est admin
     * Redirige vers / si pas admin
     */
    public static function requireAdmin(): void
    {
        self::requireAuth();

        if ($_SESSION['user_role_id'] != 1) {
            header('Location: /');
            exit;
        }
    }

    /**
     * Vérifier si l'utilisateur est déjà connecté
     * Redirige vers / si déjà connecté (pour pages login/register)
     */
    public static function requireGuest(): void
    {
        if (isset($_SESSION['user_id'])) {
            header('Location: /');
            exit;
        }
    }

    /**
     * Afficher et traiter le formulaire d'inscription
     */
    public function register(): void
    {
        // Rediriger si déjà connecté
        self::requireGuest();

        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Récupération et nettoyage des données
            $firstname = trim($_POST['firstname'] ?? '');
            $lastname = trim($_POST['lastname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validation
            if (empty($firstname)) {
                $errors[] = "Le prénom est requis";
            }
            if (empty($lastname)) {
                $errors[] = "Le nom est requis";
            }
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email invalide";
            }
            if (strlen($password) < 8) {
                $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
            }
            if ($password !== $confirmPassword) {
                $errors[] = "Les mots de passe ne correspondent pas";
            }

            // Vérifier si l'email existe déjà
            if (empty($errors) && $this->userModel->findByEmail($email)) {
                $errors[] = "Cet email est déjà utilisé";
            }

            // Créer l'utilisateur
            if (empty($errors)) {
                $result = $this->userModel->create($email, $password, $firstname, $lastname);

                if ($result) {
                    // Envoyer l'email de confirmation
                    $mailer = new Mailer();
                    $emailSent = $mailer->sendValidationEmail(
                        $email,
                        $firstname,
                        $result['token']
                    );

                    if ($emailSent) {
                        $success = true;
                    } else {
                        $errors[] = "Inscription réussie mais l'email n'a pas pu être envoyé";
                    }
                } else {
                    $errors[] = "Erreur lors de l'inscription";
                }
            }
        }

        $render = new Render("register", "frontoffice");
        $render->assign("errors", json_encode($errors));
        $render->assign("success", $success ? "true" : "false");
        $render->render();
    }

    /**
     * Valider un compte via token
     */
    public function validate(): void
    {
        $token = $_GET['token'] ?? '';
        $success = false;
        $message = '';

        if (empty($token)) {
            $message = "Token invalide";
        } else {
            if ($this->userModel->activateAccount($token)) {
                $success = true;
                $message = "Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.";
            } else {
                $message = "Le token est invalide ou a expiré";
            }
        }

        $render = new Render("validate", "frontoffice");
        $render->assign("success", $success ? "true" : "false");
        $render->assign("message", $message);
        $render->render();
    }

    /**
     * Afficher et traiter le formulaire de connexion
     */
    public function login(): void
    {
        // Rediriger si déjà connecté
        self::requireGuest();

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $errors[] = "Email et mot de passe requis";
            } else {
                $user = $this->userModel->checkCredentials($email, $password);

                if ($user) {
                    if (!$user['is_active']) {
                        $errors[] = "Votre compte n'est pas encore activé. Vérifiez vos emails.";
                    } else {
                        // Créer la session
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['user_email'] = $user['email'];
                        $_SESSION['user_firstname'] = $user['firstname'];
                        $_SESSION['user_role_id'] = $user['role_id'];

                        // Rediriger selon le rôle
                        if ($user['role_id'] == 1) { // Admin
                            header('Location: /admin');
                        } else {
                            header('Location: /');
                        }
                        exit;
                    }
                } else {
                    $errors[] = "Email ou mot de passe incorrect";
                }
            }
        }

        $render = new Render("login", "frontoffice");
        $render->assign("errors", json_encode($errors));
        $render->render();
    }

    /**
     * Déconnexion
     */
    public function logout(): void
    {
        session_destroy();
        header('Location: /login');
        exit;
    }

    /**
     * Afficher le formulaire "mot de passe oublié"
     */
    public function forgotPassword(): void
    {
        $errors = [];
        $success = false;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email invalide";
            } else {
                $token = $this->userModel->createResetToken($email);

                if ($token) {
                    // Envoyer l'email de reset
                    $mailer = new Mailer();
                    $emailSent = $mailer->sendResetPasswordEmail($email, $token);

                    if ($emailSent) {
                        $success = true;
                    } else {
                        $errors[] = "Erreur lors de l'envoi de l'email";
                    }
                } else {
                    // Ne pas révéler si l'email existe ou non (sécurité)
                    $success = true;
                }
            }
        }

        $render = new Render("forgot-password", "frontoffice");
        $render->assign("errors", json_encode($errors));
        $render->assign("success", $success ? "true" : "false");
        $render->render();
    }

    /**
     * Afficher le formulaire de reset password
     */
    public function resetPassword(): void
    {
        $token = $_GET['token'] ?? '';
        $errors = [];
        $success = false;

        if (empty($token)) {
            $errors[] = "Token invalide";
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($token)) {
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (strlen($password) < 8) {
                $errors[] = "Le mot de passe doit contenir au moins 8 caractères";
            }
            if ($password !== $confirmPassword) {
                $errors[] = "Les mots de passe ne correspondent pas";
            }

            if (empty($errors)) {
                if ($this->userModel->resetPassword($token, $password)) {
                    $success = true;
                } else {
                    $errors[] = "Le token est invalide ou a expiré";
                }
            }
        }

        $render = new Render("reset-password", "frontoffice");
        $render->assign("token", $token);
        $render->assign("errors", json_encode($errors));
        $render->assign("success", $success ? "true" : "false");
        $render->render();
    }
}