import "../css/auth.scss";
import {post} from './ajax';
import {Modal} from 'bootstrap';

window.addEventListener('DOMContentLoaded', function () {
    const twoFactorAuthModalElement = document.getElementById('twoFactorAuthModal');
    const twoFactorAuthModal = new Modal(twoFactorAuthModalElement);

    document.querySelector('.log-in-btn')!.addEventListener('click', function () {
        const form = this.closest('form') as HTMLFormElement | null;
        if (!form) return;

        const formData = new FormData(form);

        post(form.action, formData, form).then(response => response.json()).then(response => {
            if (response['two_factor']) {
                twoFactorAuthModal.show();
            } else {
                window.location.href = '/';
            }
        });
    });

    document.querySelector('.log-in-two-factor')!.addEventListener('click', function () {
        const codeInput = twoFactorAuthModalElement.querySelector('input[name="code"]') as HTMLInputElement | null;
        if (!codeInput) return;

        const code = codeInput.value;
        const emailInput = document.querySelector('.login-form input[name="email"]') as HTMLInputElement | null;

        if (!emailInput) return;
        const email = emailInput.value;

        post('/login/two-factor', {email, code}, twoFactorAuthModalElement).then(response => {
            if (response.ok) {
                window.location.href = '/';
            }
        });
    });
});
