# YourListOnline
YourListOnline is a website that allows you to keep track of all the tasks you need to complete for your streaming or normal day-to-day activities.

Getting Started
To get started with this code, you will need to create a SQL database and use the following code to build the tables:

```sql
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    api_key VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    signup_date TIMESTAMP DEFAULT NOW(),
    last_login TIMESTAMP DEFAULT NOW()
);

CREATE TABLE todos (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id),
    objective TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT NOW(),
    updated_at TIMESTAMP DEFAULT NOW(),
    completed BOOLEAN DEFAULT FALSE
);
```

The todos table has the following columns:

id: a unique identifier for the todo item
user_id: the id of the user who owns the todo item
objective: the text of the todo item
created_at: the timestamp when the todo item was created
updated_at: the timestamp when the todo item was last updated
completed: a boolean flag indicating whether the todo item has been completed or not
The users table has the following columns:

id: a unique identifier for the user
username: the username of the user
password: the password of the user
api_key: the API key of the user
is_admin: a boolean flag indicating whether the user is an admin or not
signup_date: the timestamp when the user signed up
last_login: the timestamp when the user last logged in