export function initProfile() {
    const avatarWrapper = document.getElementById('avatarWrapper');
    const avatarInput = document.getElementById('avatarInput');
    const avatarImage = document.getElementById('avatarImage');

    if (avatarWrapper && avatarInput) {
        avatarWrapper.addEventListener('click', () => {
            avatarInput.click();
        });

        avatarInput.addEventListener('change', async () => {
            const file = avatarInput.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('avatar', file);

            try {
                const res = await fetch('/api/profile/avatar', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();

                if (data.success) {
                    const newSrc = data.avatarPath + '?t=' + Date.now();

                    if (avatarImage.tagName === 'IMG') {
                        avatarImage.src = newSrc;
                    } else {
                        const img = document.createElement('img');
                        img.src = newSrc;
                        img.alt = 'avatar';
                        img.id = 'avatarImage';
                        avatarImage.replaceWith(img);
                    }

                    const headerAvatarContainer = document.querySelector('header .user-avatar');
                    if (headerAvatarContainer) {
                        const existingImg = headerAvatarContainer.querySelector('img');
                        if (existingImg) {
                            existingImg.src = newSrc;
                        } else {
                            headerAvatarContainer.innerHTML = '';
                            const img = document.createElement('img');
                            img.src = newSrc;
                            img.alt = 'avatar';
                            headerAvatarContainer.appendChild(img);
                        }
                    }
                } else {
                    alert(data.error || 'Ошибка загрузки аватара');
                }
            } catch (err) {
                alert('Ошибка сети');
            }

            avatarInput.value = '';
        });
    }

    const nicknameText = document.getElementById('nicknameText');
    const nicknameEditIcon = document.getElementById('nicknameEditIcon');
    const nicknameEditForm = document.getElementById('nicknameEditForm');
    const nicknameInput = document.getElementById('nicknameInput');
    const nicknameSaveBtn = document.getElementById('nicknameSaveBtn');
    const nicknameCancelBtn = document.getElementById('nicknameCancelBtn');

    function showNicknameEdit() {
        if (nicknameText) nicknameText.style.display = 'none';
        if (nicknameEditIcon) nicknameEditIcon.style.display = 'none';
        if (nicknameEditForm) nicknameEditForm.style.display = 'flex';
        if (nicknameInput) nicknameInput.focus();
    }

    function hideNicknameEdit() {
        if (nicknameText) nicknameText.style.display = 'inline';
        if (nicknameEditIcon) nicknameEditIcon.style.display = 'inline';
        if (nicknameEditForm) nicknameEditForm.style.display = 'none';
    }

    if (nicknameText) {
        nicknameText.addEventListener('click', () => {
            if (nicknameText.classList.contains('no-nickname')) {
                showNicknameEdit();
            }
        });
    }

    if (nicknameEditIcon) {
        nicknameEditIcon.addEventListener('click', showNicknameEdit);
    }

    if (nicknameCancelBtn) {
        nicknameCancelBtn.addEventListener('click', (e) => {
            e.preventDefault();
            hideNicknameEdit();
        });
    }

    if (nicknameSaveBtn) {
        nicknameSaveBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            const nickname = nicknameInput.value.trim();
            if (!nickname) {
                alert('Никнейм не может быть пустым');
                return;
            }

            try {
                const res = await fetch('/api/profile/nickname', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nickname })
                });
                const data = await res.json();

                if (data.success) {
                    nicknameText.textContent = nickname;
                    nicknameText.classList.remove('no-nickname');
                    hideNicknameEdit();
                } else {
                    alert(data.error || 'Ошибка сохранения никнейма');
                }
            } catch (err) {
                alert('Ошибка сети');
            }
        });
    }

    const emailForm = document.getElementById('emailForm');
    if (emailForm) {
        emailForm.addEventListener('submit', async e => {
            e.preventDefault();
            const email = emailForm.email.value;
            const res = await fetch('/api/profile/email', {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({email})
            });
            const data = await res.json();
            alert(data.success ? 'Email изменён' : data.error);
        });
    }

    const passwordForm = document.getElementById('passwordForm');
    if (passwordForm) {
        passwordForm.addEventListener('submit', async e => {
            e.preventDefault();
            const oldPassword = passwordForm.oldPassword.value;
            const newPassword = passwordForm.newPassword.value;
            const res = await fetch('/api/profile/password', {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({oldPassword, newPassword})
            });
            const data = await res.json();
            alert(data.success ? 'Пароль изменён' : data.error);
        });
    }
}
