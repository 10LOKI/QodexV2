<?php
class Category
{
    private int $id;
    private string $nom;
    private ?string $description;
    private string $created_at;

    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->nom; }
    public function getDescription(): ?string { return $this->description; }

    public static function getAll(PDO $db): array
    {
        $sql = "SELECT c.id,c.nom,c.description, COUNT(q.id) as nb_quiz
                FROM categories c
                LEFT JOIN quiz q ON c.id = q.categorie_id AND q.is_active = 1
                GROUP BY c.id
                ORDER BY c.nom ASC";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById(PDO $db, int $id): ?self
    {
        $sql = "SELECT id, nom, description, created_at FROM categories WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        $obj = $stmt->fetch();
        return $obj ?: null;
    }
}

?>