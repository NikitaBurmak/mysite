export function initAuth() {
    const authModal = document.getElementById('authModal');
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');

    const ajaxPostForm = async (formEl) => {
        const url = formEl.dataset.url;
        const formData = new FormData(formEl);
        const res = await fetch(url, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin'
        });
        return await res.json();
    };

    loginForm?.addEventListener('submit', async e => {
        e.preventDefault();
        try {
            const data = await ajaxPostForm(loginForm);
            if (data.success) {
                window.IS_LOGGED_IN = true;
                window.CURRENT_USER = data.user;
                authModal.style.display = 'none';
                alert('Logged in!');
                // перезагружаем анекдоты, чтобы активировать лайки
                document.dispatchEvent(new CustomEvent('topicChanged', { detail: window.currentTopicId || null }));
            } else alert(data.error || 'Ошибка входа');
        } catch (err) { console.error(err); }
    });

    registerForm?.addEventListener('submit', async e => {
        e.preventDefault();
        try {
            const data = await ajaxPostForm(registerForm);
            if (data.success) {
                window.IS_LOGGED_IN = true;
                window.CURRENT_USER = data.user;
                authModal.style.display = 'none';
                alert('Registered!');
                document.dispatchEvent(new CustomEvent('topicChanged', { detail: window.currentTopicId || null }));
            } else alert(data.error || 'Ошибка регистрации');
        } catch (err) { console.error(err); }
    });
}
