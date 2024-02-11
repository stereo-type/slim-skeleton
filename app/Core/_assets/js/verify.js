import {post, aggregate} from './ajax';
import config from "./config";

window.addEventListener('DOMContentLoaded', function () {
    document.querySelector('.resend-verify').addEventListener('click', function (event) {
        post(`/verify`)
            .then((response) => aggregate(response, null, null, config.DEBUG))
    })
})
