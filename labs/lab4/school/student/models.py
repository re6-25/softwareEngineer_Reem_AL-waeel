from django.db import models

class Student(models.Model):
    first_name = models.CharField(max_length=10)
    last_name = models.CharField(max_length=10)
    gpa = models.DecimalField(max_digits=5, decimal_places=2)
    level = models.IntegerField()

    def __str__(self):
        return f"{self.first_name} {self.last_name}"
