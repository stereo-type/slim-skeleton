import '../css/catalog_filter.css';
import {post} from "../../../../_assets/js/ajax";
import {modal} from "../../../../_assets/js/modal";

function showLoader() {
    const loader = document.getElementById('catalog-loader');
    loader.classList.remove('d-none');
    loader.classList.add('d-block');
}
function dismissLoader() {
    const loader = document.getElementById('catalog-loader');
    loader.classList.remove('d-block');
    loader.classList.add('d-none');
}

document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('live-catalog-filter');
    filterForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Предотвращаем стандартное действие формы
        const container = event.target.closest('.live-catalog-container');
        showLoader();
        const formData = new FormData(filterForm);
        const contentType = formData.get('content_type') !== '' ? formData.get('content_type') : 'html';
        post('/categories/filter', formData).then((response) => {
            dismissLoader();
            if(response.ok) {
                const resultContainer = document.getElementById('live-catalog-table-wrap');
                if (contentType === 'html') {
                    response.text().then(data => {
                        resultContainer.innerHTML = data.toString();
                    })
                } else if (contentType === 'json') {
                    response.json().then(data => {
                        resultContainer.innerHTML = ''; // Очищаем контейнер
                        if (data.length > 0) {
                            const table = document.createElement('table');
                            const thead = document.createElement('thead');
                            const tbody = document.createElement('tbody');
                            const headRow = document.createElement('tr');
                            headRow.innerHTML = '<th>ID</th><th>Name</th><th>Description</th>';
                            thead.appendChild(headRow);
                            table.appendChild(thead);
                            data.forEach(function (item) {
                                var row = document.createElement('tr');
                                row.innerHTML = '<td>' + item.id + '</td><td>' + item.name + '</td><td>' + item.description + '</td>';
                                tbody.appendChild(row);
                            });
                            table.appendChild(tbody);
                            resultContainer.appendChild(table);
                        } else {
                            resultContainer.textContent = 'No data found';
                        }
                    })

                } else {
                    const error = `Not specified type content ${contentType}`;
                    modal(error);
                    throw new Error(error);
                }
            }
        });
    });
});