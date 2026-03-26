from django.urls import path
from . import views

urlpatterns = [
    path('notes/', views.note_list, name='note_list'),
    path('notes/new/', views.note_create, name='note_create'),
    path('notes/<int:id>/edit/', views.note_edit, name='note_edit'),
    path('notes/<int:id>/delete/', views.note_delete, name='note_delete'),
    path('register/', views.register_view, name='register'),
]