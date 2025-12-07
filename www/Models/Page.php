<?php
namespace App\Models;

use App\Core\Database;

class Page {
    private \PDO $pdo;
    
    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }
    
    /**
     * Créer une nouvelle page
     */
    public function create(array $data): int|false {
        try {
            $sql = 'INSERT INTO public.pages (title, slug, content, meta_description, is_published, author_id, created_at) 
                    VALUES (:title, :slug, :content, :meta_description, :is_published, :author_id, CURRENT_DATE) 
                    RETURNING id';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'content' => $data['content'] ?? '',
                'meta_description' => $data['meta_description'] ?? '',
                'is_published' => $data['is_published'] ?? false,
                'author_id' => $data['author_id']
            ]);
            
            return $stmt->fetchColumn();
        } catch (\PDOException $e) {
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                error_log("Erreur création page: " . $e->getMessage());
            }
            return false;
        }
    }
    
    /**
     * Trouver une page par ID
     */
    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM public.pages WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Trouver une page par slug
     */
    public function findBySlug(string $slug): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM public.pages WHERE slug = :slug AND is_published = true');
        $stmt->execute(['slug' => $slug]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
    
    /**
     * Récupérer toutes les pages
     */
    public function findAll(): array {
        $stmt = $this->pdo->query('SELECT * FROM public.pages ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }
    
    /**
     * Récupérer toutes les pages publiées
     */
    public function findPublished(): array {
        $stmt = $this->pdo->query('SELECT * FROM public.pages WHERE is_published = true ORDER BY created_at DESC');
        return $stmt->fetchAll();
    }
    
    /**
     * Mettre à jour une page
     */
    public function update(int $id, array $data): bool {
        try {
            $fields = [];
            $values = [];
            
            if (isset($data['title'])) {
                $fields[] = 'title = :title';
                $values['title'] = $data['title'];
            }
            if (isset($data['slug'])) {
                $fields[] = 'slug = :slug';
                $values['slug'] = $data['slug'];
            }
            if (isset($data['content'])) {
                $fields[] = 'content = :content';
                $values['content'] = $data['content'];
            }
            if (isset($data['meta_description'])) {
                $fields[] = 'meta_description = :meta_description';
                $values['meta_description'] = $data['meta_description'];
            }
            if (isset($data['is_published'])) {
                $fields[] = 'is_published = :is_published';
                $values['is_published'] = $data['is_published'];
            }
            
            $fields[] = 'updated_at = CURRENT_DATE';
            $values['id'] = $id;
            
            $sql = 'UPDATE public.pages SET ' . implode(', ', $fields) . ' WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            
            return $stmt->execute($values);
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
    public function delete(int $id): bool {
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
    public function generateSlug(string $title, ?int $excludeId = null): string {
        // Convertir en slug
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Vérifier l'unicité
        $originalSlug = $slug;
        $counter = 1;
        
        while (true) {
            $sql = 'SELECT COUNT(*) FROM public.pages WHERE slug = :slug';
            if ($excludeId) {
                $sql .= ' AND id != :exclude_id';
            }
            
            $stmt = $this->pdo->prepare($sql);
            $params = ['slug' => $slug];
            if ($excludeId) {
                $params['exclude_id'] = $excludeId;
            }
            $stmt->execute($params);
            
            if ($stmt->fetchColumn() == 0) {
                return $slug;
            }
            
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
    }
}
