from django.shortcuts import render
def home(request):
    return render(request, 'index.html')
def catalogo(request):
    return render(request, 'catalogo.html')
def seguridadelectronica(request):
    return render(request, 'seguridadelectronica.html')
def infraestytelecom(request):
    return render(request, 'infraestytelecom.html')
def equipespecializado(request):
    return render(request, 'equipespecializado.html')
def radiocomunicacion(request):
    return render(request, 'radiocomunicacion.html')