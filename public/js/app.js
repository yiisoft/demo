// app.js
$(document).on('click', '.load-more-comment', function(event) {
    event.preventDefault();
    var url = $(this).attr('href');
    $.get(url, function (data) {
        $('.load-more-comment-container').hide();
        $('.comment-feed-container').append(data);
    });
});
