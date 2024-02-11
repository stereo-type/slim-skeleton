import {modal} from "./modal";
import config from "./config";

const SERVER_ERROR_BAD_REQUEST = 400;
const SERVER_ERROR_NOT_FOUND = 404;
const SERVER_ERROR_VALIDATION = 422;


Promise.prototype.aggregate = async function (domElement = null) {
    const response = await this;
    if (domElement) {
        clearValidationErrors(domElement)
    }
    if (!response.ok) {
        if (response.status === SERVER_ERROR_VALIDATION) {
            response.json().then(errors => {
                handleValidationErrors(errors, domElement)
            })
        } else {
            response.json().then(data => {
                let message = 'Не предвиденная ошибка';
                let modalClass = '';
                if (config.DEBUG) {
                    modalClass = 'modal-xl';
                    const errorDiv = document.createElement('div')
                    errorDiv.classList.add('alert')
                    errorDiv.classList.add('alert-danger')

                    const errorMsg = document.createElement('p');
                    errorMsg.textContent = data.message ?? message;

                    const errorList = document.createElement('ul');
                    errorList.classList.add('error-list');

                    (data.trace ?? []).forEach(error => {
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
                return createErrorModal(message,modalClass);
            })
        }
    }
    return response;
}

const ajax = (url, method = 'get', data = {}, domElement = null, aggregate = true) => {
    method = method.toLowerCase()

    let options = {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    }

    const csrfMethods = new Set(['post', 'put', 'delete', 'patch'])

    if (csrfMethods.has(method)) {
        let additionalFields = {...getCsrfFields()}

        if (method !== 'post') {
            options.method = 'post'

            additionalFields._METHOD = method.toUpperCase()
        }

        if (data instanceof FormData) {
            for (const additionalField in additionalFields) {
                data.append(additionalField, additionalFields[additionalField])
            }

            delete options.headers['Content-Type'];

            options.body = data
        } else {
            options.body = JSON.stringify({...data, ...additionalFields})
        }
    } else if (method === 'get') {
        url += '?' + (new URLSearchParams(data)).toString();
    }

    if (aggregate) {
        return fetch(url, options).aggregate(domElement);
    } else {
        return fetch(url, options);
    }
}

const get = (url, data) => ajax(url, 'get', data)
const post = (url, data, domElement = null, aggregate = true) => ajax(url, 'post', data, domElement, aggregate)
const del = (url, data) => ajax(url, 'delete', data)


function handleValidationErrors(errors, domElement) {
    try {
        for (const name in errors) {
            const element = domElement.querySelector(`[name="${name}"]`)

            element.classList.add('is-invalid')

            const errorDiv = document.createElement('div')

            errorDiv.classList.add('invalid-feedback')
            errorDiv.textContent = errors[name][0]

            element.parentNode.append(errorDiv)
        }
    } catch (_) {
        console.log('not found form elements');
    }
}

// Функция для создания модального окна
function createErrorModal(error, modalClass = '') {
    const modalId = 'errorModal';
    let modalWrapper = document.getElementById(`coreModal-${modalId}`);
    if (modalWrapper) {
        modalWrapper.remove();
    }
    modal({
        'modalId': modalId,
        'modalContent': error,
        'modalClass': modalClass
    });
}


function clearValidationErrors(domElement) {
    domElement.querySelectorAll('.is-invalid').forEach(function (element) {
        element.classList.remove('is-invalid')

        element.parentNode.querySelectorAll('.invalid-feedback').forEach(function (e) {
            e.remove()
        })
    })
}

function getCsrfFields() {
    const csrfNameField = document.querySelector('#csrfName')
    const csrfValueField = document.querySelector('#csrfValue')
    const csrfNameKey = csrfNameField.getAttribute('name')
    const csrfName = csrfNameField.content
    const csrfValueKey = csrfValueField.getAttribute('name')
    const csrfValue = csrfValueField.content

    return {
        [csrfNameKey]: csrfName,
        [csrfValueKey]: csrfValue
    }
}

export {
    ajax,
    get,
    post,
    del,
}
