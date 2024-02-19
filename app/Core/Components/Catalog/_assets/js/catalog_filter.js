import {post} from "../../../../_assets/js/ajax";

document.addEventListener('DOMContentLoaded', function () {
    var filterForm = document.getElementById('filterForm');
    filterForm.addEventListener('submit', function (event) {
        event.preventDefault(); // Предотвращаем стандартное действие формы
        var formData = new FormData(filterForm);
        // Отправляем AJAX запрос на сервер для фильтрации данных
        post('/categories/filter', formData, null, true).then((response) => {
            response.json().then(data => {
                var resultContainer = document.getElementById('resultContainer');
                resultContainer.innerHTML = ''; // Очищаем контейнер
                if (data.length > 0) {
                    var table = document.createElement('table');
                    var thead = document.createElement('thead');
                    var tbody = document.createElement('tbody');
                    var headRow = document.createElement('tr');
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

        });
    });
});