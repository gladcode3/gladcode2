//forneça entradas usando F8

#include<stdio.h>

int main(){
	int x;
	while (scanf("%i",&x) != EOF)
		printf("V: %i\n",x);
	return 0;
}