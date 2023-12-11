/*
exemplo de uso de funções de string.
forneça na entrada F8 duas strings, separadas por enter.
O programa irá mostrar as strings, se elas são iguais ou não e quantas ocorrências da segunda existe dentro da primeira.
*/

#include<stdio.h>
#include<string.h>

int main(){
    char str[100], str2[100];
    fgets(str,100,stdin);
    fgets(str2,100,stdin);
    str[strlen(str)-1] = '\0';
    str2[strlen(str2)-1] = '\0';
    printf("S1: %s\nS2: %s\n",str,str2);
    if (!strcmp(str2, str))
        printf("eq: y\n");
    else
        printf("eq: n\n");
    char *p = strstr(str, str2);
    int cont=0;
    while (p != NULL){
        cont++;
        p = strstr(p+1, str2);
    }
    printf("oc: %i\n",cont);
    return 0;
}