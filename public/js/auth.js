//логика модалки авторизации / регистрации / logout
export function initAuth() {
    const authModal = document.getElementById('authModal');
    const loginButton = document.getElementById('loginButton');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    const openModal = modal => modal && (modal.style.display = 'block');
    const closeModal = modal => modal && (modal.style.display = 'none');

    const clearAuthForms = () => {
        loginForm?.reset();
        registerForm?.reset();
    };

    loginButton?.addEventListener('click', () => openModal(authModal));
    document.querySelectorAll('.show-login').forEach(btn =>
        btn.addEventListener('click', () => openModal(authModal))
    );

    document.querySelectorAll('.close').forEach(btn =>
        btn.addEventListener('click', () => {
            const modal = btn.closest('.modal');
            closeModal(modal);
            if (modal.id === 'authModal') clearAuthForms();
        })
    );

    document.getElementById('logoutButton')?.addEventListener('click', async e => {
        e.preventDefault();
        try {
            await fetch('/logout', {method: 'POST', credentials: 'same-origin'});
            window.IS_LOGGED_IN = false;
            window.CURRENT_USER = null;
        } catch (err) {
            console.error(err);
        }
    });

    const ajaxPostForm = async formEl => {
        const url = formEl.dataset.url;
        const formData = new FormData(formEl);
        const res = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            credentials: 'same-origin'
        });
        const text = await res.text();
        return JSON.parse(text);
    };

    loginForm?.addEventListener('submit', async e => {
        e.preventDefault();
        try {
            const data = await ajaxPostForm(loginForm);
            if (data.success) {
                window.IS_LOGGED_IN = true;
                window.CURRENT_USER = data.user;
                clearAuthForms();
                authModal.style.display = 'none';
            } else alert(data.error || 'Ошибка входа');
        } catch (err) {
            console.error(err);
        }
    });

    registerForm?.addEventListener('submit', async e => {
        e.preventDefault();
        try {
            const data = await ajaxPostForm(registerForm);
            if (data.success) {
                window.IS_LOGGED_IN = true;
                window.CURRENT_USER = data.user;
                clearAuthForms();
                authModal.style.display = 'none';
            } else alert(data.error || 'Ошибка регистрации');
        } catch (err) {
            console.error(err);
        }
    });
}
