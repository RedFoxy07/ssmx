from django.shortcuts import render
def home(request):
    return render(request, 'index.html')
def catalogo(request):
    return render(request, 'catalogo.html')