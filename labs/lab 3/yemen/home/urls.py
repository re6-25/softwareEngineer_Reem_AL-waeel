from django.urls import path
from . import views

app_name = 'home'

urlpatterns = [
    path('', views.home, name='home'),
    path('gallery/', views.gallery, name='gallery'),
    path('gallery/add/', views.add_image, name='add_image'),
    path('gallery/edit/<int:image_id>/', views.edit_image, name='edit_image'),
    path('gallery/delete/<int:image_id>/', views.delete_image, name='delete_image'),
]
