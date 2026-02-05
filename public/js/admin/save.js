export function initSaveButtons() {
    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const row = btn.closest('tr');
            const id = row.dataset.id;
            const text = row.querySelector('.edit-text').value.trim();

            const res = await fetch('/admin/anecdote/save', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: new URLSearchParams({id, text})
            });

            const data = await res.json();
            if (data.success) {
                alert('Сохранено!');
            } else {
                alert('Ошибка: ' + (data.error || 'Неизвестно'));
            }
        });
    });
}
