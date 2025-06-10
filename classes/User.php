<?php
require_once __DIR__ . '/../includes/database.php';

class User
{
    private $conn;

    public function __construct()
    {
        global $conn;
        $this->conn = $conn;
    }

    public function register($username, $email, $password, $nome, $cognome)
    {
        if ($this->isUserExist($username, $email)) {
            return ['success' => false, 'message' => 'Username o email già esistenti'];
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $username = mysqli_real_escape_string($this->conn, $username);
        $email = mysqli_real_escape_string($this->conn, $email);
        $nome = mysqli_real_escape_string($this->conn, $nome);
        $cognome = mysqli_real_escape_string($this->conn, $cognome);

        $sql = "INSERT INTO users (username, email, password_hash, first_name, last_name) 
                VALUES ('$username', '$email', '$passwordHash', '$nome', '$cognome')";

        if (mysqli_query($this->conn, $sql)) {
            return ['success' => true, 'message' => 'Registrazione completata con successo'];
        } else {
            return ['success' => false, 'message' => 'Errore durante la registrazione: ' . mysqli_error($this->conn)];
        }
    }

    public function login($username, $password)
    {
        $username = mysqli_real_escape_string($this->conn, $username);
        $sql = "SELECT * FROM users 
                WHERE (username = '$username' OR email = '$username') 
                AND account_status = 0";

        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));

        if ($utente = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $utente['password_hash'])) {
                $this->updateLastLogin($utente['id']);

                unset($utente['password_hash']);

                return ['success' => true, 'utente' => $utente];
            } else {
                return ['success' => false, 'message' => 'Credenziali non valide'];
            }
        } else {
            return ['success' => false, 'message' => 'Credenziali non valide'];
        }
    }

    private function isUserExist($username, $email)
    {
        $username = mysqli_real_escape_string($this->conn, $username);
        $email = mysqli_real_escape_string($this->conn, $email);

        $sql = "SELECT COUNT(*) as count FROM users WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));
        $row = mysqli_fetch_assoc($result);

        return $row['count'] > 0;
    }

    private function updateLastLogin($idUtente)
    {
        $idUtente = (int)$idUtente;
        $sql = "UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = $idUtente";
        mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));
    }

    public function getUserById($id)
    {
        $id = (int)$id; 
        $sql = "SELECT * 
                FROM users WHERE id = $id";

        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));
        return mysqli_fetch_assoc($result);
    }
}
