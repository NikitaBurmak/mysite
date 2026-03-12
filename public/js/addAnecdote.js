export function initAddAnecdote() {
    const addModal = document.getElementById('addAnecdoteModal');
    if (!addModal) return;

    const addForm = document.getElementById('addAnecdoteForm');
    const authModal = document.getElementById('authModal');
    const addBtn = document.getElementById('addAnecdoteButton');

    const addTopicModal = document.getElementById('addTopicModal');
    const addTopicForm = document.getElementById('addTopicForm');
    const addTopicBtn = document.getElementById('openAddTopicModal');

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

        const topicCheckboxes = addForm.querySelectorAll('input[name="topicIds[]"]:checked');
        const topicIds = Array.from(topicCheckboxes).map(cb => parseInt(cb.value));

        if (topicIds.length === 0) {
            alert('Пожалуйста, выберите хотя бы одну тему');
            return;
        }

        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = `
            <div style="text-align: center;">
                <div class="loading-spinner"></div>
                <div class="loading-text">Добавляю анекдот...</div>
            </div>
        `;
        document.body.appendChild(overlay);

        let previousLatestId = 0;
        try {
            const latestRes = await fetch('/api/anecdotes/latest', {credentials: 'same-origin'});
            const latestData = await latestRes.json();
            previousLatestId = latestData.latestId || 0;
        } catch (e) {
            console.warn('Could not get latest ID, using fallback');
        }

        try {
            const res = await fetch(addForm.dataset.url, {
                method: 'POST',
                credentials: 'same-origin',
                body: JSON.stringify({text, topicIds}),
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const data = await res.json();

            if (data.success) {
                addModal.style.display = 'none';
                addForm.reset();

                overlay.querySelector('.loading-text').textContent = 'Ожидание обработки...';

                const pollInterval = setInterval(async () => {
                    try {
                        const latestRes = await fetch('/api/anecdotes/latest', {credentials: 'same-origin'});
                        const latestData = await latestRes.json();
                        const currentLatestId = latestData.latestId || 0;

                        if (currentLatestId > previousLatestId) {
                            clearInterval(pollInterval);
                            overlay.remove();
                            const selectedTopics = window.getSelectedTopics ? window.getSelectedTopics() : [];
                            document.dispatchEvent(new CustomEvent('topicsChanged', {detail: selectedTopics}));
                        }
                    } catch (e) {
                        console.warn('Polling error:', e);
                    }
                }, 500);

                setTimeout(() => {
                    clearInterval(pollInterval);
                    overlay.remove();
                    const selectedTopics = window.getSelectedTopics ? window.getSelectedTopics() : [];
                    document.dispatchEvent(new CustomEvent('topicsChanged', {detail: selectedTopics}));
                }, 10000);
            } else {
                overlay.remove();
                alert(data.error || 'Ошибка при добавлении анекдота');
            }
        } catch (err) {
            console.error(err);
            overlay.remove();
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
                location.reload();
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
