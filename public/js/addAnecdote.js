export function initAddAnecdote() {
    const addModal = document.getElementById('addAnecdoteModal');
    const addForm = document.getElementById('addAnecdoteForm');
    const authModal = document.getElementById('authModal');
    const addBtn = document.getElementById('addAnecdoteButton');

    const addTopicModal = document.getElementById('addTopicModal');
    const addTopicForm = document.getElementById('addTopicForm');
    const addTopicBtn = document.getElementById('addTopicBtn');
    const topicsContainer = document.getElementById('topicsContainer');

    addModal.querySelector('.close')?.addEventListener('click', () => addModal.style.display = 'none');
    authModal.querySelector('.close')?.addEventListener('click', () => authModal.style.display = 'none');
    addTopicModal?.querySelector('.close')?.addEventListener('click', () => addTopicModal.style.display = 'none');

    addBtn?.addEventListener('click', e => {
        e.preventDefault();
        if (!window.IS_LOGGED_IN || !window.CURRENT_USER) {
            authModal.style.display = 'flex';
        } else {
            addModal.style.display = 'flex';
        }
    });

    addTopicBtn?.addEventListener('click', e => {
        e.preventDefault();
        if (!window.IS_LOGGED_IN || !window.CURRENT_USER) {
            authModal.style.display = 'flex';
        } else {
            addTopicModal.style.display = 'flex';
        }
    });

    addForm?.addEventListener('submit', async e => {
        e.preventDefault();

        const text = addForm.querySelector('textarea[name="text"]').value;
        const topicId = addForm.querySelector('select[name="topic_id"]')?.value || null;
        const currentTopicId = window.currentTopicId ?? null;

        try {
            const res = await fetch(addForm.dataset.url, {
                method: 'POST',
                credentials: 'same-origin',
                body: JSON.stringify({text, topic_id: topicId, currentTopicId}),
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await res.json();

            if (data.success) {
                addModal.style.display = 'none';
                addForm.reset();
                document.dispatchEvent(new CustomEvent('topicChanged', {detail: currentTopicId}));
            } else {
                alert(data.error || 'Ошибка при добавлении анекдота');
            }
        } catch (err) {
            console.error(err);
        }
    });

    addTopicForm?.addEventListener('submit', async e => {
        e.preventDefault();
        if (!window.IS_LOGGED_IN) {
            addTopicModal.style.display = 'block';
            return;
        }

        const formData = new FormData(addTopicForm);
        const name = formData.get('name');

        try {
            const res = await fetch(addTopicForm.dataset.url, {
                method: 'POST',
                credentials: 'same-origin',
                body: JSON.stringify({name}),
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();

            if (data.requireLogin) {
                authModal.style.display = 'flex';
                return;
            }

            if (data.success) {
                addTopicModal.style.display = 'none';
                addTopicForm.reset();

                if (topicsContainer) {
                    const newTopicBtn = document.createElement('button');
                    newTopicBtn.classList.add('topic-link', 'px-4', 'py-2', 'bg-blue-500', 'text-white', 'rounded', 'hover:bg-blue-600');
                    newTopicBtn.dataset.topicId = data.id;
                    newTopicBtn.textContent = data.name;

                    newTopicBtn.addEventListener('click', () => {
                        window.currentTopicId = data.id;
                        document.dispatchEvent(new CustomEvent('topicChanged', {detail: data.id}));
                    });

                    topicsContainer.appendChild(newTopicBtn);
                }

                const topicSelect = addForm.querySelector('select[name="topic_id"]');
                if (topicSelect) {
                    const newOption = document.createElement('option');
                    newOption.value = data.id;
                    newOption.textContent = data.name;
                    topicSelect.appendChild(newOption);
                }
            } else {
                alert(data.error || 'Ошибка при добавлении темы');
            }
        } catch (err) {
            console.error(err);
        }
    });

    addModal.style.display = 'none';
    authModal.style.display = 'none';
    addTopicModal.style.display = 'none';
}
