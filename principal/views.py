from django.shortcuts import render
def home(request):
    return render(request, 'index.html')
def catalogo(request):
    return render(request, 'catalogo.html')
def videovigilancia(request):
    return render(request, 'videovigilancia.html')