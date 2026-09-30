// Fetch filtered results from PHP; PHP still checks the logged-in user.
const filters = document.querySelector('#filters');

if (filters) {
    const list = document.querySelector('#recipe-list');
    const count = document.querySelector('#result-count');
    const empty = document.querySelector('#empty-state');
    let timer;
    let controller;
    let requestNumber = 0;

    async function updateRecipes() {
        clearTimeout(timer);
        if (controller) controller.abort();
        controller = new AbortController();
        const currentRequest = ++requestNumber;
        const url = new URL(filters.action);
        url.search = new URLSearchParams(new FormData(filters)).toString();
        list.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(url, { signal: controller.signal });
            if (response.redirected) {
                window.location.assign(response.url);
                return;
            }
            if (!response.ok) throw new Error('Filter request failed');
            const page = new DOMParser().parseFromString(await response.text(), 'text/html');
            const newList = page.querySelector('#recipe-list');
            if (!newList) throw new Error('Recipe list missing');
            if (currentRequest !== requestNumber) return;
            list.replaceChildren(...newList.childNodes);
            count.textContent = page.querySelector('#result-count').textContent;
            empty.hidden = page.querySelector('#empty-state').hidden;
            history.replaceState(null, '', url);
        } catch (error) {
            if (error.name !== 'AbortError' && currentRequest === requestNumber) {
                // A normal page request remains available if live filtering fails.
                window.location.assign(url);
            }
        } finally {
            if (currentRequest === requestNumber) list.removeAttribute('aria-busy');
        }
    }

    filters.addEventListener('submit', event => {
        event.preventDefault();
        updateRecipes();
    });

    filters.addEventListener('input', event => {
        if (event.target.type === 'search') {
            clearTimeout(timer);
            if (controller) controller.abort();
            requestNumber++;
            timer = setTimeout(updateRecipes, 300);
        }
    });

    filters.querySelectorAll('select').forEach(select => {
        select.addEventListener('change', updateRecipes);
    });
}
