export async function initAnecdotes() {
    const tableBody = document.querySelector('table tbody');
    const authModal = document.getElementById('authModal');
    const addModal = document.getElementById('addAnecdoteModal');
    const addForm = document.getElementById('addAnecdoteForm');

    let currentTopicId = null;

    async function loadAnecdotes(topicId = null) {
        try {
            const url = topicId ? `/api/anecdotes?topic=${topicId}` : '/api/anecdotes';
            const res = await fetch(url, {credentials: 'same-origin'});

            const text = await res.text();

            let anecdotes;
            try {
                anecdotes = JSON.parse(text);
            } catch {
                anecdotes = [];
                console.error('Не удалось распарсить JSON');
            }

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
                                data-id="${a.id}"
                                data-count="${a.votesSum}">
                            ❤️ ${a.votesSum}
                        </button>
                    </td>
                `;
                tableBody.appendChild(tr);
            });

            setupLikeButtons();

        } catch (err) {
            console.error('Ошибка при загрузке анекдотов:', err);
        }
    }

    function setupLikeButtons() {
        document.querySelectorAll('.like-button').forEach(btn => {
            btn.onclick = async (e) => {
                e.preventDefault();
                if (!window.IS_LOGGED_IN) {
                    authModal.style.display = 'block';
                    return;
                }

                const anecdoteId = btn.dataset.id;

                try {
                    const res = await fetch(`/anecdote/${anecdoteId}/like`, {
                        method: 'POST',
                        credentials: 'same-origin'
                    });
                    if (!res.ok) {
                        if (res.status === 401) {
                            authModal.style.display = 'block';
                            return;
                        }
                        const errData = await res.json();
                        alert(errData.error || 'Ошибка лайка');
                        return;
                    }

                    const data = await res.json();
                    btn.textContent = `❤️ ${data.votes}`;
                } catch (err) {
                    console.error('Ошибка при лайке:', err);
                    alert('Ошибка сети при лайке');
                }
            };
        });
    }

    document.addEventListener('topicChanged', e => {
        currentTopicId = e.detail;
        loadAnecdotes(currentTopicId);
    });

    loadAnecdotes();
}
