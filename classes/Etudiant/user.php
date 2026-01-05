<?php
class User 
{
    private int $id;
    private string $nom;
    private string $prenom;
    private string $email;
    private string $role;

    public function getId(): int { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function getFullName(): string { return trim($this->prenom . ' ' . $this->nom); }

    public static function findById(PDO $db, int $id): ?self
    {
        $sql = "SELECT id, nom, prenom, email, role FROM users WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        $obj = $stmt->fetch();
        return $obj ?: null;
    }

    public static function findByEmail(PDO $db, string $email): ?self
    {
        $sql = "SELECT id, nom, prenom, email, role FROM users WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        $obj = $stmt->fetch();
        return $obj ?: null;
    }
}
?>