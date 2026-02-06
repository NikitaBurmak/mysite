export function initAnecdotes() {
    const tableBody = document.querySelector('table tbody');

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
                    <td class="px-6 py-4 space-x-2">
                        ${window.IS_LOGGED_IN ? `
                            <button type="button"
                                    class="like-button px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 transition"
                                    data-id="${a.id}"
                                    data-count="${a.votesSum}">
                                ❤️ ${a.votesSum}
                            </button>`
                    : `
                            <button class="show-login like-button px-3 py-1 bg-gray-400 text-white rounded cursor-not-allowed">
                                ❤️ ${a.votesSum}
                            </button>`}
                    </td>
                `;
                tableBody.appendChild(tr);
            });

            document.querySelectorAll('.like-button').forEach(btn => {
                btn.addEventListener('click', async () => {
                    if (!window.IS_LOGGED_IN) return;
                    const anecdoteId = btn.dataset.id;
                    try {
                        const res = await fetch(`/anecdote/${anecdoteId}/like`, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {'X-Requested-With': 'XMLHttpRequest'}
                        });
                        const data = await res.json();
                        if (data.success) btn.textContent = `❤️ ${data.votesSum}`;
                    } catch (err) {
                        console.error(err);
                    }
                });
            });

        } catch (err) {
            console.error(err);
        }
    }

    loadAnecdotes();

    document.addEventListener('topicChanged', e => {
        loadAnecdotes(e.detail);
    });
}
