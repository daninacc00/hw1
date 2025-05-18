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

    /**
     * Registra un nuovo utente
     */
    public function register($username, $email, $password, $nome, $cognome)
    {
        // Verifica se username o email esistono già
        if ($this->isUserExist($username, $email)) {
            return ['success' => false, 'message' => 'Username o email già esistenti'];
        }

        // Hash della password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $username = mysqli_real_escape_string($this->conn, $username);
        $email = mysqli_real_escape_string($this->conn, $email);
        $nome = mysqli_real_escape_string($this->conn, $nome);
        $cognome = mysqli_real_escape_string($this->conn, $cognome);

        // Inserimento nel database
        $sql = "INSERT INTO utenti (username, email, password_hash, nome, cognome) 
                VALUES ('$username', '$email', '$passwordHash', '$nome', '$cognome')";

        if (mysqli_query($this->conn, $sql)) {
            return ['success' => true, 'message' => 'Registrazione completata con successo'];
        } else {
            return ['success' => false, 'message' => 'Errore durante la registrazione: ' . mysqli_error($this->conn)];
        }
    }

    /**
     * Verifica login utente
     */
    public function login($username, $password)
    {
        $username = mysqli_real_escape_string($this->conn, $username);
        // Query sicura con due parametri distinti
        $sql = "SELECT * FROM utenti 
                WHERE (username = '$username' OR email = '$username') 
                AND stato_account = 0";

        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));

        if ($utente = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $utente['password_hash'])) {
                // Aggiorna ultimo accesso
                $this->updateLastLogin($utente['id_utente']);

                // Rimuovi la password dall'array di ritorno
                unset($utente['password_hash']);

                return ['success' => true, 'utente' => $utente];
            } else {
                return ['success' => false, 'message' => 'Credenziali non valide'];
            }
        } else {
            return ['success' => false, 'message' => 'Credenziali non valide'];
        }
    }


    /**
     * Verifica se utente esiste già
     */
    private function isUserExist($username, $email)
    {
        $username = mysqli_real_escape_string($this->conn, $username);
        $email = mysqli_real_escape_string($this->conn, $email);

        $sql = "SELECT COUNT(*) as count FROM utenti WHERE username = '$username' OR email = '$email'";
        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));
        $row = mysqli_fetch_assoc($result);

        return $row['count'] > 0;
    }

    /**
     * Aggiorna ultimo accesso
     */
    private function updateLastLogin($idUtente)
    {
        $idUtente = (int)$idUtente; // Cast a intero per sicurezza
        $sql = "UPDATE utenti SET ultimo_accesso = CURRENT_TIMESTAMP WHERE id_utente = $idUtente";
        mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));
    }

    /**
     * Ottieni utente per ID
     */
    public function getUtenteById($id)
    {
        $id = (int)$id; // Cast a intero per sicurezza
        $sql = "SELECT id_utente, username, email, nome, cognome, data_registrazione, ultimo_accesso, stato_account 
                FROM utenti WHERE id_utente = $id";

        $result = mysqli_query($this->conn, $sql) or die("Errore: " . mysqli_error($this->conn));
        return mysqli_fetch_assoc($result);
    }
}
