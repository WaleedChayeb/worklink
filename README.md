## QdevTechs - JobPortal

#### Tech used

- Laravel 9
- Bootstrap 4.6.X
- Vanilla JS & jQuery
- Multiple 

#### Requirements

* PHP 8.0.2+
* Mysql / MariaDB (5.7/8.X)
* Apache & mod_rewrite / Nginx
* Node, Composer & at least 2GB of RAM for dev builds

#### Install

````
composer install
cp .env.sample .env # And edit values
php artisan npm:install
npm run prod
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
````


#### Saving admin panel state.
 This will remove all prior admin related seeds and reverse genererate new ones - so default admin state & settings will persist.

````
php artisan admin:save
````

#### Publishing frontend libraries
 Onto public directory. Eg: You npm add a new lib and need to include it  into your views.
```
php artisan npm:publish
```


#### Running Code fixers

````
php artisan code:check type=php/js
php artisan code:fix type=php/js
````

####  Crons

````
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
````

#### Testing
```
php artisan dusk:chrome-driver
php artisan test:ui
or 
php artisan dusk
```

### Ionicons usage

Icons ( Ionicons )
Backend

````
 @ include('elements.icon',['icon'=>'chevron-heart'])
 @ include('elements.icon',['icon'=>'chevron-heart-outline'])
 @ include('elements.icon',['icon'=>'chevron-heart-outline','variant'=>'medium])
 @ include('elements.icon',['icon'=>'chevron-heart-outline','variant'=>'medium','centered'=>'true'])
````
            
Frontend
````
< ion-icon name="heart"> 
< ion-icon name="heart-outline"> 
< ion-icon name="heart-sharp"> 
< ion-icon size="small">
< ion-icon size="large">
````
            
### Translations

Backend
````
trans_choice('We got coconut.',2,['number'=>2])
We got coconut.

trans_choice('We got coconut.',1,['number'=>1])
We got coconut.

__('English is nice')
English is nice

__('Food is good',['food'=>'cacao cu lapte'])
Food is good
````

Frontend
````
trans_choice('We got 1 coconut.',2,{'number':2})
We got 1 coconut.

trans_choice('We got 1 coconut.',1,{'number':1})
We got 1 coconut.

trans('English is nice')
English is nice

trans('Food is good',{'food':'cacao cu lapte']})
Food is good
````

#### Benchmarks & Performance

Tested on a dual core, $10 Digital ocean droplet, running nginx wiht php-fpm and PHP74, which tends to throttle CPU usage we got the following results:

- Avg Max concurent request: ~240rps
- Avg Load time: ~0.5s
- Total bundle overhead (Gzipped): ~241KB

_Wrk Benchmark tool sample_
![alt text](https://i.imgur.com/gZ3o7eP.png)

_Google Lighthouse/Page Insights report sample_
![alt text](https://i.imgur.com/mFXY8Zb.png)

#### Questions?

Send us a message over http://qdev.tech .
