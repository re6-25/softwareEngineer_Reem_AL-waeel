// تبويب تسجيل دخول/تسجيل
$(function(){
  $('.tab-btn').click(function(){
    $('.tab-btn').removeClass('active');
    $(this).addClass('active');
    let tab = $(this).data('tab');
    $('form').removeClass('active').hide();
    if(tab=='login') $('#loginForm').addClass('active').fadeIn(350);
    else $('#registerForm').addClass('active').fadeIn(350);
  });
});