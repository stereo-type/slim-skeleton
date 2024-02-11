import {post} from "./ajax";
import {Modal} from 'bootstrap';

const modal = function (content) {
    return post(
        '/modal', content,
        null,
        false
    ).then((response) => {
        if (!response.ok) {
            alert('Ошибка отображения модального окна');
        } else {
            response.json().then(data => {
                if (data['modal']) {
                    const tempElement = document.createElement('div');
                    tempElement.innerHTML = data['modal'].toString();
                    const modalWrapper = tempElement.firstChild;
                    document.body.appendChild(modalWrapper);

                    const modal = new Modal(modalWrapper);
                    modalWrapper.addEventListener('click', function (evt) {
                        if (evt.target && evt.target.matches('[data-dismiss="modal"]')) {
                            modal.hide(); // Здесь предполагается, что у вас есть объект модального окна с методом hide()
                        }
                    });
                    modal.show();
                }
            })
        }
    });
}

export {
    modal,
}