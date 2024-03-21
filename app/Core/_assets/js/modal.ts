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

enum ModalBehaviourShow {
    replace = 'replace',
    add = 'add',
    cache = 'cache',
}

enum ModalBehaviourHide {
    hide = 'hide',
    dispose = 'dispose',
}

interface BootstrapModal {
    getElement(): string | Element;
}

/**Класс обертка, нужен потому что нельзя получить доступ к modal._element в TS*/
class SlimModal extends Modal implements BootstrapModal {

    private readonly _modalElement: string | Element;

    constructor(element: string | Element, private params: ModalTemplateParams, options?: Partial<Modal.Options>,) {
        super(element, options);
        this._modalElement = element;
    }

    getElement(): Element {
        return typeof this._modalElement !== "string" ? this._modalElement : document.querySelector(this._modalElement);
    }

    hide() {
        switch (this.params.modalBehaviourHide) {
            case ModalBehaviourHide.hide:
                super.hide();
                break;
            case ModalBehaviourHide.dispose:
                super.hide();
                this.getElement().remove();
                break;
        }
    }

}


class ModalTemplateParams {
    constructor(
        public readonly modalActionType: ModalActionType = ModalActionType.close,
        public readonly modalTitle: string = '',
        public readonly modalClasses: string = '',
        public readonly modalTemplate: string = 'modal.twig',
        public readonly modalBehaviourShow: ModalBehaviourShow = ModalBehaviourShow.add,
        public readonly modalBehaviourHide: ModalBehaviourHide = ModalBehaviourHide.hide,
    ) {
    }

    public static fromMap(map: Record<string, any>): ModalTemplateParams {
        return new ModalTemplateParams(
            map['modalActionType'] ?? ModalActionType.close,
            map['modalTitle'] ?? '',
            map['modalClasses'] ?? '',
            map['modalTemplate'] ?? 'modal.twig',
            map['modalBehaviourShow'] ?? ModalBehaviourShow.add,
            map['modalBehaviourShow'] ?? ModalBehaviourHide.hide,
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

    get_params(): ModalTemplateParams {
        return this.params;
    }

    get_id(): string {
        return this.modalId;
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

function _create_modal_wrapper(content: string): HTMLElement {
    const tempElement = document.createElement('div');
    tempElement.innerHTML = content;
    const modalWrapper = tempElement.firstChild as HTMLElement;
    document.body.appendChild(modalWrapper);
    return modalWrapper
}

function _show_modal(wrapper: HTMLElement, params: ModalTemplateParams): SlimModal {
    const modal = new SlimModal(wrapper, params);

    wrapper.addEventListener('click', function (evt) {
        if (evt.target
            && evt.target instanceof Element
            && evt.target.matches('[data-dismiss="modal"]')) {
            modal.hide();
        }
    });

    modal.show();

    return modal;
}

const modal = async function (content: ModalTemplate | Record<string, any> | string): Promise<SlimModal | null> {
    const template = ModalTemplate.build(content);
    const route = template.get_route();
    // showLoader();
    const modal_params = template.get_params();
    const id = template.get_id();
    let modalWrapper = document.getElementById(id) as HTMLElement | null;

    /**Вариант с показом модалки ранее загруженной на клиент*/
    if (modal_params.modalBehaviourShow == ModalBehaviourShow.cache && modalWrapper != null) {
        return _show_modal(modalWrapper, modal_params);
    } else {
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

                    if (modal_params.modalBehaviourShow == ModalBehaviourShow.replace) {
                        modalWrapper = modalWrapper ?? _create_modal_wrapper(data['modal'].toString());
                    } else if (modal_params.modalBehaviourShow == ModalBehaviourShow.add) {
                        modalWrapper = _create_modal_wrapper(data['modal'].toString());
                    }

                    modalWrapper =  modalWrapper ?? _create_modal_wrapper(data['modal'].toString());
                    if (modalWrapper) {
                        return _show_modal(modalWrapper, modal_params);
                    } else {
                        throw Error('no modal wrapper');
                    }
                }
                return null;
            });
        }
    }

}

export {
    modal,
    SlimModal,
    ModalTemplate,
    ModalTemplateHtml,
    ModalTemplateAjax,
    ModalTemplateParams,
    ModalActionType,
    ModalBehaviourShow,
    ModalBehaviourHide,
};
