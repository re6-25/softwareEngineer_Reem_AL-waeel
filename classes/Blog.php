<?php
require_once 'Database.php';

class Blog
{
    public $db;
    public function __construct()
    {
        $this->db = (new Database())->conn;
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT blogs.*, users.name AS user_name FROM blogs LEFT JOIN users ON users.id = blogs.user_id WHERE blogs.id=? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function byUser($user_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM blogs WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count()
    {
        $q = $this->db->query("SELECT COUNT(*) as c FROM blogs");
        return $q->fetch(PDO::FETCH_ASSOC)['c'];
    }

    public function create($title, $content, $user_id)
    {
        $stmt = $this->db->prepare("INSERT INTO blogs (title, content, user_id, created_at) VALUES (?, ?, ?, NOW())");
        return $stmt->execute([$title, $content, $user_id]);
    }

    public function all()
    {
        $q = $this->db->query("SELECT * FROM blogs ORDER BY id DESC");
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function get($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM blogs WHERE id=?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function add($user_id, $title, $content)
    {
        $stmt = $this->db->prepare("INSERT INTO blogs (user_id, title, content, status, created_at) 
                                VALUES (?, ?, ?, 'pending', NOW())");
        return $stmt->execute([$user_id, $title, $content]);
    }


    public function update($id, $title, $content)
    {
        $stmt = $this->db->prepare("UPDATE blogs SET title=?, content=? WHERE id=?");
        return $stmt->execute([$title, $content, $id]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM blogs WHERE id=?");
        return $stmt->execute([$id]);
    }
    public function getApproved()
    {
        $stmt = $this->db->prepare("SELECT blogs.*, users.name as user_name 
                                FROM blogs 
                                JOIN users ON blogs.user_id = users.id 
                                WHERE blogs.status = 'approved' 
                                ORDER BY blogs.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
