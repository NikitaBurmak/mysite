export function initCheckbox () {
    const topicCheckboxes = document.querySelectorAll('.topic-checkbox');

    topicCheckboxes.forEach(cb => {
        cb.addEventListener('change', async () => {
            const selectedTopicIds = [...document.querySelectorAll('.topic-checkbox:checked')]
                .map(el => el.value);

            updateSelectedTopicsUI(selectedTopicIds);

            const res = await fetch(`/api/anecdotes?topics[]=${selectedTopicIds.join('&topics[]=')}`);
            const anecdotes = await res.json();

            renderAnecdotesTable(anecdotes);
        });
    });

    function updateSelectedTopicsUI(topicIds) {
        const container = document.getElementById('selectedTopics');
        container.innerHTML = '';
        topicIds.forEach(id => {
            const topicName = document.querySelector(`.topic-checkbox[value="${id}"]`).nextSibling.textContent;
            const span = document.createElement('span');
            span.textContent = topicName;
            const btn = document.createElement('button');
            btn.textContent = 'x';
            btn.addEventListener('click', () => {
                document.querySelector(`.topic-checkbox[value="${id}"]`).checked = false;
                span.remove();
                topicCheckboxes.forEach(cb => cb.dispatchEvent(new Event('change')));
            });
            span.appendChild(btn);
            container.appendChild(span);
        });
    }

    function renderAnecdotesTable(anecdotes) {
        const tbody = document.getElementById('anecdotesTableBody');
        tbody.innerHTML = '';
        anecdotes.forEach(a => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
            <td>${a.id}</td>
            <td>${a.text}</td>
            <td>
                <button class="delete-btn" data-id="${a.id}">Удалить</button>
            </td>`;
            tbody.appendChild(tr);
        });
    }
}
