Project DDD
===========

Simple demo project:

## Prooph : https://github.com/prooph

This project is inspired by https://github.com/prooph/proophessor-do-symfony

You can just test a registration and change your information with fake information.
The important here is to look at the events and snapshot created in your database.
Look also in the symfony profiler how doctrine queries are processed.

## RxPHP : https://github.com/ReactiveX/RxPHP

RxPHP is used to handle asynchronous call. Prooph uses a React\Promise to 
handle queries; With RxPHP you can wrap a promise with an Observable.

An pseudo code example :

Example of RxPHP

```
Get User for a given userId and return an Observable
    array [
      "userId" => UserId 
      "emailAddress" => EmailAddress
      "username" => "syzof@yahoo.com"
    ]
```
```
Loads events and return an Observable
    array [
        0 => UserWasRegistered 
    ]
``` 

```
Zip user and events then return an Observable
    array [
      "userId" => UserId
      "emailAddress" => EmailAddress
      "username" => "syzof@yahoo.com"
      "userEvents" => array
    ]
```

```
Get publisher for a given publisherId
    array [
      "publisherId" => PublisherId
    ]
```
```
Loads events and return an Observable
   array [
     0 => LightPublisherWasRegistered
     1 => BusinessInformationsUpdated
     2 => BusinessInformationsUpdated
     3 => BusinessInformationsUpdated
     4 => BusinessInformationsUpdated
     5 => BusinessInformationsUpdated
     6 => BusinessInformationsUpdated
     7 => BusinessInformationsUpdated
     8 => BusinessInformationsUpdated
     9 => BusinessInformationsUpdated
     10 => BusinessInformationsUpdated
   ] 
```
```
Zip publisher and events then return an Observable
    array [
      "publisherId" => PublisherId
      "publisherEvents" => array
    ]
```
```
Zip user Observable and publisher Observable
And return an Observable

array [
  "userId" => UserId
  "emailAddress" => EmailAddress}
  "username" => "syzof@yahoo.com"
  "userEvents" => array
  "publisherId" => PublisherId 
  "publisherEvents" => array
]

```

Installation:
-------------

```sh
git clone
```

```sh
composer install
```

```sh
bin/console do:mi:mi
```

```sh
bin/console ser:run
```
