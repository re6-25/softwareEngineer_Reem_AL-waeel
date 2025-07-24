<?php
session_start();
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>عن مكتبة الإلهام الساكن</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" />
    <style>
        body { background: #f8f9fa; font-family: 'Cairo', Arial, sans-serif; margin:0; }
        .main-wrapper { display: flex; min-height: 100vh; }
        .content-about {
            max-width: 670px; margin: 50px auto 25px auto;
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 8px 32px #bdb1e623;
            padding: 44px 34px 30px 34px;
            text-align: center;
            flex: 1;
        }
        .about-title {
            color: #7157c7;
            font-size: 2.2em;
            font-weight: bold;
            margin-bottom: 18px;
            letter-spacing: 1.5px;
        }
        .about-desc {
            font-size: 1.21em;
            color: #393e46;
            line-height: 1.85em;
            margin-bottom: 26px;
        }
        .about-features {
            display: flex;
            flex-wrap: wrap;
            gap: 23px;
            justify-content: center;
            margin-bottom: 22px;
        }
        .about-feature {
            background: #f6f3fe;
            color: #7157c7;
            border-radius: 13px;
            padding: 20px 18px 13px 18px;
            min-width: 150px;
            box-shadow: 0 3px 16px #b7a5e71c;
            font-size: 1.01em;
            font-weight: bold;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
        }
        .about-feature i { font-size: 1.7em; margin-bottom: 4px; color: #f9c846;}
        .about-contact {
            background: #f4f0fb;
            color: #393e46;
            border-radius: 12px;
            padding: 16px 10px;
            margin: 12px 0 5px 0;
            font-size: 1.02em;
            display: inline-block;
            box-shadow: 0 2px 12px #e9e0f233;
        }
        @media (max-width:700px) {
            .content-about { padding: 21px 6vw 12px 6vw; margin: 20px 0 14px 0;}
            .about-title { font-size: 1.3em; }
        }
    </style>
</head>
<body>
    
        <?php include 'sidebar.php'; ?>
    <div class="main-wrapper">
        <div class="content-about">
            <div class="about-title">
                <i class="fa fa-book-reader"></i>  مكتبة الإلهام الساكن
            </div>
            <div class="about-desc">
                مكتبة الإلهام الساكن هي وجهتك الأولى لعالم الكتب والثقافة.<br>
                نسعى لتوفير أفضل الكتب الورقية والإلكترونية لجميع الأعمار والاهتمامات.<br>
                نؤمن بأن القراءة تصنع الفرق، لذا نوفر لك تشكيلة ضخمة من العناوين العربية والعالمية، مع خدمات التوصيل السريع، والإهداءات المميزة، واشتراك شهري للقراءة بلا حدود.
            </div>
            <div class="about-features">
                <div class="about-feature"><i class="fa fa-thumbs-up"></i> تنوع العناوين</div>
                <div class="about-feature"><i class="fa fa-truck"></i> توصيل سريع</div>
                <div class="about-feature"><i class="fa fa-gift"></i> إهداءات فريدة</div>
                <div class="about-feature"><i class="fa fa-star"></i> جودة مضمونة</div>
                <div class="about-feature"><i class="fa fa-users"></i> تناسب كل الفئات</div>
            </div>
            <div class="about-desc">
                هدفنا نشر المعرفة وتحفيز الشغف بالقراءة في مجتمعنا.<br>
                انضم إلينا لتكون جزءاً من مجتمع القراء المميزين.
            </div>
            <div class="about-contact">
               تواصل مع المهندسة ريم طاهر الوعيل لأي استفسار: <br>
                <i class="fa fa-envelope"></i> engreemalweel40@gmail.com <br>
                <i class="fa fa-phone"></i> 77*****
            </div>
        </div>
    </div>
</body>
</html>
