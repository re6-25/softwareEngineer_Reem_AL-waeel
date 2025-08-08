from django import forms
from .models import YemeniImage

class YemeniImageForm(forms.ModelForm):
    class Meta:
        model = YemeniImage
        fields = ['name', 'image']
