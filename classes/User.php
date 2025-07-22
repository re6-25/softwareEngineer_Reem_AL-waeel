<?php
require_once 'Database.php';

class User
{
    public $db;
    public function __construct()
    {
        $this->db = (new Database())->conn;
    }

    public function all()
    {
        $q = $this->db->query("SELECT * FROM users ORDER BY id DESC");
        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM users WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function changeRole($id, $role)
    {
        $stmt = $this->db->prepare("UPDATE users SET role=? WHERE id=?");
        return $stmt->execute([$role, $id]);
    }

    public function updateName($id, $name)
    {
        $stmt = $this->db->prepare("UPDATE users SET name=? WHERE id=?");
        return $stmt->execute([$name, $id]);
    }

    public function updatePassword($id, $password)
    {
        $stmt = $this->db->prepare("UPDATE users SET password=? WHERE id=?");
        return $stmt->execute([$password, $id]);
    }

    public function updateProfile($id, $name, $email, $avatar, $password = null)
    {
        $query = "UPDATE users SET name=?, email=?, avatar=?";
        $params = [$name, $email, $avatar];

        if ($password) {
            $query .= ", password=?";
            $params[] = $password;
        }

        $query .= " WHERE id=?";
        $params[] = $id;

        $stmt = $this->db->prepare($query);
        return $stmt->execute($params);
    }


    public function count()
    {
        $q = $this->db->query("SELECT COUNT(*) as c FROM users");
        return $q->fetch(PDO::FETCH_ASSOC)['c'];
    }

    public function register($name, $email, $password, $avatar_path = null, $birthday = null)
    {
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password, avatar, birthday) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $password, $avatar_path, $birthday]);
    }

    public function updateAvatar($user_id, $avatar_path)
    {
        $stmt = $this->db->prepare("UPDATE users SET avatar=? WHERE id=?");
        return $stmt->execute([$avatar_path, $user_id]);
    }

    public function deductPoints($user_id, $amount)
    {
        $stmt = $this->db->prepare("UPDATE users SET points = points - ? WHERE id=? AND points >= ?");
        return $stmt->execute([$amount, $user_id, $amount]);
    }

    public function emailExists($email)
    {
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email=?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    public function getPoints($user_id)
    {
        $stmt = $this->db->prepare("SELECT points FROM users WHERE id=?");
        $stmt->execute([$user_id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        return $res ? $res['points'] : 0;
    }

    public function login($email, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email=? AND password=? LIMIT 1");
        $stmt->execute([$email, $password]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function get($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateBio($user_id, $bio)
    {
        $stmt = $this->db->prepare("UPDATE users SET bio=? WHERE id=?");
        return $stmt->execute([$bio, $user_id]);
    }
    public function updateBasicInfo($id, $name, $email)
    {
        $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        return $stmt->execute([$name, $email, $id]);
    }

  
}
