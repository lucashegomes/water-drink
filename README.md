# Water Drink REST API

Simple REST API build

## Quick Start
Run `./src/config/database.sql` to create database and tables

Run `php -S localhost:8080` to start the server

## API Endpoints

Create a new user
```http 
POST /users/
```
__Parameters__
| __Parameter__ | __Required / Optional | __Description__                                                           | __Type__ |
| ------------------ | --------------------- | ------------------------------------------------------------------------- | -------- |
| email              | REQUIRED              | The user email that needs to be __unique__.                                   | String   |
| name               | REQUIRED              | The full user name.                                                       | String   |
| password           | REQUIRED              | The user password.                                                        | String   |


Login user
```http 
POST /login/
```
__Parameters__
| __Parameter__ | __Required / Optional | __Description__                                                           | __Type__ |
| ------------------ | --------------------- | ------------------------------------------------------------------------- | -------- |
| email              | REQUIRED              | The user email.                                   | String   |
| password           | REQUIRED              | The user password.                                                        | String   |

Login user
```http 
POST /login/
```
__Parameters__
| __Parameter__ | __Required / Optional | __Description__                                                           | __Type__ |
| ------------------ | --------------------- | ------------------------------------------------------------------------- | -------- |
| email              | REQUIRED              | The user email.                                   | String   |
| password           | REQUIRED              | The user password.                                                        | String   |


Get data from an user
```http 
GET /users/:iduser
```
__Parameters__
| __Parameter__ | __Required / Optional | __Description__                                                           | __Type__ |
| ------------------ | --------------------- | ------------------------------------------------------------------------- | -------- |
| token              | REQUIRED              | User token generated after the login. <br>__OBS:__ Put on request `HEADER`                                   | String   |


Get data from all users
```http 
GET /users/
```
__Parameters__
| __Parameter__ | __Required / Optional | __Description__                                                           | __Type__ |
| ------------------ | --------------------- | ------------------------------------------------------------------------- | -------- |
| token              | REQUIRED              | User token generated after the login. <br>__OBS:__ Put on request `HEADER`                                   | String   |


Change data from an user
```http 
PUT /users/:iduser
```
__Parameters__
| __Parameter__ | __Required / Optional | __Description__                                                           | __Type__ |
| ------------------ | --------------------- | ------------------------------------------------------------------------- | -------- |
| token              | REQUIRED              | User token generated after the login. <br>__OBS:__ Put on request `HEADER`                                   | String   |
| email              | OPTIONAL              | The user email that needs to be __unique__.                                   | String   |
| name               | OPTIONAL              | The full user name.                                                       | String   |
| password           | OPTIONAL              | The user password.                                                        | String   |



Delete an user
```http 
DELETE /users/:iduser
```
__Parameters__
| __Parameter__ | __Required / Optional | __Description__                                                           | __Type__ |
| ------------------ | --------------------- | ------------------------------------------------------------------------- | -------- |
| token              | REQUIRED              | User token generated after the login. <br>__OBS:__ Put on request `HEADER`                                   | String   |


Register water drinked in miligram
```http 
POST /users/:iduser/drink
```
__Parameters__
| __Parameter__ | __Required / Optional | __Description__                                                           | __Type__ |
| ------------------ | --------------------- | ------------------------------------------------------------------------- | -------- |
| token              | REQUIRED              | User token generated after the login. <br>__OBS:__ Put on request `HEADER`                                   | String   |
| drink_ml              | OPTIONAL              | Quantity of water drinked (in miligrams)`                                   | Integer   |


Ranking of users on current date
```http 
GET /ranking/
```
__Parameters__
| __Parameter__ | __Required / Optional | __Description__                                                           | __Type__ |
| ------------------ | --------------------- | ------------------------------------------------------------------------- | -------- |
| token              | REQUIRED              | User token generated after the login. <br>__OBS:__ Put on request `HEADER`                                   | String   |


Drink's history of an user
```http 
GET /history/:iduser
```
__Parameters__
| __Parameter__ | __Required / Optional | __Description__                                                           | __Type__ |
| ------------------ | --------------------- | ------------------------------------------------------------------------- | -------- |
| token              | REQUIRED              | User token generated after the login. <br>__OBS:__ Put on request `HEADER`                                   | String   |