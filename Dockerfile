version: "3.8"

services:
  db:
    image: mysql:8.0
    container_name: mysql_db
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: test
    ports:
      - "3306:3306"
    volumes:
      - db_data:/var/lib/mysql
      - ./users.sql:/docker-entrypoint-initdb.d/users.sql
      - ./product.sql:/docker-entrypoint-initdb.d/product.sql

  web:
    build: .
    container_name: php_web_app
    restart: always
    ports:
      - "8080:80"
    depends_on:
      - db

volumes:
  db_data:

