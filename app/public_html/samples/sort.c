//ordena um vetor em ordem crescente

#include<stdio.h>

int main(){
    int t;
    scanf("%i",&t);
    int vet[t], i;
    for (i=0 ; i<t ; i++){
        scanf("%i",&vet[i]);
    }
    int troca, aux;
    do {
        troca = 0;
        for (i=0 ; i<t-1 ; i++){
            if (vet[i] > vet[i+1]){
                aux = vet[i];
                vet[i] = vet[i+1];
                vet[i+1] = aux;
                troca = 1;
            }
        }
    } while(troca);
    for (i=0 ; i<t ; i++){
        printf("%i ",vet[i]);
    }
    printf("\n");
	return 0;
}