//exemplo onde a saída excede o limite de 1MB

#include<stdio.h>

int main(){
	int n = 10000;
	int i,j;
	for (j=0 ; j<n ; j++){
		for (i=0 ; i<n ; i++){
			if (!(i%100))
				printf("\n");
			printf("%i",i%10);
		}
	}
	return 0;
}