# backend-hmif

## Recuirement
1. PHP 7.4.33
2. Mysql 8.0^
3. Composer

## How to set up project in local.
1. Clone repositories dengan "git clone https://github.com/hmifamikom-org/backend-hmif.git".

2. Masuk ke dalam direktori projek melalui command prompt atau terminal.

3. Install composer di dalam direktori projek, dengan code di bawah ini:

    "backend-hmif>composer install"
    
    Seperti gambar berikut :

    ![image](https://github.com/hmifamikom-org/backend-hmif/assets/58809318/30df0aa7-6ae7-4981-b4db-829e03e58833)

4. Rename file ".env.example" menjadi ".env"

5. Membuat secret-key untuk projek, dengan menjalankan code di bawah ini di dalam direktori projek:

    "backend-hmif>php artisan key:generate"

    Seperti gambar berikut :

    ![image](https://github.com/hmifamikom-org/backend-hmif/assets/58809318/c9f7d9ed-c4eb-490e-98a2-a0c203d530e3)       

6. Buka file ".env" kemudian berikan konfigurasi database dan app_url sesuai yang dibutuhkan.

    ![image](https://github.com/hmifamikom-org/backend-hmif/assets/58809318/5c80c8eb-d481-47d7-847e-c56d2bcdc741)

    Ubah isi "DB_DATABASE, DB_USERNAME, DB_PASSWORD" sesuai dengan nama database yang digunakan. 
    Jika database yang dimiliki tidak memiliki password, maka "DB_PASSWORD" dapat dikosongkan saja.

7. Projek backend-hmif sudah dapat digunakan.

-------------------------------------------------------------------------------------------------------------------------

## Teams
1. Asrul Septiawan Dwiantono (https://github.com/Asrulgans11)
2. Ema Devani Putri (https://github.com/emadev03)
3. Herman Dwi Yulianto (https://github.com/HermanDwiYulianto)
4. Ronald Ferdinand (https://github.com/1ronaldferdinand)
