import { modal, ModalTemplate } from "./modal";
import config from "./config";

const SERVER_ERROR_VALIDATION = 422;

declare global {
    interface Promise<T> {
        aggregate(domElement: HTMLElement | null): Promise<T>;
    }
}

Promise.prototype.aggregate = async function (domElement: HTMLElement | null = null): Promise<Response> {
    const response = await this as Response;
    if (domElement) {
        clearValidationErrors(domElement);
    }
    if (!response.ok) {
        if (response.status === SERVER_ERROR_VALIDATION) {
            response.json().then(errors => {
                handleValidationErrors(errors, domElement);
            });
        } else {
            response.json().then(data => {
                let message = 'Не предвиденная ошибка';
                let modalClass = '';
                if (config.DEBUG) {
                    modalClass = 'modal-xl';
                    const errorDiv = document.createElement('div');
                    errorDiv.classList.add('alert');
                    errorDiv.classList.add('alert-danger');
                    errorDiv.classList.add('text-break');

                    const errorMsg = document.createElement('p');
                    errorMsg.textContent = data.message ?? message;

                    const errorList = document.createElement('ul');
                    errorList.classList.add('error-list');

                    (data.trace ?? []).forEach((error: { [x: string]: string; }) => {
                        const errorItem = document.createElement('li');
                        errorItem.textContent = error['class'] + ' - ' + error['function'] + ' - ' + error['line'];
                        errorList.appendChild(errorItem);
                    });

                    errorDiv.appendChild(errorMsg);
                    errorDiv.appendChild(errorList);

                    message = errorDiv.outerHTML;
                } else {
                    if (data.message) {
                        message = data.message;
                    }
                }
                return createErrorModal(message, modalClass);
            });
        }
    }
    return response;
};

function ajax(url: string, method: string = 'get', data: FormData | Record<string, any> = {}, domElement: HTMLElement | null = null, aggregate = true): Promise<Response> {
    method = method.toLowerCase();

    let options: RequestInit = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    };

    const csrfMethods = new Set(['post', 'put', 'delete', 'patch']);

    if (csrfMethods.has(method)) {
        let additionalFields: Record<string, any> = { ...getCsrfFields() };

        if (method !== 'post') {
            options.method = 'post';
            additionalFields._METHOD = method.toUpperCase();
        }

        if (data instanceof FormData) {
            for (const additionalField in additionalFields) {
                data.append(additionalField, additionalFields[additionalField]);
            }

            delete (options.headers as Record<string, string>)['Content-Type'];

            options.body = data;
        } else {
            options.body = JSON.stringify({ ...data, ...additionalFields });
        }
    } else if (method === 'get') {
        url += '?' + (new URLSearchParams(data as Record<string, string>)).toString();
    }

    const promise = fetch(url, options);
    if (aggregate) {
        return promise.aggregate(domElement);
    } else {
        return promise;
    }
}

function get(url: string, data: Record<string, string> = {}): Promise<Response> {
    return ajax(url, 'get', data);
}

function post(url: string, data: Record<string, any>, domElement: HTMLElement | null = null, aggregate: boolean = true): Promise<Response> {
    return ajax(url, 'post', data, domElement, aggregate);
}

function del(url: string, data: Record<string, string> = {}): Promise<Response> {
    return ajax(url, 'delete', data);
}

function handleValidationErrors(errors: Record<string, any>, domElement: HTMLElement | null) {
    try {
        for (const name in errors) {
            const element = domElement?.querySelector(`[name="${name}"]`);

            element?.classList.add('is-invalid');

            const errorDiv = document.createElement('div');

            errorDiv.classList.add('invalid-feedback');
            errorDiv.textContent = errors[name][0];

            element?.parentNode?.append(errorDiv);
        }
    } catch (_) {
        console.log('not found form elements');
    }
}

function createErrorModal(error: string, modalClass = '') {
    const modalId = 'errorModal';
    let modalWrapper = document.getElementById(`coreModal-${modalId}`);
    if (modalWrapper) {
        modalWrapper.remove();
    }
    return modal({
        'modalId': modalId,
        'modalContent': error,
        'modalClasses': modalClass,
        'modalTitle': 'Ошибка',
    });
}

function clearValidationErrors(domElement: HTMLElement) {
    domElement.querySelectorAll('.is-invalid').forEach(function (element) {
        element.classList.remove('is-invalid');

        element.parentNode?.querySelectorAll('.invalid-feedback').forEach(function (e) {
            e.remove();
        });
    });
}

function getCsrfFields(): Record<string, string> {
    const csrfNameField = document.querySelector('#csrfName') as HTMLMetaElement;
    const csrfValueField = document.querySelector('#csrfValue') as HTMLMetaElement;
    const csrfNameKey = csrfNameField.getAttribute('name')!;
    const csrfName = csrfNameField.content;
    const csrfValueKey = csrfValueField.getAttribute('name')!;
    const csrfValue = csrfValueField.content;

    return {
        [csrfNameKey]: csrfName,
        [csrfValueKey]: csrfValue
    };
}

export {
    ajax,
    get,
    post,
    del,
};
