# BookingApp

Aplikacja do rezerwacji sal konferencyjnych. Laravel 12 + Vue 3.

Demo: https://bookingapp.atomicsandbox.com/

## Instalacja

```bash
git clone <repo-url>
cd bookingapp.atomicsandbox.com

composer install
npm install
```

Skopiuj plik `.env.example` i nazwij go `.env`, następnie ustaw dane do bazy:

```
DB_DATABASE=bookingapp
DB_USERNAME=root
DB_PASSWORD=
```

## Uruchomienie

Uruchom Apache i MySQL (np. przez XAMPP), następnie:

```bash
php artisan migrate --fresh --seed
npm run dev
```

Dodaj virtualhost lub uruchom wbudowany serwer:

```bash
php artisan serve
```

## Dane testowe

Hasło do obu kont: `password`

- test@bookingapp.com
- test2@bookingapp.com


## Testy

```bash
php artisan test
```
