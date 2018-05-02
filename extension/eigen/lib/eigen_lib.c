#include <stdio.h>
#include <stdlib.h>
#include <gsl/gsl_eigen.h>
#include <gsl/gsl_math.h>
#include <gsl/gsl_complex_math.h>

#include "eigen_lib.h"

gsl_complex vector_sum(gsl_vector_complex * vect);


int normalize(gsl_vector_complex * vect)
{
	if (gsl_vector_complex_isnull(vect))
	{
		return 0;
	}
	// now we know it is not null
	gsl_complex sum=vector_sum(vect);
	if(gsl_complex_abs(sum)==0)
	{
		// can't normalize
		return 1;
	}
	return gsl_vector_complex_scale(vect, gsl_complex_inverse(sum));
	return 0;
}

gsl_complex vector_sum(gsl_vector_complex * vect)
{
	
	gsl_complex out=GSL_COMPLEX_ZERO,temp;
	int i,taille = vect->size;
	for (i=0; i< taille; i++)
	{
		temp = gsl_complex_add( gsl_vector_complex_get(vect,i), out);
		out =temp;
	}
	return out;
}

int highest_eigenvector(int taille, double * data, double * output)
{
	if (taille <= 0)
	{
		// error
		return 1;
	}
	int i,j;	
	gsl_matrix *  m = gsl_matrix_alloc ( (size_t)taille, (size_t) taille);
  for (i=0 ; i< taille; i++)
	{
		for (j=0 ; j<taille ; j++)
		{
			//printf("%f ", data[ taille * i + j]);
			gsl_matrix_set(m,i,j,data[ taille * i + j]);
		}
		//printf("\n");
	}
	gsl_vector_complex *eval = gsl_vector_complex_alloc (taille);
 	gsl_matrix_complex *evec = gsl_matrix_complex_alloc (taille, taille);

  gsl_eigen_nonsymmv_workspace * w = 
    gsl_eigen_nonsymmv_alloc (taille);
  gsl_matrix_transpose(m);
  gsl_eigen_nonsymmv (m, eval, evec, w);
	gsl_eigen_nonsymmv_sort(eval,evec, GSL_EIGEN_SORT_ABS_DESC);
  gsl_eigen_nonsymmv_free (w);

  //printf("eigenvalues ; ");
  for (i=0; i< taille; i++)
  {
  	gsl_complex value = gsl_vector_complex_get(eval, i);
/*  	printf("%g\n", GSL_REAL(value));*/
  }
  gsl_vector_complex_view highest_vector = gsl_matrix_complex_column(evec, 0);  
  normalize(&(highest_vector.vector));
  for(i = 0; i < taille ; i++)
  {
  	
  	gsl_complex z = gsl_vector_complex_get(&(highest_vector.vector), i);
  	output[i] = GSL_REAL(z);
/*  	printf("%g ",GSL_REAL(z));*/
  }

  gsl_vector_complex_free (eval);
  gsl_matrix_complex_free (evec); 
  gsl_matrix_free(m); 
	return 0;
}


double * matrix_alloc(size_t size)
{
	if(size >0)
	{
		return (double*) malloc(size*size * sizeof(double));
	}
	else
	{
		return NULL;
	}
}
void matrix_free(double* matrix)
{
	free(matrix);
	return ;
}
double * vect_alloc(size_t size)
{
	if(size >0)
	{
		return (double*) malloc(size * sizeof(double));
	}
	else
	{
		return NULL;
	}
}
void vect_free(double* vect)
{
	free(vect);
	return ;
}
