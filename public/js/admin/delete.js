export function initDeleteButtons() {
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const row = btn.closest('tr');
            const id = row.dataset.id;

            try {
                const res = await fetch(`/admin/anecdote/delete/${id}`, {
                    method: 'POST',
                    headers: {'X-Requested-With': 'XMLHttpRequest'}
                });

                const data = await res.json();
                if (data.success) {
                    row.remove();
                    alert('Анекдот удалён!');
                } else {
                    alert('Ошибка: ' + (data.error || 'Неизвестно'));
                }

            } catch (err) {
                console.error(err);
                alert('Ошибка сети или сервера');
            }
        });
    });
}
