# Documentation in progress

# Generic Endpoints

These endpoints are present in all the controllers unless stated otherwise.
<br/>
Every endpoint can return with a 500 Status Code (Internal Server Error) but are not limited to it.

## All

### Gets all records in the database.

-   If sucessful returns the data with pagination format.

## One

### Gets one record from the database given the id.

-   Receives an ID as a parameter.
-   Gets one record from the database given the id.
-   If the record is found returns it with a 200 Status Code (OK).
-   Else returns 'Record {id} not found.' with a 404 Status Code (Not Found).

## Create

### Create one record on the database.

-   Receives a request body with the model data.
-   Creates the record on the database with the data received.
-   If sucessfuly created, returns the created record with a 200 Status Code (OK).
-   Else returns a 'Failed to save record' with a 304 Status Code (Not Modified).

## Update

### Update one record on the database given the id and request body.

-   Receives a request body with the model data and an ID parameter.
-   Check if the record is found in the database.
-   If found, updates the record and returns it with a 200 Status Code (OK).
-   Else returns 'Record {id} no found.' with a 404 Status Code (Not Found).

## Destroy

### Deletes one record record given the id

-   Receives a ID as a parameter.
-   Tries to delete the record.
-   If sucessfuly delete returns '1' with a 200 Status Code (OK).
-   Else returns '0' with a 404 Status Code (Not Found).

## Query

### Gets manufacturers' information.

-   Receives query that is passed to its' query filter.
-   If returns the found information with a 200 Status Code (OK).

# Auth Endpoints (Auth Controller)

This class handles user authentication with JWT tokens. <br/>
Does not have any generic endpoints

### Login

-   Receives a request body with email and password for authentication.

    ```
    {
      "email": "user@example.com",
      "password": "your_password"
    }
    ```

-   Upon successful login, a JSON response containing the JWT will be returned:

    ```
    {
      "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
    }
    ```

### Refresh Token:

-   Attempt to refresh given token on authentication header.
-   If the refresh is successful, a new JWT will be returned:

    ```
    {
    	"token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
    }
    ```

# User Endpoints (User Controller)

This class is handles the management for user accounts. <br/>
Does not have the generic Create function, instead it's named Register.

## Register

### Create a user record with the client role in the database

-   Receives a request body with the model data.
    ```
    {
    	'first_name': string,
    	'last_name': string,
    	'email': string,
    	'password': string,
    }
    ```
-   If sucessfuly created, returns a JWT token with a 201 Status Code (Created).
-   Else returns 'Failed to register user.' with a 400 Status Code (Bad Request).
-   Can return 'Email already registered.' with a 409 Status Code (Conflict) if the email is already registered

## Profile

### Gets information of the authenticated user.

-   Authenticates the Bearer token from the authorization header.
-   If authenticated, returns user information with a 200 Status Code (OK).
-   Else returns 'Could not authenticate user.' with a 401 Status Code (Unauthorized).

## Update

### Update the information of the authenticated user.

-   Receives the user information and Bearer token to authenticate the user.
-   If authenticaded, updates the user record and returns it with a 200 Status Code (OK).
-   If not possible to update returns 'Unable to update the user.'with a 304 Status Code (Not Modified).

# Manufacturers Endpoint

## Products

### Gets manufacturers' products paginated.

-   Receives manufacturer name as param.
-   If found manufacturer like the name returns the products as paginate with 200 Status Code (OK).

# Products Endpoint

## AllMiniatures

### Returns paginate with only the principal product information

## SomeMiniatures

### Given an Array of IDs Returns paginate with only the principal product information
