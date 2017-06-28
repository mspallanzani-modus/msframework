## Synopsis

This is a very simple MVC application developed in PHP with the use of ZendFramework modules (for more information about which ones are used, please refer to the composer.json file). 

It consists of a set of four different actions, accessible through an API, that implements the CRUD functions on a User entity (email, first name, last name and password).

My application **is missing of an encryption system for sensitive data (i.e. password field on User entity)**. This could be the object of a further development.



## Installation

### Configuring the VM through vagrant

Use vagrant to configure and run this web application on a virtual box. Before proceeding, make sure your SSH is configured to work with Github (https://help.github.com/articles/generating-an-ssh-key/Locally). 

- download this repository;

- Update your /etc/hosts file so that the application runs under the domain testbox.dev:
```
echo 192.168.59.76   testbox.dev www.testbox.dev | sudo tee -a /etc/hosts
```

- go to your local installation folder and run the following command (for more information on how to use and install vagrant on your local machine, please refer to the official online documentation):
```
vagrant up
```

### Configuring all vendors through composer

- Run composer install to download all project dependencies (for more information on how to use and install composer on your local machine, please refer to the official online documentation):
```
composer install
```

### Creating the database

By default, the used Vagrant image creates a database 'my_app'. To create the required tables, do the following actions:

- Connect to your vagrant machine with the command 
```
vagrant ssh 
```
- Run the following mysql command:
```
mysql my_app < /vagrant/db/db.sql
```

The database scheme can be found in the project in the following file:
```
db/db.sql
```

Here is the sql creation code for the 'user' table:
```sql
CREATE TABLE `user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) NOT NULL,
  `password` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

This database creation action could be probably automated through Vagrant, but I didn't do that because I didn't want to modify the give Vagrant file.  


### Checking API

once all the command have been successfully executed, you should be able to go http://www.testbox.dev/user and see a JSON response.


## How to use the application

### The actions

The available HTTP actions are the following:

- CREATE action: create a new user;
- READ action: get all information about a given user by id;
- UPDATE action: update an existing user by id;
- DELETE action: delete an existing user by id;

All the above actions return a JSON response. The general response structure is: 

```json
  {
    "status": "success|error",
    "code": "1|-1",
    "message": "",
    "data": {
        
    }
  }
  ```
  
#### CREATE action

This action is used to create a new user. To do that, you have to send a POST request to the action url with all required POST parameters. See here below for more information:

- HTTP METHOD: POST
- URL: http://www.testbox.dev/user
- RESPONSE TYPE: JSON
- REQUIRED POST PARAMETERS: 
  - email (string);
  - firstname (string);
  - lastname (string);
  - password (string);
- SUCCESSFUL RESPONSE EXAMPLE: 
  ```json
  {
    "status": "success",
    "code": "1",
    "message": "",
    "data": {
        "id": "1",
        "email": "marco@testdev.box",
        "firstname": "Marco",
        "lastname": "Spallanzani"
    }
  }
  ```
- ERROR RESPONSE EXAMPLE: 
  ```json
  {
    "status": "error",
    "code": "-1",
    "message": "Missing 'password' parameter",
    "data": []
  }
  ```

#### READ action

This action is used to read an existing user. To do that, you have to send a GET request to the action url with all required GET parameters. See here below for more information:

- HTTP METHOD: GET
- URL: http://www.testbox.dev/user?id={id}
- RESPONSE TYPE: JSON
- REQUIRED GET PARAMETERS: 
  - id (int);
- SUCCESSFUL RESPONSE EXAMPLE: 
  ```json
  {
    "status": "success",
    "code": "1",
    "message": "",
    "data": {
        "id": "1",
        "email": "marco@testdev.box",
        "firstname": "Marco",
        "lastname": "Spallanzani"
    }
  }
  ```
- ERROR RESPONSE EXAMPLE: 
  ```json
  {
    "status": "error",
    "code": "-1",
    "message": "Missing 'id' parameter",
    "data": []
  }
  ```

#### UPDATE action

This action is used to update an existing. To do that, you have to send a PUT request to the action url with all desired parameters. See here below for more information:

- HTTP METHOD: PUT
- URL: http://www.testbox.dev/user?id={id}
- RESPONSE TYPE: JSON
- OPTIONAL PUT PARAMETERS: 
  - email (string);
  - firstname (string);
  - lastname (string);
  - password (string);
- SUCCESSFUL RESPONSE EXAMPLE: 
  ```json
  {
    "status": "success",
    "code": "1",
    "message": "",
    "data": {
        "id": "1",
        "email": "marco@testdev.box",
        "firstname": "Marco",
        "lastname": "Spallanzani"
    }
  }
  ```
- ERROR RESPONSE EXAMPLE: 
  ```json
  {
    "status": "error",
    "code": "-1",
    "message": "Missing 'email' parameter",
    "data": []
  }
  ```

#### DELETE action

This action is used to delete an existing user. To do that, you have to send a DELETE request to the action url with all required parameters. See here below for more information:

- HTTP METHOD: DELETE
- URL: http://www.testbox.dev/user?id={id}
- RESPONSE TYPE: JSON
- REQUIRED GET PARAMETERS: 
  - id (int);
- SUCCESSFUL RESPONSE EXAMPLE: 
  ```json
  {
    "status": "success",
    "code": "1",
    "message": "Entity deleted",
    "data": []
  }
  ```
- ERROR RESPONSE EXAMPLE: 
  ```json
  {
    "status": "error",
    "code": "-1",
    "message": "Missing 'id' parameter",
    "data": []
  }
  ```
  
## Run the Unit Tests

Once correctly installed, you can run the phpunit tests with the following commands:

```
vagrant ssh
cd /vagrant
vendor/bin/phpunit
```
