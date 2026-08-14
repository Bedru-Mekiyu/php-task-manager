<?php

namespace App\Models;

use PDO;

class Task
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAllByUserId($userId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($title, $priority, $userId)
    {
        $sql = "INSERT INTO tasks (title, priority, user_id) VALUES (:title, :priority, :user_id)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ":title" => $title,
            ":priority" => $priority,
            ":user_id" => $userId
        ]);
    }

    public function delete($id, $userId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id = :id AND user_id = :user_id");
        return $stmt->execute([
            ":id" => $id,
            ":user_id" => $userId
        ]);
    }

    public function find($id, $userId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM tasks WHERE id = :id AND user_id = :user_id");
        $stmt->execute([
            ":id" => $id,
            ":user_id" => $userId
        ]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function update($id, $title, $priority, $userId)
    {
        $stmt = $this->pdo->prepare("UPDATE tasks SET title = :title, priority = :priority WHERE id = :id AND user_id = :user_id");
        return $stmt->execute([
            ":title" => $title,
            ":priority" => $priority,
            ":id" => $id,
            ":user_id" => $userId
        ]);
    }
}
