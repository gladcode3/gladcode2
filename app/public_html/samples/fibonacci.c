//calcula o enésimo termo da sequencia de fibonacci

#include<stdio.h>

int fib(int n){
    if (n == 1)
        return 0;
    if (n == 2)
        return 1;
    return fib(n-1) + fib(n-2);
}

int main(){
    int v;
    scanf("%i",&v);
    printf("%i\n",fib(v));
	return 0;
}