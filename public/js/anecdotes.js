export async function initAnecdotes() {
    const tableBody = document.querySelector('table tbody');
    if (!tableBody) return;

    const authModal = document.getElementById('authModal');

    let selectedTopicIds = [];

    async function loadAnecdotes(topicIds = []) {
        try {
            let url = '/api/anecdotes';

            if (topicIds.length > 0) {
                const params = topicIds.map(id => `topics[]=${id}`).join('&');
                url = `/api/anecdotes?${params}`;
                console.log('Loading anecdotes from:', url);
            } else {
                console.log('Loading all anecdotes');
            }

            const res = await fetch(url, {credentials: 'same-origin'});

            const text = await res.text();
            console.log('Response:', text);

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

    document.addEventListener('topicsChanged', e => {
        console.log('topicsChanged event received:', e.detail);
        selectedTopicIds = e.detail || [];
        loadAnecdotes(selectedTopicIds);
    });

    loadAnecdotes();
}
