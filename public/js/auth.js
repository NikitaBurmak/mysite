export let showUserUI;
export let hideUserUI;

export async function initAuth() {
    const elements = {
        authModal: document.getElementById('authModal'),
        loginForm: document.getElementById('loginForm'),
        registerForm: document.getElementById('registerForm'),
        loginButton: document.getElementById('loginButton'),
        logoutButton: document.getElementById('logoutButton'),
        userAvatar: document.getElementById('userAvatar'),
        tabLogin: document.getElementById('tab-login'),
        tabRegister: document.getElementById('tab-register'),
        loginContainer: document.getElementById('login-form'),
        registerContainer: document.getElementById('register-form'),
    };
    if (!elements.authModal) return;

    showUserUI = () => {
        window.IS_LOGGED_IN = true;
        if (elements.userAvatar) elements.userAvatar.style.display = 'flex';
        if (elements.loginButton) elements.loginButton.style.display = 'none';
        if (elements.logoutButton) elements.logoutButton.style.display = 'inline-block';
        document.dispatchEvent(new CustomEvent('userLoggedIn'));
    };

    hideUserUI = () => {
        window.IS_LOGGED_IN = false;
        if (elements.userAvatar) elements.userAvatar.style.display = 'none';
        if (elements.loginButton) elements.loginButton.style.display = 'inline-block';
        if (elements.logoutButton) elements.logoutButton.style.display = 'none';
        document.dispatchEvent(new CustomEvent('userLoggedOut'));
    };

    const openModal = () => elements.authModal.style.display = 'block';
    const closeModal = () => elements.authModal.style.display = 'none';

    elements.loginButton?.addEventListener('click', openModal);
    elements.authModal.querySelectorAll('.close').forEach(btn => btn.addEventListener('click', closeModal));

    function showLoginTab() {
        elements.loginContainer.style.display = 'block';
        elements.registerContainer.style.display = 'none';
        elements.tabLogin.classList.add('active');
        elements.tabRegister.classList.remove('active');
    }

    function showRegisterTab() {
        elements.loginContainer.style.display = 'none';
        elements.registerContainer.style.display = 'block';
        elements.tabRegister.classList.add('active');
        elements.tabLogin.classList.remove('active');
    }

    elements.tabLogin?.addEventListener('click', showLoginTab);
    elements.tabRegister?.addEventListener('click', showRegisterTab);

    elements.loginForm?.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = elements.loginForm.querySelector('input[name="email"]').value;
        const password = elements.loginForm.querySelector('input[name="password"]').value;

        try {
            const res = await fetch('/api/login', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ email, password })
            });
            const data = await res.json();

            if (res.ok && data.success && data.user) {
                window.CURRENT_USER = data.user.id;
                window.IS_LOGGED_IN = true;
                closeModal();
                showUserUI();
                window.location.href = data.redirect;
            } else {
                alert(data.error || 'Неверный логин');
            }
        } catch (err) {
            console.error('Ошибка при логине', err);
            alert('Ошибка сети');
        }
    });

    elements.registerForm?.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = elements.registerForm.querySelector('input[name="email"]').value;
        const password = elements.registerForm.querySelector('input[name="password"]').value;

        try {
            const res = await fetch('/register', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({email, password})
            });

            const data = await res.json();

            if (res.ok && data.success) {
                window.IS_LOGGED_IN = true;
                window.CURRENT_USER = data.user?.id ?? null;
                closeModal();
                showUserUI();
            } else {
                alert(data.error || 'Ошибка регистрации');
            }
        } catch (err) {
            console.error('Ошибка при регистрации', err);
            alert('Ошибка сети');
        }
    });
    elements.logoutButton?.addEventListener('click', async (e) => {
        e.preventDefault();
        try {
            await fetch('/logout', {method: 'POST', credentials: 'same-origin'});
        } finally {
            hideUserUI();
            window.CURRENT_USER = null;
        }
    });

    try {
        const res = await fetch('/api/current_user', {credentials: 'same-origin'});
        if (res.ok) {
            const user = await res.json();
            window.IS_LOGGED_IN = true;
            window.CURRENT_USER = user.id;
            showUserUI();
        } else {
            hideUserUI();
        }
    } catch {
        hideUserUI();
    }

    document.querySelectorAll('.password-wrapper').forEach(wrapper => {
        const btn = wrapper.querySelector('.show-password-btn');
        const input = wrapper.querySelector('input');
        if (!btn || !input) return;
        btn.addEventListener('click', () => input.type = input.type === 'password' ? 'text' : 'password');
    });

    showLoginTab();
}
