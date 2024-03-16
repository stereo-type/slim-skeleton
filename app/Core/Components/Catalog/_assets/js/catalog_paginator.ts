import '../css/catalog_filter.css';
import { max } from "@popperjs/core/lib/utils/math";
import { updateTable } from "./catalog_table";

document.addEventListener('DOMContentLoaded', () => {
    const wrap = document.querySelector<HTMLDivElement>('.--live-catalog-paginbar');
    if (!wrap) return;

    const catalogContainer = wrap.closest<HTMLDivElement>('.--live-catalog-container');
    if (!catalogContainer) return;

    const filter = catalogContainer.querySelector<HTMLFormElement>('#--live-catalog-filter');
    if (!filter) return;

    const perpageInput = filter.querySelector<HTMLInputElement>('input[name="page"]');
    if (!perpageInput) return;

    const id = catalogContainer.getAttribute('id');
    if (!id) return;

    wrap.addEventListener('click', (evt) => {
        evt.preventDefault();
        try {
            const target = evt.target as HTMLElement;
            if (!target) return;

            let newPage: number | null = null;
            if (target.tagName.toLowerCase() === 'a' && target.classList.contains('page-link')) {
                const li = target.closest<HTMLLIElement>('li');
                if (!li || li.classList.contains('active')) return;

                const active = wrap.querySelector<HTMLLIElement>('.page-item.active');
                if (!active) return;

                const currentPage = +(active.querySelector<HTMLAnchorElement>('a.page-link')?.innerText || '');
                if (!isNaN(currentPage)) {
                    if (!li.classList.contains('prev') && !li.classList.contains('next')) {
                        active.classList.remove('active');
                        li.classList.add('active');
                        newPage = +(target.innerText);
                    } else {
                        if (li.classList.contains('prev')) {
                            newPage = currentPage - 1;
                        } else if (li.classList.contains('next')) {
                            newPage = currentPage + 1;
                        }
                    }
                }
            }

            if (newPage !== null) {
                perpageInput.value = String(max(newPage, 0));
                updateTable(id);
            }
        } catch (error) {
            console.error(error);
        }
    });
});
