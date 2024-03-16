import { post } from './ajax';

window.addEventListener('DOMContentLoaded', () => {
    const resendVerifyBtn = document.querySelector('.resend-verify') as HTMLButtonElement | null;

    if (resendVerifyBtn) {
        resendVerifyBtn.addEventListener('click', () => {
            post(`/verify`, {})
                .then(response => {
                    if (response.ok) alert('A new email verification has been successfully sent!');
                });
        });
    }
});
