// app.js
document.addEventListener('click', event => {
    if (event.target.matches('.load-more-comment')) {
        event.preventDefault();
        event.target.disabled = true;

        const xhr = new XMLHttpRequest();
        xhr.open('GET', event.target.href);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send();
        xhr.onreadystatechange = () => {
            if (xhr.readyState === 4) {
                if (xhr.status === 200) {
                    event.target.parentNode.parentNode.remove();
                    document.querySelector(".comment-feed-container").insertAdjacentHTML('beforeend', xhr.responseText);
                } else {
                    event.target.disabled = false;
                    document.querySelector(".comment-feed-container").insertAdjacentText('beforeend', `An error occurred during your request: ${xhr.status}: ${xhr.statusText}`);
                }
            }
        };
    }

    if (event.target.matches('#addTagButton')) {
        const input = document.getElementById('addTag');
        if (input.value && !document.getElementById(`tag${input.value}`)) {
            const tags = document.getElementById('tags');
            tags.insertAdjacentHTML('beforeend',
                `<button type="button" class="btn btn-sm btn-info mt-3 me-2 remove-tag">
                    <input type="hidden" name="tags[]" value="${input.value}">
                    ${input.value}<span class="btn-close ms-1"></span>
                </button>`);
            input.value = '';
        }
    }

    if (event.target.matches('.remove-tag')) {
        event.target.remove();
    }
});
