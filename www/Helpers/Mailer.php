<?php
namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        try {
            // Configuration SMTP
            $this->mailer->isSMTP();
            $this->mailer->Host = MAIL_HOST;
            $this->mailer->Port = MAIL_PORT;

            // Authentification (si nécessaire)
            if (!empty(MAIL_USERNAME)) {
                $this->mailer->SMTPAuth = true;
                $this->mailer->Username = MAIL_USERNAME;
                $this->mailer->Password = MAIL_PASSWORD;
            } else {
                $this->mailer->SMTPAuth = false;
            }

            // Encryption (si nécessaire)
            if (!empty(MAIL_ENCRYPTION)) {
                $this->mailer->SMTPSecure = MAIL_ENCRYPTION;
            }

            // Configuration de l'expéditeur
            $this->mailer->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $this->mailer->CharSet = 'UTF-8';

            // Désactiver la vérification SSL en dev (MailHog)
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                $this->mailer->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];
            }

        } catch (Exception $e) {
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("Erreur configuration mailer: " . $e->getMessage());
            }
        }
    }

    /**
     * Envoyer un email de validation de compte
     */
    public function sendValidationEmail(string $to, string $firstname, string $token): bool
    {
        $subject = "Activez votre compte - " . SITE_NAME;
        $validationUrl = SITE_URL . "/validate?token=" . $token;

        $message = "Bonjour {$firstname},

Merci de vous être inscrit sur " . SITE_NAME . " !

Pour activer votre compte, cliquez sur le lien ci-dessous :
{$validationUrl}

Ce lien expire dans 24 heures.

Si vous n'êtes pas à l'origine de cette inscription, ignorez cet email.

---
Cet email a été envoyé automatiquement, merci de ne pas y répondre.";

        return $this->send($to, $subject, $message, false);
    }

    /**
     * Envoyer un email de réinitialisation de mot de passe
     */
    public function sendResetPasswordEmail(string $to, string $token): bool
    {
        $subject = "Réinitialisation de mot de passe - " . SITE_NAME;
        $resetUrl = SITE_URL . "/reset-password?token=" . $token;

        $message = "Réinitialisation de mot de passe

Vous avez demandé à réinitialiser votre mot de passe sur " . SITE_NAME . ".

Pour créer un nouveau mot de passe, cliquez sur le lien ci-dessous :
{$resetUrl}

Ce lien expire dans 1 heure.

Si vous n'êtes pas à l'origine de cette demande, ignorez cet email.
Votre mot de passe actuel reste inchangé.

---
Cet email a été envoyé automatiquement, merci de ne pas y répondre.";

        return $this->send($to, $subject, $message, false);
    }

    /**
     * Fonction générique d'envoi d'email
     */
    private function send(string $to, string $subject, string $message, bool $isHtml = false): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->isHTML($isHtml);

            if ($isHtml) {
                $this->mailer->Body = $message;
            } else {
                $this->mailer->Body = $message;
            }

            $result = $this->mailer->send();

            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("Email envoyé à {$to} : {$subject}");
            }

            return $result;

        } catch (Exception $e) {
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("Erreur envoi email: " . $this->mailer->ErrorInfo);
            }
            return false;
        }
    }
}