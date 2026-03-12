export function initTopics() {
    const container = document.getElementById('topicsContainer');
    if (!container) return;

    const selectedTopicIds = new Set();

    function readTopicsFromURL() {
        const params = new URLSearchParams(window.location.search);
        const topicsParam = params.get('topics');
        if (topicsParam) {
            const ids = topicsParam.split(',').map(id => parseInt(id)).filter(id => !isNaN(id));
            ids.forEach(id => selectedTopicIds.add(id));
        }
        const topicsArray = params.getAll('topics[]');
        if (topicsArray.length > 0) {
            topicsArray.forEach(id => {
                const parsed = parseInt(id);
                if (!isNaN(parsed)) selectedTopicIds.add(parsed);
            });
        }
    }

    function updateURL() {
        const url = new URL(window.location.href);
        url.searchParams.delete('topics');
        url.searchParams.delete('topics[]');

        if (selectedTopicIds.size > 0) {
            selectedTopicIds.forEach(id => {
                url.searchParams.append('topics[]', id);
            });
        }
        window.history.replaceState({}, '', url);
    }

    readTopicsFromURL();

    (async function loadTopics() {
        try {
            const res = await fetch('/api/topics', { credentials: 'same-origin' });
            const topics = await res.json();

            container.innerHTML = '';

            topics.forEach(t => {
                const btn = document.createElement('button');
                btn.className = 'topic-link';
                btn.textContent = t.name;
                btn.dataset.topicId = t.id;

                if (selectedTopicIds.has(t.id)) {
                    btn.classList.add('active');
                }

                btn.addEventListener('click', () => {
                    toggleTopic(t.id, btn);
                });

                container.appendChild(btn);
            });

            if (selectedTopicIds.size > 0) {
                document.dispatchEvent(new CustomEvent('topicsChanged', {
                    detail: Array.from(selectedTopicIds)
                }));
            }

        } catch (err) {
            console.error(err);
        }
    })();

    function toggleTopic(topicId, btn) {
        if (selectedTopicIds.has(topicId)) {
            selectedTopicIds.delete(topicId);
            btn.classList.remove('active');
        } else {
            selectedTopicIds.add(topicId);
            btn.classList.add('active');
        }

        updateURL();

        const selectedArray = Array.from(selectedTopicIds);
        document.dispatchEvent(new CustomEvent('topicsChanged', {
            detail: selectedArray
        }));
    }

    window.getSelectedTopics = () => Array.from(selectedTopicIds);
}
