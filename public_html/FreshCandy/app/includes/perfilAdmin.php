<!-- Panel lateral emergente -->
<div id="admin-profile-panel" class="profile-panel">
    <div class="profile-content">
        <div class="profile-header">
            <h2 class="profile-title">Perfil de Administrador</h2>
            <button id="close-panel" class="profile-close" aria-label="Cerrar panel">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="profile-body">
            <div class="profile-avatar">
                <i class="fa-solid fa-user-shield"></i>
                <span class="profile-badge">ADMIN</span>
            </div>

            <!-- Sistema de pestañas -->
            <div class="profile-tabs">
                <button class="tab-button active" data-tab="basic-tab">
                    <i class="fa-solid fa-user"></i> Datos Básicos
                </button>
                <button class="tab-button" data-tab="security-tab">
                    <i class="fa-solid fa-lock"></i> Seguridad
                </button>
            </div>

            <!-- Contenido de las pestañas -->
            <div class="tab-content active" id="basic-tab">
                <form id="profile-form" class="profile-form">
                    <div class="form-group">
                        <label for="username">
                            <i class="fa-solid fa-user"></i> Nombre de usuario
                        </label>
                        <input type="text" id="username" class="form-control" value="ADMIN" required>
                        <span class="form-error" id="username-error"></span>
                    </div>

                    <div class="form-group">
                        <label for="email">
                            <i class="fa-solid fa-envelope"></i> Correo electrónico
                        </label>
                        <input type="email" id="email" class="form-control" value="admin@freshcandy.com" required>
                        <span class="form-error" id="email-error"></span>
                    </div>
                </form>
            </div>

            <div class="tab-content" id="security-tab">
                <form id="security-form" class="profile-form">
                    <div class="form-group">
                        <label for="current-password">
                            <i class="fa-solid fa-lock"></i> Contraseña actual
                        </label>
                        <div class="password-input">
                            <input type="password" id="current-password" class="form-control">
                            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <span class="form-error" id="current-password-error"></span>
                        <small class="help-text">Dejar en blanco para mantener la contraseña actual</small>
                    </div>
                    <div class="form-group">
                        <label for="password">
                            <i class="fa-solid fa-lock"></i> Nueva contraseña
                        </label>
                        <div class="password-input">
                            <input type="password" id="password" class="form-control">
                            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <span class="form-error" id="password-error"></span>
                        <small class="help-text">Dejar en blanco para mantener la contraseña actual</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm-password">
                            <i class="fa-solid fa-check-double"></i> Confirmar contraseña
                        </label>
                        <div class="password-input">
                            <input type="password" id="confirm-password" class="form-control">
                            <button type="button" class="toggle-password" aria-label="Mostrar contraseña">
                                <i class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        <span class="form-error" id="confirm-password-error"></span>
                    </div>
                </form>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary" id="save-btn">
                    <i class="fa-solid fa-save"></i> Guardar cambios
                </button>
                <button type="button" class="btn btn-secondary" id="cancel-btn">
                    <i class="fa-solid fa-xmark"></i> Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Overlay para cerrar el panel al hacer clic fuera -->
<div id="profile-overlay" class="profile-overlay"></div>

<!-- Alerta de éxito -->
<div id="success-alert" class="alert alert-success">
    <i class="fa-solid fa-check-circle"></i>
    <span>Cambios guardados correctamente</span>
</div>

<!-- Scripts -->
<script src="app/assets/js/adminV2js/perfilAdmin.js"></script>