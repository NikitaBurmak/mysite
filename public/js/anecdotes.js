//рендер таблицы, лайки, добавление анекдота
export function initAnecdotes() {
    let anecdotesByTopic = {};

    const renderTableByTopic = () => {
        const tbody = document.querySelector('.content table tbody');
        if (!tbody) return;
        tbody.innerHTML = '';
        const list = anecdotesByTopic[currentTopic] || [];
        if (!list.length) return tbody.innerHTML = '<tr><td colspan="3">Нет анекдотов</td></tr>';

        list.sort((a, b) => a.id - b.id);
        list.forEach((a, idx) => {
            const tr = document.createElement('tr');
            tr.dataset.topic = a.topic || 'All topics';
            tr.innerHTML = `
                <td>${idx + 1}</td>
                <td>${a.text}</td>
                <td>
                    <button class="like-button" data-url="/anecdote/${a.id}/like" data-count="${a.votesSum || 0}">❤️ ${a.votesSum || 0}</button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    };

    (async function loadAnecdotes() {
        try {
            const res = await fetch('/api/anecdotes', { credentials: 'same-origin' });
            const data = await res.json();

            anecdotesByTopic = {};
            data.forEach(a => {
                const topic = a.topic || 'All topics';
                if (!anecdotesByTopic[topic]) anecdotesByTopic[topic] = [];
                anecdotesByTopic[topic].push(a);
            });

            renderTableByTopic();
        } catch (err) {
            console.error(err);
        }
    })();
}
