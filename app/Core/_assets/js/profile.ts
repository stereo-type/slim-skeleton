import { post } from './ajax';

window.addEventListener('DOMContentLoaded', function () {
    const saveProfileBtn = document.querySelector('.save-profile') as HTMLButtonElement | null;
    const updatePasswordBtn = document.querySelector('.update-password') as HTMLButtonElement | null;

    if (saveProfileBtn) {
        saveProfileBtn.addEventListener('click', function () {
            const form = this.closest('form') as HTMLFormElement | null;
            if (!form) return;

            const formData = new FormData(form);

            saveProfileBtn.classList.add('disabled');

            post('/profile', formData, form).then(response => {
                saveProfileBtn.classList.remove('disabled');

                if (response.ok) {
                    alert('Profile has been updated.');
                }
            }).catch(() => {
                saveProfileBtn.classList.remove('disabled');
            });
        });
    }

    if (updatePasswordBtn) {
        updatePasswordBtn.addEventListener('click', function () {
            const form = document.getElementById('passwordForm') as HTMLFormElement | null;
            if (!form) return;

            const formData = new FormData(form);

            updatePasswordBtn.classList.add('disabled');

            post('/profile/update-password', formData, form).then(response => {
                updatePasswordBtn.classList.remove('disabled');

                if (response.ok) {
                    alert('Password has been updated.');
                }
            }).catch(() => {
                updatePasswordBtn.classList.remove('disabled');
            });
        });
    }
});
