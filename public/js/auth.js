export let showUserUI;
export let hideUserUI;

export async function initAuth() {
    const authModal = document.getElementById('authModal');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const loginButton = document.getElementById('loginButton');
    const logoutButton = document.getElementById('logoutButton');
    const userAvatar = document.getElementById('userAvatar');

    const ajaxPostForm = async (form) => {
        const res = await fetch(form.dataset.url, {
            method: 'POST',
            body: new FormData(form),
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            credentials: 'same-origin'
        });
        return await res.json();
    };

    showUserUI = () => {
        window.IS_LOGGED_IN = true;
        if (userAvatar) userAvatar.style.display = 'flex';
        if (loginButton) loginButton.style.display = 'none';
        if (logoutButton) logoutButton.style.display = 'inline-block';
        document.dispatchEvent(new CustomEvent('userLoggedIn'));
    };

    hideUserUI = () => {
        window.IS_LOGGED_IN = false;
        if (userAvatar) userAvatar.style.display = 'none';
        if (loginButton) loginButton.style.display = 'inline-block';
        if (logoutButton) logoutButton.style.display = 'none';
        document.dispatchEvent(new CustomEvent('userLoggedOut'));
    };

    loginButton?.addEventListener('click', () => authModal.style.display = 'block');
    authModal.querySelectorAll('.close').forEach(btn => btn.addEventListener('click', () => authModal.style.display = 'none'));

    loginForm?.addEventListener('submit', async e => {
        e.preventDefault();
        const data = await ajaxPostForm(loginForm);
        if (data.success) {
            authModal.style.display = 'none';
            showUserUI();
            window.CURRENT_USER = data.user;
        } else {
            alert(data.error || 'Ошибка входа');
        }
    });

    registerForm?.addEventListener('submit', async e => {
        e.preventDefault();
        const data = await ajaxPostForm(registerForm);
        if (data.success) {
            authModal.style.display = 'none';
            showUserUI();
            window.CURRENT_USER = data.user;
        } else {
            alert(data.error || 'Ошибка регистрации');
        }
    });

    logoutButton?.addEventListener('click', async e => {
        e.preventDefault();
        await fetch('/logout', {method: 'POST', credentials: 'same-origin'});
        hideUserUI();
        window.CURRENT_USER = null;
    });

    try {
        const res = await fetch('/api/current_user', {credentials: 'same-origin'});
        const user = await res.json();
        if (user) {
            window.IS_LOGGED_IN = true;
            window.CURRENT_USER = user;
            showUserUI();
        } else {
            hideUserUI();
        }
    } catch (err) {
        console.error('Ошибка при получении текущего пользователя', err);
        hideUserUI();
    }

    document.addEventListener('DOMContentLoaded', () => {
        const authModal = document.getElementById('authModal');
        const loginContainer = document.getElementById('login-form');
        const registerContainer = document.getElementById('register-form');
        const tabLogin = document.getElementById('tab-login');
        const tabRegister = document.getElementById('tab-register');

        const clearAuthForms = () => {
            loginForm?.reset();
            registerForm?.reset();
        };

        loginContainer.style.display = 'block';
        registerContainer.style.display = 'none';
        tabLogin.classList.add('active');
        tabRegister.classList.remove('active');

        tabLogin.addEventListener('click', () => {
            loginContainer.style.display = 'block';
            registerContainer.style.display = 'none';
            tabLogin.classList.add('active');
            tabRegister.classList.remove('active');
        });

        tabRegister.addEventListener('click', () => {
            loginContainer.style.display = 'none';
            registerContainer.style.display = 'block';
            tabRegister.classList.add('active');
            tabLogin.classList.remove('active');
        });

        authModal.querySelectorAll('.close').forEach(btn => {
            btn.addEventListener('click', () => authModal.style.display = 'none');
        });

        document.querySelectorAll('.show-login').forEach(btn => {
            btn.addEventListener('click', () => authModal.style.display = 'block');
        });
    });
    document.querySelectorAll('.password-wrapper').forEach(wrapper => {
        const btn = wrapper.querySelector('.show-password-btn');
        const input = wrapper.querySelector('input[type="password"], input[type="text"]');

        btn.addEventListener('click', () => {
            if (input.type === 'password') {
                input.type = 'text';
            } else {
                input.type = 'password';
            }
        });
    });
    document.querySelectorAll('.social-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const provider = btn.classList.contains('google-btn') ? 'Google' : 'Facebook';
            alert(`Login with ${provider} clicked! (подключить OAuth здесь)`);
        });
    });
}
