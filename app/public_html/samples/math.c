//exemplo de uso da biblioteca math.h

#include<stdio.h>
#include<math.h>

int main(){
    float x = sqrt(24);
    float y = pow(2,x);
    float z = log10(y);
    printf("%f\n%f\n%f",x,y,z);
    return 0;
}