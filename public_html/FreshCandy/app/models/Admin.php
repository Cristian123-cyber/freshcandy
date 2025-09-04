<?php

class Admin
{
    private $mysql;

    public function __construct($mysql)
    {
        $this->mysql = $mysql;
    }

    public function changePassword($data)
    {
        $currentPassword = $data['current_password'];
        $newPassword = $data['new_password'];
        $confirmPassword = $data['confirm_password'];

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return false;
        }

        if ($newPassword !== $confirmPassword) {
            return false;
        }

        $query = "SELECT password FROM administradores WHERE id_administrador = ? LIMIT 1";

        $adminId = $this->obtenerIdAdmin();

        if (!$adminId) {
            return false;
        }

        $result = $this->mysql->prepararConsultaSelect($query, 'i', [$adminId]);

        if (!$result || empty($result)) {
            return false;
        }

        $row = $result[0];
        $hashedPassword = $row['password'];

        $isVerified = $this->verificarPassword($currentPassword, $hashedPassword);

        if (!$isVerified) {
            return false;
        }

        $query = "UPDATE administradores SET password = ? WHERE id_administrador = ?";

        $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);

        $result = $this->mysql->prepararConsulta($query, 'si', [$hashedNewPassword, $adminId]);

        if (!$result) {
            return false;
        }

        return true;
    }

    private function verificarPassword($password, $hashedPassword)
    {
        return password_verify($password, $hashedPassword);
    }

    private function obtenerIdAdmin()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $adminId = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

        if (!$adminId) {
            return false;
        }

        return $adminId;
    }

    public function updateProfile($data)
    {
        $username = $data['username'];
        $email = $data['email'];

        if (empty($username) || empty($email)) {
            return false;
        }

        // Verificar si el email ya existe para otro administrador
        $query = "SELECT id_administrador FROM administradores WHERE correo_admin = ? AND id_administrador != ?";
        $adminId = $this->obtenerIdAdmin();

        if (!$adminId) {
            return false;
        }

        $result = $this->mysql->prepararConsultaSelect($query, 'si', [$email, $adminId]);

        if ($result && !empty($result)) {
            return false; // Email ya existe
        }

        $query = "UPDATE administradores SET nombre_usuario = ?, correo_admin = ? WHERE id_administrador = ?";

        $result = $this->mysql->prepararConsulta($query, 'ssi', [$username, $email, $adminId]);

        if (!$result) {
            return false;
        }

        return true;
    }

    public function getAdminById($id)
    {
        $query = "SELECT id_administrador AS id, nombre_usuario AS name, 
                         correo_admin AS email, Roles_id_rol AS role 
                  FROM administradores 
                  WHERE id_administrador = ?";

        $resultado = $this->mysql->prepararConsultaSelect($query, 'i', [$id]);

        return ($resultado && !empty($resultado)) ? $resultado[0] : null;
    }
}
