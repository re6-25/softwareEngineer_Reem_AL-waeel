from django.shortcuts import render
from home.templatetags.yemen_word import yemen_word

def home(request):
    username = request.session.get('username', 'زائر')
    input_text = ''
    converted_text = ''
    if request.method == 'POST':
        input_text = request.POST.get('text', '')
        converted_text = yemen_word(input_text)
    return render(request, 'index.html', {
    'username': username,
    'input_text': input_text,
    'converted_text': converted_text,
})
