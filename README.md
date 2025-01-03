<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# An uptime monitoring application fully built with Laravel and Livewire.

## Features
- CSS Monitoring (Check for CSS links and if they are valid)
- Lighthouse Monitoring (Check for Lighthouse score and if it is above a certain threshold)
- Uptime Monitoring (Check if the website is up and running)
- SSL Monitoring (Check if the SSL certificate is valid)
- Playwright Monitoring (Run custom Playwright scripts to check for specific things on the website)
- Incident Management (View incidents that have occurred)

## Planned Features
- DNS Monitoring (Check if the DNS records are valid and check if they ever change for security reasons)

## Installation
### Web server
1. Make sure you have a system ready to run a laravel Application (for example a LAMP stack)
2. Clone the repository
3. Run `composer install`
4. Run `npm install`
5. Run `npm run build`
6. Run `php artisan migrate:fresh --seed` to create the database and add the default user
7. Add a cronjob with `* * * * * php /path/to/artisan schedule:run` to run the scheduled tasks
8. You should now be able to login with the default user (email: `test@example.com`, password: `password`)

### Default worker
1. Clone the repository
2. Run `composer install`
3. Run `php artisan queue:work` to start the worker (this should be run in the background with a process manager like `supervisord`)

### Lighthouse worker
1. Clone the repository
2. Run `composer install`
3. Install the lighthouse packages
```shell
npm install lighthouse
npm install chrome-launcher
```
4. If on a server, make sure to download chrome and chrome-driver (following example is for Ubuntu/Debian)
```shell
sudo apt install chromium-browser
```
5. Run `php artisan queue:work --queue=lighthouse` to start the worker (this should be run in the background with a process manager like `supervisord`)


## Usage
1. Add a new website to monitor
2. Your website should automagically start checking if you set everything up correctly
3. You can view the results on the website and view incidents if they occur

## Contributing
Pull requests are welcome. For major changes, please open an issue first to discuss what you would like to change.
