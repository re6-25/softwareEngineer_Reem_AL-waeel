$(document).on('submit', '#add-comment-form', function(e){
    e.preventDefault();
    var comment = $(this).find('textarea[name="comment"]').val();
    var book_id = $(this).data('book-id');
    var btn = $(this).find('button[type="submit"]');
    btn.prop('disabled', true);
    $('#add-comment-msg').html('');
    $.post('add_comment.php', {comment: comment, book_id: book_id}, function(res){
        btn.prop('disabled', false);
        let data = {};
        try { data = JSON.parse(res); } catch(e) {}
        if(data.status === 'success'){
            $('#add-comment-msg').html('<div class="msg-success">'+data.msg+'</div>');
            $('#comments-list').prepend(
                `<div class="comment-card"><span class="user-name">${data.comment.user_name}:</span> <span>${data.comment.comment}</span></div>`
            );
            $('#add-comment-form textarea').val('');
        } else {
            $('#add-comment-msg').html('<div class="msg-error">'+(data.msg||'حدث خطأ!')+'</div>');
        }
    });
});
