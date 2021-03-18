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
### /var/www/html/keys/
- Server Secret Key
- Client Public Key
- nicht von außen zugreifbar


### /var/www/html/klimacamp/corona/
- formular.php
- style.css
- logout.php
- Von außen zugreifbar


### Offline Speicher(USB Stick)
- Server Public Key
- Client Secret Key
- wird nur zur entschlüsselung im Fall einer Infektion genutzt

### Mysql
Running everyday:
`DELETE FROM kontaktverfolgung_tbl WHERE Abreise <= DATE_SUB(NOW(),INTERVAL 30 DAY)`

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
    id INT NOT NULL AUTO_INCREMENT,
    Nachname VARCHAR(250),
    Email VARCHAR(250),
    Telefonnummer VARCHAR(250),
    Anreise DATETIME,
    Abreise DATETIME,
    Dauer BOOLEAN(1),
    Code VARCHAR(8),
    PRIMARY KEY ( id )
    );
```

### generate Keys
Du musst zwei Keypare generieren. Das funktioniert grundlegend wie Email per PGP.
Dazu musst du einmal die `gen_key.php` aufrufen. Dabei wird dir dein private Key
angezeigt. Den musst du unbedint aufschreiben. Ohne den kanns du die Daten
aus der Datenbank nicht mehr entschluesseln.
