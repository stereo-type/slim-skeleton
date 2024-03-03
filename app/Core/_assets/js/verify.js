import {post} from './ajax';

window.addEventListener('DOMContentLoaded', function () {
    document.querySelector('.resend-verify').addEventListener('click', function () {
        post(`/verify`, {})
            .then(function (response) {
                if (response.ok) alert('A new email verification has been successful sent!');
            })
    })
})
