//Lê valores N, A e B, sorteia e mostra na tela N valores aleatórios entre A e B

#include<stdio.h>
#include<stdlib.h>
#include<time.h>

int aleatorio(int inf, int sup){
    if (inf > sup){
        int aux = inf;
        inf = sup;
        sup = aux;
    }
    return rand()%(sup - inf + 1) + inf;
}

int main(){
    srand(time(NULL));
    int a, b, n, i;
    scanf("%i %i %i",&n,&a,&b);
    for (i=0 ; i<n ; i++){
        printf("%i ",aleatorio(a,b));
    }
    printf("\n");
    return 0;
}