import { post } from './ajax';

window.addEventListener('DOMContentLoaded', function () {
    const forgotPasswordBtn = document.querySelector('.forgot-password-btn') as HTMLButtonElement | null;
    const resetPasswordBtn = document.querySelector('.reset-password-btn') as HTMLButtonElement | null;

    if (forgotPasswordBtn) {
        forgotPasswordBtn.addEventListener('click', function () {
            const form = document.querySelector('.forgot-password-form') as HTMLFormElement | null;
            if (!form) return;

            const emailInput = form.querySelector('input[name="email"]') as HTMLInputElement | null;
            if (!emailInput) return;

            const email = emailInput.value;

            post('/forgot-password', { email }, form).then(response => {
                if (response.ok) {
                    alert('An email with instructions to reset your password has been sent.');
                    window.location.href = '/login';
                }
            });
        });
    }

    if (resetPasswordBtn) {
        resetPasswordBtn.addEventListener('click', function () {
            const form = this.closest('form') as HTMLFormElement | null;
            if (!form) return;

            const formData = new FormData(form);
            const data: Record<string, any> = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });

            post(form.action, data, form).then(response => {
                if (response.ok) {
                    alert('Password has been updated successfully.');
                    window.location.href = '/login';
                }
            });
        });
    }
});
