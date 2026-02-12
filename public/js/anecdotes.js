export async function initAnecdotes() {
    const tableBody = document.querySelector('table tbody');
    const authModal = document.getElementById('authModal');
    const addModal = document.getElementById('addAnecdoteModal');
    const addForm = document.getElementById('addAnecdoteForm');

    async function loadAnecdotes(topicId = null) {
        try {
            const url = topicId ? `/api/anecdotes?topic=${topicId}` : '/api/anecdotes';
            const res = await fetch(url, { credentials: 'same-origin' });
            const anecdotes = await res.json();

            tableBody.innerHTML = '';

            if (!anecdotes.length) {
                tableBody.innerHTML = `
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-gray-500">No jokes yet 😅</td>
                    </tr>
                `;
                return;
            }

            anecdotes.forEach(a => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="px-6 py-4">${a.id}</td>
                    <td class="px-6 py-4">${a.text}</td>
                    <td class="px-6 py-4">
                        <button type="button"
                                class="like-button px-3 py-1 rounded transition ${window.IS_LOGGED_IN ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-gray-400 text-white cursor-not-allowed show-login'}"
                                data-id="${a.id}">
                            ❤️ ${a.votesSum}
                        </button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });

            setupLikeButtons();
        } catch (err) {
            console.error(err);
        }
    }

    function setupLikeButtons() {
        document.querySelectorAll('.like-button').forEach(btn => {
            btn.addEventListener('click', async e => {
                e.preventDefault();
                if (!window.IS_LOGGED_IN) {
                    authModal.style.display = 'block';
                    return;
                }

                const anecdoteId = btn.dataset.id;
                if (!anecdoteId) return;

                try {
                    const token = document.querySelector('meta[name="csrf-token"]').content;

                    const res = await fetch(`/anecdote/${anecdoteId}/like`, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': token   // вот этот заголовок!
                        }
                    });
                    const data = await res.json();
                    if (data.requireLogin) authModal.style.display = 'block';
                    else if (data.votes !== undefined) btn.textContent = `❤️ ${data.votes}`;
                } catch (err) {
                    console.error(err);
                }
            });
        });
    }

    function setupAddButton() {
        const addBtn = document.getElementById('addAnecdoteButton') || document.querySelector('.add-btn.show-login');
        if (!addBtn) return;

        addBtn.addEventListener('click', e => {
            e.preventDefault();
            if (!window.IS_LOGGED_IN) authModal.style.display = 'block';
            else addModal.style.display = 'block';
        });
    }

    addForm?.addEventListener('submit', async e => {
        e.preventDefault();
        const formData = new FormData(addForm);
        try {
            const res = await fetch(addForm.dataset.url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.success) {
                addModal.style.display = 'none';
                addForm.reset();
                loadAnecdotes(window.currentTopicId || null);
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

    loadAnecdotes();

    document.addEventListener('topicChanged', e => {
        loadAnecdotes(e.detail);
    });

    setupAddButton();

    document
        .getElementById('addAnecdoteForm')
        ?.addEventListener('submit', async (e) => {
            e.preventDefault()

            const text = e.target.text.value

            await fetch('/api/anecdotes', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ text })
            })
        })
}
