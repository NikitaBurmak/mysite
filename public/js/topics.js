//загрузка, фильтры, рендер тем
export function initTopics() {
    let currentTopic = 'All topics';
    let currentTopicId = null;

    const renderTopics = topics => {
        const container = document.getElementById('topicsContainer');
        if (!container) return;
        container.innerHTML = '';

        topics.forEach(t => {
            const btn = document.createElement('button');
            btn.className = 'topic-link w-full text-left px-4 py-2 bg-blue-500 text-white rounded mb-1 hover:bg-blue-600';
            btn.textContent = t.name;
            btn.dataset.topic = t.name;
            btn.dataset.topicId = t.id;
            container.appendChild(btn);
        });

        document.querySelectorAll('.topic-link').forEach(link => {
            link.onclick = e => {
                e.preventDefault();
                currentTopic = link.dataset.topic;
                currentTopicId = link.dataset.topicId;
            };
        });
    };

    (async function loadTopics() {
        try {
            const res = await fetch('/api/topics', { credentials: 'same-origin' });
            const topics = await res.json();
            renderTopics(topics);

            if (topics.length) {
                currentTopic = topics[0].name;
                currentTopicId = topics[0].id;
            }
        } catch (err) {
            console.error(err);
        }
    })();
}
