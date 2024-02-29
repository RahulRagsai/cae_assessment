# CAE Project

### Prerequisites

Make sure you have the following installed on your system:

- PHP (>= 8.1)
- Composer

### Installation
- cae.sqlite is placed on the root folder of the project directory.

- Roster - CrewConnex.html is placed on the root folder of the project directory.

- Install dependencies
    - composer install

- Do a fresh migration using the below command
    - php artisan migrate:fresh

- The postman collection link is below
    - https://api.postman.com/collections/33275235-ee573a56-e977-4291-8eab-cdfa0d6305c1?access_key=PMAT-01HQSZ2V6KY95Z2QGVVPP7QDVM

- The API endpoints are listed below.
    - upload
    - events
    - nextWeekFlight
    - nextWeekStandBy
    - getFlights

- To run tests
    - php artisan test