## About Shop application
> Using this small program to explain about the Sessions.

> To run the program, please read the follow:
#### Requirements
* PHP

#### Features
1. Login
2. Add product
3. Checkout
4. Logout

#### Installation
```
git clone https://github.com/Stucom-Pelai/MP0487_RA4_Session_Shop
```

#### Run 
```
run file home.php
```

#### Sequence Diagram

```mermaid
sequenceDiagram
    participant User
    participant Browser
    participant Server
    participant Session

    User->>Browser: Opens home.php
    Browser->>Server: GET request
    Server->>Session: Check if user is logged in
    alt User not logged in
        Session-->>Server: No active session
        Server->>Browser: Redirect to login.php
        Browser->>User: Display login form
        User->>Browser: Enter credentials
        Browser->>Server: POST login credentials
        Server->>Server: Validate credentials
        Server->>Session: Create session
        Session-->>Server: Session created
        Server->>Browser: Redirect to home.php
    else User is logged in
        Session-->>Server: Session found
        Server->>Browser: Display home page
    end
    Browser->>User: Show shop or checkout
    User->>Browser: Add product to cart
    Browser->>Server: Update session
    Server->>Session: Store cart data
    Session-->>Server: Cart updated
    User->>Browser: Proceed to checkout
    Browser->>Server: GET checkout.php
    Server->>Session: Retrieve cart data
    Session-->>Server: Cart data retrieved
    Server->>Browser: Display checkout page
    User->>Browser: Click logout
    Browser->>Server: GET logout request
    Server->>Session: Destroy session
    Session-->>Server: Session destroyed
    Server->>Browser: Redirect to login.php
```
   
