<?php
namespace App\Models;

use App\Core\Database;

class Page
{
    private \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
    }

    /**
     * Créer une nouvelle page
     */
    public function create(array $data): int|false
    {
        try {
            $sql = 'INSERT INTO public.pages (title, slug, content, meta_description, is_published, author_id, created_at) VALUES (:title, :slug, :content, :meta_description, :is_published, :author_id, CURRENT_DATE) RETURNING id';

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':title', $data['title']);
            $stmt->bindValue(':slug', $data['slug']);
            $stmt->bindValue(':content', $data['content'] ?? '');
            $stmt->bindValue(':meta_description', $data['meta_description'] ?? '');

            $stmt->bindValue(':is_published', $data['is_published'], \PDO::PARAM_BOOL);

            $stmt->bindValue(':author_id', $data['author_id'], \PDO::PARAM_INT);

            $stmt->execute();


            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            var_dump($data);
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("Erreur création page: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Trouver une page par ID
     */
    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM public.pages WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Trouver une page par slug
     */
    public function findBySlug(string $slug): ?array
    {

        $stmt = $this->pdo->prepare('SELECT * FROM public.pages WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);

        $result = $stmt->fetch();


        return $result ?: null;
    }

    public function findAllBySlug(string $slug): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM public.pages WHERE slug = :slug');
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Récupérer toutes les pages
     */
    public function findAll(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM public.pages ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    /**
     * Récupérer toutes les pages publiées
     */
    public function findPublished(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM public.pages WHERE is_published = true ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }

    /**
     * Mettre à jour une page
     */
    public function update(int $id, array $data): bool
    {
        try {
            $sql = 'UPDATE public."pages"
            SET title = :title,
                slug = :slug,
                content = :content,
                meta_description = :meta_description,
                is_published = :is_published,
                updated_at = CURRENT_DATE
            WHERE id = :id';

            $stmt = $this->pdo->prepare($sql);

            $stmt->bindValue(':title', $data['title']);

            $stmt->bindValue(':slug', $data['slug']);
            $stmt->bindValue(':content', $data['content']);
            $stmt->bindValue(':meta_description', $data['meta_description']);

            $stmt->bindValue(':is_published', $data['is_published'], \PDO::PARAM_BOOL);

            $stmt->bindValue(':id', $id, \PDO::PARAM_INT);

            return $stmt->execute();
        } catch (\PDOException $e) {
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("Erreur update page: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Supprimer une page
     */
    public function delete(int $id): bool
    {
        try {
            $stmt = $this->pdo->prepare('DELETE FROM public.pages WHERE id = :id');
            return $stmt->execute(['id' => $id]);
        } catch (\PDOException $e) {
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("Erreur delete page: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Générer un slug unique depuis un titre
     */
    public function generateSlug(string $title): string
    {
        // Convertir en slug
        $slug = strtolower(trim($title));
        $slug = urlencode($slug);

        // Vérifier l'unicité
        $originalSlug = $slug;
        $counter = 1;
        $flag = true;
        while ($flag == true) {
            $sql = 'SELECT COUNT(*) FROM public.pages WHERE slug = :slug';


            $stmt = $this->pdo->prepare($sql);
            $params = ['slug' => $slug];

            $stmt->execute($params);

            if ($stmt->fetchColumn() == 0) {
                $flag = false;
                return $slug;
            }

            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
    }

}

