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
