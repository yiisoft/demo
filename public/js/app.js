// app.js
$(document).on('click', '.load-more-comment', function (event) {
    event.preventDefault();
    $.get($(this).attr('href'), function (data) {
        $('.load-more-comment-container').hide();
        $('.comment-feed-container').append(data);
    });
});
