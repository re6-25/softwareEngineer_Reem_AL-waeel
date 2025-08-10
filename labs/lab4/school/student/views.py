from django.shortcuts import render, redirect, get_object_or_404
from .models import Student

def student_list(request):
    students = Student.objects.all()
    return render(request, 'student_list.html', {'students': students})

def student_add(request):
    if request.method == 'POST':
        first_name = request.POST['first_name']
        last_name = request.POST['last_name']
        gpa = request.POST['gpa']
        level = request.POST['level']
        Student.objects.create(first_name=first_name, last_name=last_name, gpa=gpa, level=level)
        return redirect('student:student_list')
    return render(request, 'student_add.html')

def student_edit(request, pk):
    student = get_object_or_404(Student, pk=pk)
    if request.method == 'POST':
        student.first_name = request.POST['first_name']
        student.last_name = request.POST['last_name']
        student.gpa = request.POST['gpa']
        student.level = request.POST['level']
        student.save()
        return redirect('student_list')
    return render(request, 'student_edit.html', {'student': student})
