export function initAddAnecdote() {
    const addModal = document.getElementById('addAnecdoteModal');
    const addForm = document.getElementById('addAnecdoteForm');
    const authModal = document.getElementById('authModal');

    addForm?.addEventListener('submit', async e => {
        e.preventDefault();

        if (!window.IS_LOGGED_IN) {
            addModal.style.display = 'none';
            authModal.style.display = 'block';
            return;
        }

        const formData = new FormData(addForm);

        try {
            const res = await fetch(addForm.dataset.url, {
                method: 'POST',
                credentials: 'same-origin',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await res.json();

            if (data.success) {
                addModal.style.display = 'none';
                addForm.reset();
                document.dispatchEvent(new CustomEvent('topicChanged', { detail: window.currentTopicId || null }));
            } else if (data.requireLogin) {
                addModal.style.display = 'none';
                authModal.style.display = 'block';
            } else {
                alert(data.error || 'Ошибка при добавлении анекдота');
            }

        } catch (err) {
            console.error(err);
        }
    });

    const addBtn = document.getElementById('addAnecdoteButton') || document.querySelector('.add-btn.show-login');
    addBtn?.addEventListener('click', e => {
        e.preventDefault();
        if (!window.IS_LOGGED_IN) authModal.style.display = 'block';
        else addModal.style.display = 'block';
    });
}
