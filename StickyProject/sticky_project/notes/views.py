from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.decorators import login_required
from django.contrib.auth.forms import UserCreationForm
from django.contrib.auth import login
from .models import Note
from .forms import NoteForm
from django.db.models import Q  # for OR queries

@login_required
def note_list(request):
    query = request.GET.get('q')
    if query:
        notes = Note.objects.filter(
            owner=request.user
        ).filter(
            Q(title__icontains=query) | Q(content__icontains=query)
        )
    else:
        notes = Note.objects.filter(owner=request.user)
    return render(request, 'note_list.html', {'notes': notes, 'query': query})

def register_view(request):
    from django.contrib.auth.forms import UserCreationForm
    from django.contrib.auth import login

    if request.method == 'POST':
        form = UserCreationForm(request.POST)

        if form.is_valid():
            user = form.save()
            login(request, user)
            return redirect('note_list')
    else:
        form = UserCreationForm()

    return render(request, 'register.html', {'form': form})


@login_required
def note_create(request):
    if request.method == 'POST':
        form = NoteForm(request.POST)

        if form.is_valid():
            note = form.save(commit=False)
            note.owner = request.user  # VERY IMPORTANT
            note.save()
            return redirect('note_list')
    else:
        form = NoteForm()

    return render(request, 'note_form.html', {'form': form})


@login_required
def note_edit(request, id):
    note = get_object_or_404(Note, id=id, owner=request.user)

    if request.method == 'POST':
        form = NoteForm(request.POST, instance=note)

        if form.is_valid():
            form.save()
            return redirect('note_list')
    else:
        form = NoteForm(instance=note)

    return render(request, 'note_form.html', {'form': form})


@login_required
def note_delete(request, id):
    note = get_object_or_404(Note, id=id, owner=request.user)

    if request.method == 'POST':
        note.delete()
        return redirect('note_list')

    return render(request, 'note_confirm_delete.html', {'note': note})