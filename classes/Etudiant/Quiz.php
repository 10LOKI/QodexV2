<?php
class Quiz
{
    private int $id ;
    private string $titre ;
    private string $description;
    private  int $is_active ;
    private string $created_at;

    public function getId(): int
    {
        return $this -> id;
    }
    public function getTitle(): string
    {
        return $this -> titre;
    }
    public function getDescription(): string
    {
        return $this -> description;
    }
    public static function getByCategory(PDO $db,int $categoryId): array
    {
        $sql = "SELECT q.id,q.titre,q.description,q.is_active,q.created_at FROM quiz q WHERE q.categorie_id = :category_id AND q.is_active =1";
        $stmt = $db -> prepare($sql);
        $stmt -> bindValue(':category_id',$categoryId, PDO::PARAM_INT);
        $stmt -> execute();
        return $stmt -> fetchAll(PDO::FETCH_CLASS, self::class);
    }
}

?>