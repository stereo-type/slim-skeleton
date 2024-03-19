import "../css/modal.scss";
import {post} from "./ajax";
import {Modal} from 'bootstrap';

enum ModalActionType {
    none = 'none',
    close = 'close',
    save_and_close = 'save_and_close',
    save = 'save'
}

enum ModalDataType {
    html = 'html',
    ajax = 'ajax',
    ajax_form = 'ajax_form',
}

interface BootstrapModal {
    getElement(): string | Element;
}

/**Класс обертка, нужен потому что нельзя получить доступ к modal._element в TS*/
class SlimModal extends Modal implements BootstrapModal {

    private readonly _modalElement: string | Element;

    constructor(element: string | Element, options?: Partial<Modal.Options>) {
        super(element, options);
        this._modalElement = element;
    }

    getElement(): Element {
        return typeof this._modalElement !== "string"?   this._modalElement : document.querySelector(this._modalElement);
    }

}


class ModalTemplateParams {
    constructor(
        public readonly modalActionType: ModalActionType = ModalActionType.close,
        public readonly modalTitle: string = '',
        public readonly modalClasses: string = '',
    ) {
    }

    public static fromMap(map: Record<string, any>): ModalTemplateParams {
        return new ModalTemplateParams(
            map['modalActionType'] ?? ModalActionType.close,
            map['modalTitle'] ?? '',
            map['modalClasses'] ?? '',
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

abstract class ModalTemplate {
    protected constructor(
        protected modalId: string,
        protected modalType: ModalDataType = ModalDataType.html,
        protected params: ModalTemplateParams = new ModalTemplateParams()
    ) {
    }

    get_route(): string {
        return '/modal';
    }

    static build(data: ModalTemplate | Record<string, any> | string): ModalTemplate {
        if (data instanceof ModalTemplate) {
            return data;
        }
        if (typeof data === 'string') {
            data = {
                'modalContent': data
            };
        }
        return this.fromMap(data);
    }

    static fromMap(map: Record<string, any>): ModalTemplate {
        const modalType = map['modalType'] ?? ModalDataType.html;
        const id = (map['modalId'] ?? Math.floor(Math.random() * 1000)).toString();

        switch (modalType) {
            case ModalDataType.html:
                return new ModalTemplateHtml(
                    id,
                    map['modalContent'],
                    ModalTemplateParams.fromMap(map),
                );

            default:
                throw new Error('unsupporteed yet ' + modalType);
        }
    }


    toMap(): Record<string, any> {
        const obj: Record<string, any> = {};
        const properties = Object.getOwnPropertyNames(this);
        properties.forEach(property => {
            let prop = (this as Record<string, any>)[property];
            if (typeof prop.toMap === 'function') {
                prop = prop.toMap();
            }
            obj[property] = prop;
        });
        return obj;
    }
}

/**Model объекта модалки, расширяться будет по мере необходимости*/
class ModalTemplateHtml extends ModalTemplate {
    constructor(
        protected modalId: string,
        protected modalContent: string,
        protected params: ModalTemplateParams = new ModalTemplateParams()
    ) {
        super(modalId, ModalDataType.html, params);
    }

}

class ModalTemplateAjax extends ModalTemplate {
    constructor(
        protected modalId: string,
        protected route: string,
        protected formParams: Record<string, any>,
        protected params: ModalTemplateParams = new ModalTemplateParams(),
    ) {
        super(modalId, ModalDataType.ajax, params);
    }

    get_route(): string {
        return this.route;
    }

}

const modal = async function (content: ModalTemplate | Record<string, any> | string): Promise<SlimModal | null> {
    const template = ModalTemplate.build(content);
    const route = template.get_route();
    // showLoader();

    const response = await post(
        route,
        template.toMap(),
        null,
        false
    );
    // dismissLoader();

    if (!response.ok) {
        alert('Ошибка отображения модального окна');
        return null;
    } else {
        return response.json().then(data => {
            if (data['modal']) {
                const tempElement = document.createElement('div');
                tempElement.innerHTML = data['modal'].toString();
                const modalWrapper = tempElement.firstChild as HTMLElement;
                document.body.appendChild(modalWrapper);

                const modal = new SlimModal(modalWrapper);


                modalWrapper.addEventListener('click', function (evt) {
                    if (evt.target && evt.target instanceof Element && evt.target.matches('[data-dismiss="modal"]')) {
                        modal.hide(); // Здесь предполагается, что у вас есть объект модального окна с методом hide()
                    }
                });
                modal.show();
                return modal;
            }
            return null;
        });
    }
}

export {
    modal,
    SlimModal,
    ModalTemplateHtml,
    ModalTemplateAjax,
    ModalTemplateParams,
    ModalActionType
};
