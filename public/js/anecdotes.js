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
            }

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

                const idCell = document.createElement('td');
                idCell.className = 'px-6 py-4';
                idCell.textContent = a.id;

                const textCell = document.createElement('td');
                textCell.className = 'px-6 py-4';
                textCell.textContent = a.text;

                const actionsCell = document.createElement('td');
                actionsCell.className = 'px-6 py-4';
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'like-button px-3 py-1 rounded transition ' + (window.IS_LOGGED_IN ? 'bg-red-500 text-white hover:bg-red-600' : 'bg-gray-400 text-white cursor-not-allowed show-login');
                button.dataset.id = a.id;
                button.dataset.count = a.votesSum;
                button.textContent = '❤️ ' + a.votesSum;
                actionsCell.appendChild(button);

                tr.appendChild(idCell);
                tr.appendChild(textCell);
                tr.appendChild(actionsCell);
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
