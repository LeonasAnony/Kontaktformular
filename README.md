# Kontaktformular
Das Kontaktformular von dem Bremer Klimacamp

## Datein
### formular.php:
für Registrierung gedacht
Fragt ab:
- Name
- Email oder Telefonnummer
- Anreise datum
- Abreise datum oder "Dauergast"

Gibt zurück: Bei Auswahl von "Dauergast" einen 6 stelligen Code in logout.php genutzt werden kann

### logout.php
zum austragen von "Dauergästen" gedacht
Fragt ab:
- Code

### style.css
stylesheet für logout.php und formular.php

### Database.png
Bild von der Datenbank

## Struktur
### src/keys/
- Server Secret Key
- Client Public Key
- not accesible from the outside

### /
- formular.php
- logout.php
- accesible from the outside

### Offline Speicher(USB Stick)
- Server Public Key
- Client Secret Key
- wird nur zur entschlüsselung im Fall einer Infektion genutzt

### Mysql
Running everyday:
`DELETE FROM kontaktverfolgung_tbl WHERE Abreise != NULL AND Abreise <= DATE_SUB(NOW(),INTERVAL 30 DAY)`

## Kontakt
Wenn du Bugs oder Fehler findest schreib diese bitte an Telegram: @Le0nas
Danke!

## Setup
### Database
#### create db and user
```
CREATE DATABASE kontaktverfolgung;
CREATE USER 'kontaktUser'@'localhost' IDENTIFIED BY 'some_pass';
GRANT ALL PRIVILEGES ON kontaktverfolgung.* TO 'kontaktUser'@'localhost';
FLUSH PRIVILEGES;
```

#### create table
```
USE kontaktverfolgung;
CREATE TABLE kontaktverfolgung_tbl(
    Code VARCHAR(8),
    Nachname VARCHAR(250),
    Email VARCHAR(250),
    Telefonnummer VARCHAR(250),
    Anreise DATETIME,
    Abreise DATETIME,
    PRIMARY KEY ( Code )
    );
```

### nginx config #1
This is an example nginx server config. You have to change "YOUR-ADRESS", the path to your keys (idealy let this do Certbot), and "YOUR-WEBFOLDER" to the place where this is cloned to (for example `/var/www/html/Kontaktformular`).
```
server {
    if ($host = YOUR-ADRESS) {
        return 301 https://$host$request_uri;
    } # managed by Certbot


        listen 80;

        server_name YOUR-ADRESS;

        return 301 https://$server_name$request_uri;

}

# we're in the http context here
map $http_upgrade $connection_upgrade {
        default upgrade;
        ''      close;
}

server {
##      listen [::]:443 ssl http2 ipv6only=on;
        listen 127.0.0.1:443 ssl http2;

        ssl_certificate /path/to/fullchain.pem;
        ssl_certificate_key /path/to/privkey.pem;
        ssl_session_cache shared:SSL:20m;
        ssl_session_timeout 180m;
        ssl_prefer_server_ciphers on;
        ssl_ciphers ECDH+AESGCM:ECDH+AES256:ECDH+AES128:DHE+AES128:!ADH:!AECDH:!MD5;
        ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;
        ssl_protocols TLSv1.2;
        add_header Strict-Transport-Security 'max-age=31536000' always;


        root YOUR-WEBFOLDER;

        index index.php index.html index.htm;

        server_name YOUR-ADRESS;

        location / {
                try_files $uri $uri/ =404;
        }

#        location /src {
#                deny all;
#                return 404;
#        }

        location ~ \.php$ {
                include snippets/fastcgi-php.conf;
                fastcgi_pass unix:/run/php/php7.4-fpm.sock;
        }
}
```

### Setting Permissions
Je nachdem wie du dein Web-Verzeichnes aufbaust, musst du auch die Berechtigungen auf den Ordner ändern. Hier sind die Standard Berechtigungen(nginx):
`sudo chmod -R 755 Kontaktformular/`
`sudo chown -R www-data:www-data Kontaktformular/`

### generate Keys
Du musst zwei Keypare generieren. Das funktioniert grundlegend wie Email per PGP.
Dazu musst du einmal die `src/gen_key.php` aufrufen. Dabei wird dir dein private Key
angezeigt. Den musst du unbedint aufschreiben. Ohne den kanns du die Daten
aus der Datenbank nicht mehr entschluesseln.

### nginx config #2
Nachdem du die Keys generiert hast, darf der Ordner `src` von außen nicht mehr erreichbar sein. Änder dafür:
```
#        location /src {
#                deny all;
#                return 404;
#        }
```
zu:
```
        location /src {
                deny all;
                return 404;
        }
```
