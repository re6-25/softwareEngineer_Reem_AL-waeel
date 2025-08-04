from django.shortcuts import render

def home_page(request):
    input_text = ''
    converted_text = ''
    if request.method == 'POST':
        input_text = request.POST.get('text', '')
       # converted_text = yemen_word(input_text)
    return render(request, 'index.html', {
        'username': 'Remi',
        'input_text': input_text,
        'converted_text': converted_text,
    })
