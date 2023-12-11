struct stringArgs {
	char toStr[100];
	char toLongStr[1000];
	int toInt;
	float toFloat;
};

struct stringFunc {
	char call[300];
	int nargs;
	struct stringArgs *arg;
};

struct stringFunc *decodeFuncArg(char *client_message){
	client_message[299] = '\0';
	struct stringFunc *func = (struct stringFunc*) malloc(sizeof(struct stringFunc));
	func->arg = NULL;
	strcpy(func->call, client_message);
	char *p = strstr(func->call, " ");
	int i=0;
	if (p){
		*p = '\0';
		char temp[500];
		strcpy(temp, p+1);
		do{
			if (func->arg)
				func->arg = (struct stringArgs*) realloc(func->arg, sizeof(struct stringArgs) * (i+1));
			else
				func->arg = (struct stringArgs*) malloc(sizeof(struct stringArgs) * (i+1));
			
			strncpy(func->arg[i].toStr, temp, 100);
			func->arg[i].toStr[99] = '\0';
			if (i == 0)
				strcpy(func->arg[i].toLongStr, temp);
			p = strstr(func->arg[i].toStr, " ");
			if (p){
				*p = '\0';
				strcpy(temp, p+1);
			}
			func->arg[i].toInt = atoi(func->arg[i].toStr);
			func->arg[i].toFloat = atof(func->arg[i].toStr);
			i++;
		}while (p);
	}
	func->nargs = i;
	return func;
}