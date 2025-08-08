from django.shortcuts import render, redirect, get_object_or_404
from home.templatetags.yemen_word import yemen_word
from .forms import YemeniImageForm
from .models import YemeniImage

def home(request):
    username = request.session.get('username', 'زائر')
    input_text = request.GET.get('text', '')
    return render(request, 'index.html', {
        'username': username,
        'input_text': input_text,
    })


def gallery(request):
    images = YemeniImage.objects.all()
    return render(request, 'home/gallery.html', {'images': images})

def add_image(request):
    if request.method == 'POST':
        form = YemeniImageForm(request.POST, request.FILES)
        if form.is_valid():
            form.save()
            return redirect('home:gallery')
    else:
        form = YemeniImageForm()
    return render(request, 'home/add_image.html', {'form': form})

def edit_image(request, image_id):
    image = get_object_or_404(YemeniImage, pk=image_id)
    if request.method == 'POST':
        form = YemeniImageForm(request.POST, request.FILES, instance=image)
        if form.is_valid():
            form.save()
            return redirect('home:gallery')
    else:
        form = YemeniImageForm(instance=image)
    return render(request, 'home/edit_image.html', {'form': form, 'image': image})

def delete_image(request, image_id):
    image = get_object_or_404(YemeniImage, pk=image_id)
    if request.method == 'POST':
        image.delete()
        return redirect('home:gallery')
    return render(request, 'home/delete_image.html', {'image': image})
