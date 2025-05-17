<?php
require_once __DIR__ . '/../includes/database.php';

class User
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    /**
     * Registra un nuovo utente
     */
    public function registraUtente($username, $email, $password, $nome, $cognome)
    {
        try {
            // Verifica se username o email esistono già
            if ($this->utenteEsiste($username, $email)) {
                return ['success' => false, 'message' => 'Username o email già esistenti'];
            }

            // Hash della password
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Inserimento nel database
            $sql = "INSERT INTO utenti (username, email, password_hash, nome, cognome) 
                    VALUES (:username, :email, :password_hash, :nome, :cognome)";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password_hash' => $passwordHash,
                ':nome' => $nome,
                ':cognome' => $cognome
            ]);

            return ['success' => true, 'message' => 'Registrazione completata con successo'];
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Errore durante la registrazione: ' . $e->getMessage()];
        }
    }

    /**
     * Verifica login utente
     */
    public function loginUtente($username, $password)
    {
        try {
            // Query sicura con due parametri distinti
            $sql = "SELECT * FROM utenti 
                WHERE (username = :username OR email = :email) 
                AND stato_account = 0";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':username' => $username,
                ':email' => $username
            ]);

            $utente = $stmt->fetch();

            if ($utente && password_verify($password, $utente['password_hash'])) {
                // Aggiorna ultimo accesso
                $this->aggiornaUltimoAccesso($utente['id_utente']);

                // Rimuovi la password dall'array di ritorno
                unset($utente['password_hash']);

                return ['success' => true, 'utente' => $utente];
            } else {
                return ['success' => false, 'message' => 'Credenziali non valide'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Errore durante il login: ' . $e->getMessage()];
        }
    }


    /**
     * Verifica se utente esiste già
     */
    private function utenteEsiste($username, $email)
    {
        $sql = "SELECT COUNT(*) FROM utenti WHERE username = :username OR email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username, ':email' => $email]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Aggiorna ultimo accesso
     */
    private function aggiornaUltimoAccesso($idUtente)
    {
        $sql = "UPDATE utenti SET ultimo_accesso = CURRENT_TIMESTAMP WHERE id_utente = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $idUtente]);
    }

    /**
     * Ottieni utente per ID
     */
    public function getUtenteById($id)
    {
        $sql = "SELECT id_utente, username, email, nome, cognome, data_registrazione, ultimo_accesso, stato_account 
                FROM utenti WHERE id_utente = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
}
