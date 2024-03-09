import '../css/catalog_filter.css';
import {max} from "@popperjs/core/lib/utils/math";
import {updateTable} from "./catalog_table";


document.addEventListener('DOMContentLoaded', function () {
    const wrap = document.querySelector('.--live-catalog-paginbar')
    const catalogContainer = wrap.closest('.--live-catalog-container');
    const filter = catalogContainer.querySelector('#--live-catalog-filter');
    const perpageInput = filter.querySelector('input[name="page"]');
    const id = catalogContainer.getAttribute('id');

    wrap.addEventListener('click', function (evt) {
        evt.preventDefault();
        try {
            const target = evt.target;
            if (target instanceof Element) {
                let newPage = null;
                if (target.tagName.toLowerCase() === 'a' && target.classList.contains('page-link')) {
                    const li = target.closest('li');
                    if (!li.classList.contains('active')) {
                        const active = wrap.querySelector('.page-item.active');
                        const currentPage = +(active.querySelector('a.page-link').innerText);
                        if (!li.classList.contains('prev') && !li.classList.contains('next')) {
                            active.classList.remove('active');
                            target.closest('li').classList.add('active');
                            newPage = +(target.innerText);
                        } else {
                            if (li.classList.contains('prev')) {
                                newPage = currentPage - 1;
                            } else if (li.classList.contains('next')) {
                                newPage = currentPage + 1;
                            }
                        }
                    }
                } else {
                    console.error('not a link', target);
                }
                if (newPage !== null) {
                    perpageInput.value = max(newPage, 0);
                    updateTable(id);
                }
            } else {
                console.error('not element');
            }
        } catch (_) {
            console.error(_)
        }

    })

});
