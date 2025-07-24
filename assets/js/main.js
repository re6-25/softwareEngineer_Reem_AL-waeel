// الصفحة الرئيسية 
$('.main-slider').slick({
    autoplay: true,
    dots: true,
    rtl: true
});
$('.services-slider').slick({
    slidesToShow: 2,
    rtl: true,
    infinite: true,
    autoplay: true,
    autoplaySpeed: 3400,
    dots: true,
    arrows: true,
    responsive: [{
        breakpoint: 768,
        settings: { slidesToShow: 1 }
    }]
});
