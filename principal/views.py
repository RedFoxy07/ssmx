from django.shortcuts import render
def home(request):
    return render(request, 'index.html')
def catalogo(request):
    return render(request, 'catalogo.html')
def videovigilancia(request):
    return render(request, 'videovigilancia.html')
def audioyvideo(request):
    return render(request, 'audioyvideo.html')
def automatizacion(request):
    return render(request, 'automatizacion.html')
def controldeacceso(request):
    return render(request, 'controlacceso.html')
def radiocomunicacion(request):
    return render(request, 'radiocomunicacion.html')
def redeseit(request):
    return render(request, 'redeseit.html')