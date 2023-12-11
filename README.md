# GladCode 2 legacy

This repository serves as a reference for the GladCode 3 project. It contains the legacy codebase of GladCode 2, which has been copied from the original repository.

Feel free to explore the code and use it as a resource for understanding the evolution of the GladCode project.

## Setup

You must place some files that are not included in the repository in order to run the project.

### /.env

```
MYSQL_DATABASE=<database name>
MYSQL_ROOT_PASSWORD=<database password>
```

You can choose any name and password you want, as they will be used to create the database.

### build/mysql/database.sql

This file contains the SQL code to create the database tables.

### app/public_html/config.json

```json
{
    "mysql": {
        "host": "mysql",
        "port": 3306,
        "user": "root",
        "password": "<database password>",
        "database": "<database name>"
    }
}
```

### PHPMailer

You can get it from a zipped file on the gladcode server

```bash
wget https://gladcode.dev/phpmailer.zip
unzip phpmailer.zip
```

### Spritesheets

You also need to get the spritesheets needed for showing the gladiators. You can get them from the gladcode server as well.

```bash
wget https://gladcode.dev/spritesheet.zip
unzip spritesheet.zip
```

### Database

Before running the project, you must create the database. To do that, just place the `database.sql` file in the `build/mysql` directory. When you run the project, the database will be created automatically.

You can get the `database.sql` file from the gladcode server.

```bash
wget https://gladcode.dev/database.sql
```

## Running

To run the project, simply run `docker-compose up` in the root directory of the project. The website will be available at `localhost`.