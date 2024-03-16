import "../css/modal.scss"
import {post} from "./ajax";
import {Modal} from 'bootstrap';

/**Model объекта модалки, расширяться будет по мере необходимости*/
class ModalTemplate {

    static build(data) {
        if (data instanceof ModalTemplate) {
            return data;
        }
        return ModalTemplate.fromMap(data);
    }

    constructor(
        modalId,
        modalType = 'html',
        modalContent,
        modalTitle = '',
        modalClasses = ''
    ) {
        if (modalType !== 'html') {
            //TODO
            throw new Error('Пока не поддерживается, задел на получение контента после показа модалки из аякса!');
        }
        Object.assign(this, {
            modalId,
            modalType,
            modalContent,
            modalTitle,
            modalClasses
        });
    }


    static fromMap(map) {

        if (typeof map === 'string') {
            map = {'modalContent': map};
        }

        return new ModalTemplate(
            map['modalId'] ?? Math.floor(Math.random() * 1000),
            map['modalType'] ?? 'html',
            map['modalContent'] ?? '',
            map['modalTitle'] ?? '',
            map['modalClasses'] ?? ''
        );
    }

    toMap() {
        const obj = {};
        const properties = Object.getOwnPropertyNames(this);
        properties.forEach(property => {
            obj[property] = this[property];
        });
        return obj;
    }
}

const modal = function (content) {
    return post(
        '/modal',
        ModalTemplate.build(content).toMap(),
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
                        if (evt.target && evt.target instanceof Element && evt.target.matches('[data-dismiss="modal"]')) {
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
    ModalTemplate,
}