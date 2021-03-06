dnl $Id$
dnl config.m4 for extension eigen

dnl Comments in this file start with the string 'dnl'.
dnl Remove where necessary. This file will not work
dnl without editing.

dnl If your extension references something external, use with:

PHP_ARG_WITH(eigen, for eigen support,
dnl Make sure that the comment is aligned:
[  --with-eigen             Include eigen support])

dnl Otherwise use enable:

PHP_ARG_ENABLE(eigen, whether to enable eigen support,
dnl Make sure that the comment is aligned:
[  --enable-eigen           Enable eigen support])

if test "$PHP_EIGEN" != "no"; then
  dnl Write more examples of tests here...

  dnl # --with-eigen -> check with-path
  dnl SEARCH_PATH="/usr/local /usr"     # you might want to change this
  dnl SEARCH_FOR="/include/eigen.h"  # you most likely want to change this
  dnl if test -r $PHP_EIGEN/$SEARCH_FOR; then # path given as parameter
  dnl   EIGEN_DIR=$PHP_EIGEN
  dnl else # search default path list
  dnl   AC_MSG_CHECKING([for eigen files in default path])
  dnl   for i in $SEARCH_PATH ; do
  dnl     if test -r $i/$SEARCH_FOR; then
  dnl       EIGEN_DIR=$i
  dnl       AC_MSG_RESULT(found in $i)
  dnl     fi
  dnl   done
  dnl fi
  dnl
  dnl if test -z "$EIGEN_DIR"; then
  dnl   AC_MSG_RESULT([not found])
  dnl   AC_MSG_ERROR([Please reinstall the eigen distribution])
  dnl fi

  dnl # --with-eigen -> add include path
  dnl PHP_ADD_INCLUDE($EIGEN_DIR/include)

  dnl # --with-eigen -> check for lib and symbol presence
  dnl LIBNAME=eigen # you may want to change this
  dnl LIBSYMBOL=eigen # you most likely want to change this 

  dnl PHP_CHECK_LIBRARY($LIBNAME,$LIBSYMBOL,
  dnl [
  dnl   PHP_ADD_LIBRARY_WITH_PATH($LIBNAME, $EIGEN_DIR/lib, EIGEN_SHARED_LIBADD)
  dnl   AC_DEFINE(HAVE_EIGENLIB,1,[ ])
  dnl ],[
  dnl   AC_MSG_ERROR([wrong eigen lib version or lib not found])
  dnl ],[
  dnl   -L$EIGEN_DIR/lib -lm
  dnl ])
  dnl
  dnl PHP_SUBST(EIGEN_SHARED_LIBADD)
	AC_SEARCH_LIBS(sin,[m])
	AC_SEARCH_LIBS(cblasdgemm,[gslcblas])
	AC_SEARCH_LIBS( gsl_eigen_nonsymmv,[gsl])
	AC_SEARCH_LIBS( php_printf,[php5])
	AC_CHECK_LIB(m,main)
	AC_CHECK_LIB([gslcblas], [cblas_dgemm])
	AC_CHECK_LIB([gsl], [gsl_eigen_nonsymmv])
	AC_CHECK_LIB([php5], [php_printf])
	PHP_CHECK_LIBRARY(m,sin, [ LDFLAGS=$LIBS])
	
  PHP_NEW_EXTENSION(eigen, eigen.c lib/eigen_lib.c, $ext_shared)
fi

