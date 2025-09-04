




export async function checkAuthStatus(roleID) {
    roleID = parseInt(roleID);

    if (isNaN(roleID)) {
        console.error("El rol no es un número válido");
        return false;
    }

    if (roleID !== 1 && roleID !== 2) {
        console.error("El rol no es válido");
        return false;
    }

    try {
        const response = await fetch(`/../../controllers/AuthController.php?action=checkAuth&role=${roleID}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (!data.success) {
            
            return false;
        }

        
        return true;

    } catch (error) {
        console.error("Error al autenticar:", error);
        return false;
    }
}



export function redirectTo(url) {
    // Validar que la URL sea segura y relativa
    const safeUrl = url.startsWith('/') ? url : `/${url}`;
    
    // Redirigir después de 500ms para permitir que llegue la respuesta
    setTimeout(() => {
        window.location.href = safeUrl;
    }, 100);
}

export function logout(){


    fetch('/../../controllers/AuthController.php?action=logout',{
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){

            
            redirectTo(data.data.redirect);
        }else{
            console.error('Error al cerrar sesión:', data.message);
        }
    })
    .catch(error => {
        console.error('Error al cerrar sesión:', error);
    });



}
 