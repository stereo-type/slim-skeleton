import "../css/modal.scss";
import {post} from "./ajax";
import {Modal} from 'bootstrap';

/**Model объекта модалки, расширяться будет по мере необходимости*/
class ModalTemplate {

    static build(data: ModalTemplate | Record<string, any> | string): ModalTemplate {
        if (data instanceof ModalTemplate) {
            return data;
        }
        if(typeof data === 'string') {
            data = {

            };
        }

        return ModalTemplate.fromMap(data);
    }

    constructor(
        private modalId: number,
        private modalType: string = 'html',
        private modalContent: string,
        private modalTitle: string = '',
        private modalClasses: string = ''
    ) {
        if (this.modalType !== 'html') {
            //TODO
            throw new Error('Пока не поддерживается, задел на получение контента после показа модалки из аякса!');
        }
    }

    static fromMap(map: Record<string, any>): ModalTemplate {
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

    toMap(): Record<string, any> {
        const obj: Record<string, any> = {};
        const properties = Object.getOwnPropertyNames(this);
        properties.forEach(property => {
            obj[property] = (this as Record<string, any>)[property];
        });
        return obj;
    }
}

const modal = function (content: ModalTemplate | Record<string, any> | string): Promise<void> {
    return post(
        '/modal',
        ModalTemplate.build(content).toMap(),
        null,
        false
    ).then((response: { ok: any; json: () => Promise<any>; }) => {
        if (!response.ok) {
            alert('Ошибка отображения модального окна');
        } else {
            response.json().then(data => {
                if (data['modal']) {
                    const tempElement = document.createElement('div');
                    tempElement.innerHTML = data['modal'].toString();
                    const modalWrapper = tempElement.firstChild as HTMLElement;
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
};
