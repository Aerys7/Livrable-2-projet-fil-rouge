<?php

require_once "Database.php";

class FormationRepository
{

    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function all()
    {
        return $this->pdo->query("SELECT * FROM formations")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM formations WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function add($data)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO formations (titre, duree, prix, actif) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([
            $data["titre"],
            $data["duree"],
            $data["prix"],
            $data["actif"]
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM formations WHERE id = ?");
        $stmt->execute([$id]);
    }
}
