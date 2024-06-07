# Tasks Project

- [Install](#install)

Prerequisites:
- Docker
## Install

1. Clone the repository:
```
git clone https://github.com/DedKO123/tasks-project.git
```
2. Navigate into the project directory:
```
cd tasks-project
```
3. Copy the .env.example file to .env:
```
cp .env.example .env
```
4. Set up your database credentials in the .env file.
5. Build the Docker containers:
```
./vendor/bin/sail up -d
```
6. Install the composer dependencies:
```
 composer install
```

7. Generate the application key:
```
./vendor/bin/sail artisan key:generate
```
8. Run the database migrations:
```
./vendor/bin/sail artisan migrate --seed
```
9. Visit the application in your browser:
```
http://localhost
```
