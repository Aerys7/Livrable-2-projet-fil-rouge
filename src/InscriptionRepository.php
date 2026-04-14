<?php

require_once "Database.php";

class InscriptionRepository
{

    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function all()
    {
        return $this->pdo->query("SELECT * FROM inscriptions")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($data)
    {
        $stmt = $this->pdo->prepare(
            "INSERT INTO inscriptions (nom, email, date, heure, formation_id, status)
             VALUES (?, ?, ?, ?, ?, ?)"
        );

        $stmt->execute([
            $data["nom"],
            $data["email"],
            $data["date"],
            $data["heure"],
            $data["formation_id"],
            $data["status"]
        ]);
    }

    public function update($id, $fields)
    {

        $set = [];
        $values = [];

        foreach ($fields as $key => $value) {
            $set[] = "$key = ?";
            $values[] = $value;
        }

        $values[] = $id;

        $sql = "UPDATE inscriptions SET " . implode(", ", $set) . " WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($values);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM inscriptions WHERE id = ?");
        $stmt->execute([$id]);
    }
}
