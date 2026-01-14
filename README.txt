# My School Project (SMS)

hey this is my group project for the cse3101 database class. its a school management system that i made with php and mysql. i tried to use MVC like the professor asked but its kinda hard lol.

## How to run it
stuff you need:
- xampp (i used the newest one)
- google chrome

1. put the folder inside `htdocs` in xampp
2. turn on apache and mysql on the control panel
3. go to `http://localhost/phpmyadmin` and make a database called `School_Management` (make sure its spelled exactly like that!)
4. click import and choose the `database.sql` file i included
5. hit go

then just go to `http://localhost/cse3101-g17-proj/` in your browser.

## Logins
i made some accounts to test it:

**Admin login:** (can do everything)
user: `admin`
pass: `password123`

**Teacher login:** (only grades)
user: `sarah_teacher`
pass: `password123`

## Features
- login works
- i think the grades work okay
- creating users works
- looks pretty good (i used some css)

## Folders
- `/models/` - database stuff
- `/controllers/` - php logic stuff
- `/views/` - html pages
- `/public/` - css and images

let me know if something breaks, it worked on my laptop.
