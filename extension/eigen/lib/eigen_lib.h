

#include <stdio.h>
#include <stdlib.h>
#include <gsl/gsl_eigen.h>
#include <gsl/gsl_math.h>
#include <gsl/gsl_complex_math.h>

double * vect_alloc(size_t size);
void vect_free(double * vect);
int highest_eigenvector(int taille, double * data, double * output);
double * matrix_alloc(size_t size);
void matrix_free(double * matrix);
