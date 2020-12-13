// app.js
document.addEventListener('click', function (event) {
    if (event.target.matches('.load-more-comment')) {
        event.preventDefault();
        event.target.disabled = true;

        var xhr = new XMLHttpRequest();
        xhr.open('GET', event.target.href);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.send();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                if(xhr.status === 200) {
                    event.target.parentNode.parentNode.remove();
                    document.querySelector(".comment-feed-container").insertAdjacentHTML('beforeend', xhr.responseText);
                } else {
                    event.target.disabled = false;
                    document.querySelector(".comment-feed-container").insertAdjacentText('beforeend', 'An error occurred during your request: ' +  xhr.status + ': ' + xhr.statusText);
                }
            }
        }
    }

    if (event.target.matches('#addTagButton')) {
        var input = document.getElementById('addTag');
        if (input.value && !document.getElementById('tag' + input.value)) {
            var tags = document.getElementById('tags');
            tags.insertAdjacentHTML('beforeend',
                `<button type="button" class="btn btn-sm btn-info mt-3 me-2" onclick="removeTag(this)">
                    <input type="hidden" name="tags[]" value="` + input.value + `">
                    ` + input.value + ` <span class="badge bg-dark">
                    <svg
                    width="2em"
                    height="2em"
                    viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                      fill-rule="evenodd"
                      d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647
                      2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646
                      5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                    </span>
                </button>`);
            input.value = '';
        }
    }
});

function removeTag(element) {
    element.remove();
}
