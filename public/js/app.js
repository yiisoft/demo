// app.js
$(document).on('click', '.load-more-comment', function (event) {
    event.preventDefault();
    $.get($(this).attr('href'), function (data) {
        $('.load-more-comment-container').hide();
        $('.comment-feed-container').append(data);
    });
});

function addTag() {
    var input = document.getElementById('addTag');
    if (input.value && !document.getElementById('tag' + input.value)) {
        var tags = document.getElementById('tags');
        tags.insertAdjacentHTML('beforeend',
            `<span class="badge badge-info mr-2" id='tag` + input.value + `'>
                            <input type="hidden" name="tags[]" value="` + input.value + `">
                            <span>` + input.value + `</span>
                            <svg 
                            width="2em" 
                            height="2em" 
                            viewBox="0 0 16 16" class="bi bi-x" fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                            onclick="removeTag(this)"
                            >
                              <path 
                              fill-rule="evenodd" 
                              d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 
                              2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 
                              5.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </span>`);
        input.value = '';
    }
}

function removeTag(element) {
    element.parentNode.parentNode.removeChild(element.parentNode);
}

$(document).ready(function(){
    document.getElementById("addTagButton").addEventListener('click', addTag);
});
