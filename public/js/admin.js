document.addEventListener('DOMContentLoaded', () => {
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

                const text = await res.text();
                console.log('Ответ сервера:', text);

                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    alert('Ошибка парсинга ответа сервера! Открой консоль.');
                    throw e;
                }

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
});
