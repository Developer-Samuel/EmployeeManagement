Projekt som vytvoril v PHP frameworku Nette a Javascript / jQuery.
Pri štýlovaní som použil preprocesor Sass, ktorý kompiluje štýly aj do CSS, bez neustalého zapnutia "npm run dev"

Pre tabuľku so zamestnancami, som použil knižnicu: Datatable.js
Pre graf s vekom zamestnancov, som použil knižnicu: Chart.js

Dáta zamestnancov sú uložené: www/files/xml/employees.xml
XML súbor slúži ako databáza, a dajú sa tam pridávať, úpravovať, a mazať záznamy, a tiež sa zobrazujú v tabuľke.
Pri pridávaní a úpravovaní je urobená validácia pre štruktúru XML, a tiež pre formuláre v skripte pre inputy.

Všetky akcie, ktoré sa vykonávajú sú uložené v app/Models/Employee.php:
    - Zvýšovanie následujucého ID
    - Nájdenie zamestnanca na zaklade ID
    - Zisťovanie či existuje záznam s rodným číslom
    - Zobrazenie zamestnancov
    - Vytvorenie zamestnanca
    - Úprava zamestnanca
    - Vymazanie zamestnanca
