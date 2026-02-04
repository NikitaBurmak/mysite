document.addEventListener('DOMContentLoaded', () => {

        const authModal = document.getElementById('authModal');
        const addAnecdoteModal = document.getElementById('addAnecdoteModal');
        const loginButton = document.getElementById('loginButton');
        const avatarBtn = document.getElementById('userAvatar');
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const addAnecdoteForm = document.getElementById('addAnecdoteForm');
        const tabLogin = document.getElementById('tab-login');
        const tabRegister = document.getElementById('tab-register');
        const loginContainer = document.getElementById('login-form');
        const registerContainer = document.getElementById('register-form');

        let anecdotesByTopic = {};
        let currentTopic = 'All topics';
        let currentTopicId = null;
        window.IS_LOGGED_IN = !!window.IS_LOGGED_IN;

        const openModal = modal => modal && (modal.style.display = 'block');
        const closeModal = modal => modal && (modal.style.display = 'none');
        const escapeHtml = str => String(str).replace(/[&<>"']/g, s => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[s]));

        function clearAuthForms() {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            loginForm?.reset();
            registerForm?.reset();
        }

        document.getElementById('logoutButton')?.addEventListener('click', async (e) => {
            e.preventDefault();
            try {
                await fetch('/logout', {method: 'POST', credentials: 'same-origin'});
                updateUIForLoggedInUser(null);
                window.location.href = '/';  // после выхода переходим на главную
            } catch (err) {
                console.error('Ошибка выхода', err);
            }
        });


        const updateUIForLoggedInUser = (user = null) => {
            window.IS_LOGGED_IN = !!user;
            window.CURRENT_USER = user;

            const loginButton = document.getElementById('loginButton');
            const avatarBtn = document.getElementById('userAvatar');

            if (loginButton) loginButton.style.display = window.IS_LOGGED_IN ? 'none' : 'inline-block';
            if (avatarBtn) avatarBtn.style.display = window.IS_LOGGED_IN ? 'inline-block' : 'none';

            renderTableByTopic();
        };
        const logoutButton = document.getElementById('logoutButton');
        logoutButton?.addEventListener('click', async (e) => {
            e.preventDefault();
            try {
                await fetch('/logout', {method: 'POST', credentials: 'same-origin'});
                updateUIForLoggedInUser(null); // показываем кнопку "Войти" и серые лайки
            } catch (err) {
                console.error('Ошибка выхода', err);
            }
        });

        const fetchCurrentUser = async () => {
            try {
                const res = await fetch('/api/current_user', {credentials: 'same-origin'});
                if (!res.ok) return;
                const data = await res.json();
                updateUIForLoggedInUser(data);
            } catch (err) {
                console.error('Fetch current user failed', err);
            }
        };
        fetchCurrentUser();

        document.querySelectorAll('.close').forEach(btn =>
            btn.addEventListener('click', () => {
                const modal = btn.closest('.modal');
                closeModal(modal);
                if (modal.id === 'authModal') {
                    clearAuthForms();
                }
            })
        );
        loginButton?.addEventListener('click', () => openModal(authModal));
        document.querySelectorAll('.show-login').forEach(btn => btn.addEventListener('click', () => openModal(authModal)));

        const switchTab = (tab) => {
            if (tab === 'login') {
                loginContainer.style.display = 'block';
                registerContainer.style.display = 'none';
                tabLogin.classList.add('active');
                tabRegister.classList.remove('active');
            } else {
                loginContainer.style.display = 'none';
                registerContainer.style.display = 'block';
                tabRegister.classList.add('active');
                tabLogin.classList.remove('active');
            }
        };
        tabLogin?.addEventListener('click', () => switchTab('login'));
        tabRegister?.addEventListener('click', () => switchTab('register'));

        const attachTopicFilters = () => {
            document.querySelectorAll('.topic-link').forEach(link => {
                link.onclick = e => {
                    e.preventDefault();
                    currentTopic = link.dataset.topic;
                    currentTopicId = link.dataset.topicId;
                    renderTableByTopic();
                };
            });
        };
        const renderTopics = (topics) => {
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

            attachTopicFilters();
        };
        const attachLikeButtons = () => {
            document.addEventListener('click', async (e) => {
                const btn = e.target.closest('button.like-button');
                if (!btn) return;

                if (!window.IS_LOGGED_IN) {
                    openModal(authModal);
                    return;
                }

                const url = btn.dataset.url;
                if (!url) return;

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {'X-Requested-With': 'XMLHttpRequest'},
                        credentials: 'same-origin'
                    });

                    if (res.status === 403) {
                        window.IS_LOGGED_IN = false;
                        updateUIForLoggedInUser(null);
                        openModal(authModal);
                        return;
                    }

                    const data = await res.json();
                    if (data.votes !== undefined) {
                        btn.dataset.count = data.votes;
                        btn.textContent = `❤️ ${data.votes}`;
                    }
                } catch (err) {
                    console.error(err);
                    alert('Ошибка сети');
                }
            });
        };
        attachLikeButtons();

        document.body.addEventListener('click', e => {
            if (e.target.id === 'addAnecdoteButton') {
                if (!window.IS_LOGGED_IN) {
                    openModal(authModal);
                    return;
                }
                openModal(addAnecdoteModal);
            }
        });

        addAnecdoteForm?.addEventListener('submit', async e => {
            e.preventDefault();
            if (!window.IS_LOGGED_IN) {
                openModal(authModal);
                return;
            }
            const url = addAnecdoteForm.dataset.url;
            if (!url) return alert('Не задан url для добавления');

            const formData = new FormData(addAnecdoteForm);
            formData.append('topic_id', currentTopicId);

            try {
                const res = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {'X-Requested-With': 'XMLHttpRequest'},
                    credentials: 'same-origin'
                });
                const data = await res.json();

                if (data.success) {
                    addAnecdoteForm.reset();
                    closeModal(addAnecdoteModal);
                    renderTableByTopic();
                    if (!anecdotesByTopic[data.topic]) anecdotesByTopic[data.topic] = [];
                    anecdotesByTopic[data.topic].push({
                        id: data.id,
                        text: data.text,
                        votesSum: data.votesSum || 0,
                        topic: data.topic
                    });
                    renderTableByTopic();
                    closeModal(addAnecdoteModal);
                } else {
                    alert(data.error || 'Ошибка при добавлении');
                }
            } catch (err) {
                console.error(err);
                alert('Ошибка сети');
            }
        });

        const ajaxPostForm = async formEl => {
            const url = formEl.dataset.url;
            if (!url) throw new Error('No data-url on form');
            const formData = new FormData(formEl);
            const res = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {'X-Requested-With': 'XMLHttpRequest'},
                credentials: 'same-origin'
            });
            const text = await res.text();
            console.log('Ответ с сервера:', text);
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Не JSON в ответе: ' + text);
            }
        };
        (async function loadTopics() {
            try {
                const res = await fetch('/api/topics', {credentials: 'same-origin'});
                if (!res.ok) return console.error(await res.text());
                const topics = await res.json();
                renderTopics(topics);

                if (topics.length) {
                    currentTopic = topics[0].name;
                    currentTopicId = topics[0].id;
                }
            } catch (err) {
                console.error('Fetch /api/topics failed', err);
            }
        })();


        loginForm?.addEventListener('submit', async e => {
            e.preventDefault();
            try {
                const data = await ajaxPostForm(loginForm);
                if (data.success) {
                    updateUIForLoggedInUser(data.user);
                    renderTableByTopic();
                    clearAuthForms();
                    authModal.style.display = 'none';

                    if (data.roles.includes('ROLE_ADMIN')) {
                        window.location.href = '/admin';
                    } else {
                        window.location.href = '/';
                    }
                } else {
                    alert(data.error || 'Ошибка входа');
                }
            } catch (err) {
                console.error(err);
                alert('Ошибка сети или не JSON: ' + err.message);
            }
        });
        registerForm?.addEventListener('submit', async e => {
            e.preventDefault();
            try {
                const data = await ajaxPostForm(registerForm);
                if (data.success) {
                    updateUIForLoggedInUser(data.user);
                    renderTableByTopic();
                    clearAuthForms();
                    authModal.style.display = 'none';

                    if (data.roles && Array.isArray(data.roles) && data.roles.includes('ROLE_ADMIN')) {
                        window.location.href = '/admin';
                        return;
                    }
                } else {
                    alert(data.error || 'ошибка регистрации');
                }
            } catch (err) {
                console.error(err);
                alert(err.message || 'Ошибка сети');
            }
        });
        const renderTableByTopic = () => {
            const tbody = document.querySelector('.content table tbody');
            if (!tbody) return;
            tbody.innerHTML = '';
            const list = anecdotesByTopic[currentTopic] || [];
            if (!list.length) return tbody.innerHTML = '<tr><td colspan="3">Нет анекдотов 😅</td></tr>';

            list.sort((a, b) => Number(a.id) - Number(b.id));

            list.forEach((a, idx) => {
                const tr = document.createElement('tr');
                tr.dataset.topic = a.topic || 'All topics';
                tr.innerHTML = `
                <td>${idx + 1}</td>
                <td>${escapeHtml(a.text)}</td>
                <td>
                   ${window.IS_LOGGED_IN ?
                    `<button class="like-button" data-url="/anecdote/${a.id}/like" data-count="${a.votesSum || 0}">❤️ ${a.votesSum || 0}</button>` :
                    `<button class="show-login like-button px-3 py-1 bg-gray-400 text-white rounded cursor-not-allowed">❤️ ${a.votesSum || 0}</button>`}
                </td>
            `;
                tbody.appendChild(tr);
            });
        };
        window.IS_LOGGED_IN = false;
        window.CURRENT_USER = null;
        updateUIForLoggedInUser(null, {skipRender: true});


        (async function loadAnecdotes() {
            try {
                const res = await fetch('/api/anecdotes', {credentials: 'same-origin'});
                if (!res.ok) return console.error(await res.text());
                const data = await res.json();

                anecdotesByTopic = {};
                data.forEach(a => {
                    const topic = a.topic || 'All topics';
                    if (!anecdotesByTopic[topic]) anecdotesByTopic[topic] = [];
                    anecdotesByTopic[topic].push(a);
                });

                Object.keys(anecdotesByTopic).forEach(topic => {
                    anecdotesByTopic[topic].sort((a, b) => Number(a.id) - Number(b.id));
                });

                renderTableByTopic();
            } catch (err) {
                console.error('Fetch /api/anecdotes failed', err);
            }

        })();

    }
)
;
