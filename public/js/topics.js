export function initTopics() {
    const container = document.getElementById('topicsContainer');
    if (!container) return;

    (async function loadTopics() {
        try {
            const res = await fetch('/api/topics', { credentials: 'same-origin' });
            const topics = await res.json();

            container.innerHTML = '';
            topics.forEach(t => {
                const btn = document.createElement('button');
                btn.className = 'topic-link px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600';
                btn.textContent = t.name;
                btn.dataset.topicId = t.id;
                container.appendChild(btn);

                btn.addEventListener('click', () => {
                    window.currentTopicId = t.id;
                    document.dispatchEvent(new CustomEvent('topicChanged', { detail: t.id }));
                });
            });
        } catch (err) {
            console.error(err);
        }
    })();
}
