from django.contrib import admin
from django.urls import path, include
from django.http import HttpResponse

def landing_page(request):
    return HttpResponse("أهلًا بك في مكتبة Silent_Inspiration!")

urlpatterns = [
    path('admin/', admin.site.urls),
    path('home/', include('home.urls')),
    path('', include('accounts.urls')),
]
