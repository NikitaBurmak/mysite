export function initTopics() {
    const container = document.getElementById('topicsContainer');
    if (!container) return;

    (async function loadTopics() {
        try {
            const res = await fetch('/api/topics', { credentials: 'same-origin' });
            const topics = await res.json();

            container.innerHTML = '';

            const allBtn = document.createElement('button');
            allBtn.className = 'topic-link px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600';
            allBtn.textContent = 'All Topics';
            allBtn.dataset.topicId = '';
            allBtn.addEventListener('click', () => {
                window.currentTopicId = null;
                document.dispatchEvent(new CustomEvent('topicChanged', { detail: null }));
            });
            container.appendChild(allBtn);

            topics.forEach(t => {
                const btn = document.createElement('button');
                btn.className = 'topic-link px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600';
                btn.textContent = t.name;
                btn.dataset.topicId = t.id;
                btn.addEventListener('click', () => {
                    window.currentTopicId = t.id;
                    document.dispatchEvent(new CustomEvent('topicChanged', { detail: t.id }));
                });
                container.appendChild(btn);
            });

        } catch (err) {
            console.error(err);
        }
    })();
}
