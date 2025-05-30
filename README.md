# securepoint
Dokumentation Arbeitsschritte und Gedanken hierzu:

2025-05-22
Erste Gedanken nach dem Lesen der Aufgabe:
Man könnte die File mit regulären Ausdrücken in ein assoziatives Array zerlegen, der JSON-Part ist so wie es aussieht komprimiert, hinter dem Block hierfür steht was von gzip, mal schauen, wie ich das sauber rausziehe, da gibt es bestimmt eine eingebaute Funktion mit ..._decode(), prüfe das auf php.net. Prüfe hier ebenfalls, ob es eine Funktion für das erstellen eines Arrays gibt, explode kommt hier wohl nicht in Frage, wegen fehlender einzigartiger Trennzeichen, aber vielleicht doch, oder etwas ähnliches?

Der JSON-Part ist für Bonus-Aufgabe 3 relevant, darum kümmere ich mich später, als erstes gilt es Aufgabe 1 und 2 zu lösen.

Geht nicht mit explode(), hier funktioniert lt. Php.net nur ein Komma als Separator, aber preg_split ginge. Schreibe ein erstes Test-Script, das den String in eine Variable lädt, aufgrund der Größe erstmal nur einen Teil, wahrscheinlich mit file_get_contents und teste, ob da was Gescheites bei rauskommt. Ergebnis: Ja, der komplette String sprengt das Memory Limit muss ich ggf. später anpassen, für den Test arbeite ich mit einem Teilstring von 100.000 Zeichen (als Argument in file_get_contents definiert).

2025-05-22
Problem: auch wenn ich jetzt mit einem kleineren Datensatz arbeite, wird, wenn ich alles in ein Array lade, spätestens dann wahrscheinlich wieder das Memory Limit gesprengt. Frag google, ob es effizientere Methoden gibt, große Textdateien zu verarbeiten.

Mit fgets() kann ich über die Logfile Zeile für Zeile iterieren. Um die Tests von nun an kurz zu halten, habe ich die Logfile erstmal auf knapp über 200 Einträge gekürzt. Ich habe eine While-Schleife mit Ausgabe einer Zählvariablen zum Test drüberlaufen lassen. Funktioniert wo weit. Nun kann ich die einzelnen Datensätze zerpflücken. Die Frage ist immer noch, wie ich die Daten Sinnvoll zwischenspeichere. Alles auf einmal im Speicher ist too much, hier macht in meinen Augen nur eine DB sinn. Das erlaubt dann später auch recht performante SQL-Abfragen und die benötigten Daten zu aggregieren.

2025-05-23
Daten kommen aus Datei und werden bei jedem Schleifendurchlauf mit explode(" ", $file); in Array zerlegt. Zeile und Array (ja, seitdem ich PHP spreche fange ich mit 0 an zu zählen) wird erstmal commitet, später kümmere ich mich darum, die Daten in die DB zu schreiben. 

Erstelle DB, hier verwende ich die IP als Unique ID, sollte es später beim Import zum Fehler kommen, weiß ich, dass ich über die IP kein einzelnes Gerät als solches über die IP erkennen kann. 
Für das JSON-Feld nehme ich erstmal "varchar", später kümmere ich mich ums decodieren, wenn ich bei der Bonusaufgabe angekommen bin.

Später am Nachmittag: Habe DB angelegt und mal testweise einfach die Count-Variable in den primary key geschrieben. Offensichtlich fehlt genau die Hälfte der Daten aus meiner Test-Logfile. Prüfe den Code.
Problem: Habe fgets() in der while-Schleife ein zweites Mal aufgerufen. Lösung: Nutze fgets() aus dem Aufruf der Schleife. (Klammersetzung korrigiert).

Schreiben in DB funktioniert, erstmal nur das erste Feld (IP). Ich teste direkt die Originaldatei, ob ich die IP als Primary Key verwenden kann. Ergebnis: Funktioniert so nicht, IP ist nicht unique. Setze stattdessen neues Primärschlüsselfeld mit auto_increment und mache testlauf mit der Originaldatei, ob es wirklich keine RAM-Probleme oder dergleichen gibt und das Script sauber bis zum Schluss durchläuft.

Es läuft und läuft und läuft... Mach an dieser Stelle einen weiteren commit, werde mich im Anschluss darum kümmern, die kompletten Daten in die DB zu schreiben, so dass ich im Anschluss dazu übergehen kann die Fragen aus der Aufgabenstellung mittels SQL-Querys zu beantworten.

2025-05-24
IP konnte ja nie funktionieren um ein physisches Gerät sicher zu erkennen, da sich die Geräte ja auch mehrfach verbinden. Dies funktioniert meines Erachtens nur über die "mac" aus dem codierten json part. also geschaut, wie ich so etwas sauber decodieren kann (google, stackoverflow, php.de und php.net) und mit viel probieren und wieder nachlesen zu einem ergebnis gekommen. hierfür nun auch spalten in der db angelegt, beim testlauf noch bemerkt, dass die manchmal null sind und um warnings zu vermeiden den coalescent operator bei der variablenzuweisung für die query-execution eingesetzt. Lade jetzt alle daten in die DB...

Mit preg_replace noch "serial=" abgeschnitten. Nun lässt sich Frage 1 mit "SELECT `serialnumber_license`, COUNT(*) AS `anzahl` FROM `log_data` GROUP BY `serialnumber_license` HAVING COUNT(*) > 1 ORDER BY `anzahl` DESC;" beantworten.

Ich glaube hier passiert auch Vorarbeit für die Lösung von Aufgabe 2: Ich bekomme hier ja alle Seriennummern, die mehr als einmal in der db auftauchen. Hier habe ich eine Vorauswahl, ich muss dann nur noch vergleichen ob es pro Seriennummer mehr als eine 'mac' gibt. Während die DB gerde nochmal nach dem preg_replace neu befüllt wird spiele ich in der neu angelegten Datei excercise2.php mal verschiedene sql/php lösungen durch. Später werde ich den Code noch sauber auf verschiedene Dateien aufteilen und eine optisch zumindest etwas ansprechende Ausgabe erzeugen. Es folgt ein commit, damit nachvollziehbar ist, was ich gerade tue.

2025-05-25
Ok, habe jetzt ein array befüllt, in denen die Lizenz-Seriennummern, die mehr als einmal in der db vorkommen enthalten sind. Jetzt muss ich nur noch schauen, ob eine Seriennummer mehr als eine mac-Adresse hat.

Das Array hätte ich dafür gar nicht befüllen müssen, nach einigem probieren und recherchieren habe ich erkannt, dass es auch hierfür eine performante SQL-Abfrage gibt. Viel rumprobieren, bis es endlich funktioniert hat.

Die Dateien wurden wie folgt umbenannt: die index.php liefert jetzt die Antworten auf die gestellten Fragen. Die dataload.php lädt die Daten neu in die Datenbank. Ggf. sollten diese vorher manuell gelöscht werden.

Bonusfrage: Eine Kombination der verschiedenen Angaben zur Hardware (machine, mem, cpu, disk_root, disk_data) sollten auf einen bestimmten Typen Hardware schließen lassen (das Feld 'architecture' ist hier überflüssig, ist ja in 'machine' auch mitcodiert). Das sollte wieder eine große SQL-Bastelei werden, in der ich prüfe, wie oft es jede Kombination gibt.

Hmmm, dabei überlege ich mir, dass die Werte für disk_root und disk_data vielleicht zu spezifisch sind. Ich bin mir an dieser Stelle nicht sicher, ob es sich um belegten oder insgesamt verfügbaren Speicherplatz handelt. Ich denke die Kombination aus machine, mem und cpu sollte ausreichen.

Um jetzt herauszufinden, wie viele Lizenzseriennummern auf den jeweiligen Hardwaretypen installiert sind, gehe ich wie folgt vor: Ich ergänze eine Spalte in der DB in die ich den Hardwaretypen schreibe (integer, einfach definiert durch eine Zählvariable). Hierfür muss ich noch einen key generieren (mit den hardwaredaten) und der value ist dann der Hardwaretyp ($type_number). Ich bin mir nicht sicher, ob es hierfür nicht eine einfachere Lösung gibt, aber mir fällt dazu nichts ein. Dieser Zwischenschritt scheint zu funktionieren, zur Dokumentation mache ich einen commit.
Zum Schluss kann ich eine SQL Abfrage basteln, die mir die Bonusfrage beantwortet.

Erledigt. Die Aufgaben sind beantwortet.

Mir fällt an dieser Stelle auf, dass die Hardwareklassenzuordnung wohl nicht ganz sauber ist, es gibt zu viele Klassen. Eine Klasseneinteilung mit weniger Parametern wäre wohl sinnvoller, z.B. nur anhand von CPU. Auch sind einige Datenfelder in der DB NULL, so dass ich noch einmal tiefergehend überprüfen müsste, ob das Einspielen der Daten aus der Logfile in die DB sauber funktioniert, ich denke hier gibt es noch Probleme, die ich lösen müsste. Leider fehlt mir an dieser Stelle die Zeit, mein alter Laptop braucht jedes mal knapp 3 Stunden um die Daten in die DB zu schreiben.