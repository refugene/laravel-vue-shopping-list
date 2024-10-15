# Laravel Vue Shopping List App

## Setup
- Copy .env.example to .env and add your MySQL connection settings
- Run `php artisan migrate:fresh --seed` to set up the database schema and create some data
- Build assets with `npm run build`
- Run feature tests with `php artisan test`
- You can log in with user `test@example.com` password `password` and you'll be redirected to see the user's shopping list at /shopping-list

## About the Shopping List App
This app provides a basic shopping list built with Laravel and Vue.js using Inertia.js
When seeding the database as described in [setup](#setup), 2 test users are created each with 10 items in their shopping list
A diff of all code changes to the default Laravel and Inertia with Vue install can be seen [here](https://github.com/refugene/laravel-vue-shopping-list/pull/1)

A logged in user can:
- add a new item with a price to their shopping list
- delete an item from their shopping list
- Mark each item as 'bought' and also toggle this back by clicking 'undo' 
- Reorder items by dragging and dropping them in the list
- See a total cost of the shopping list
- See a warning if the total cost of the shopping list exceeds £100
