# securepoint
2025-05-22
Erste Gedanken nach dem Lesen der Aufgabe:
Man könnte die File mit regulären Ausdrücken in ein assoziatives Array zerlegen, der JSON-Part ist so wie es aussieht komprimiert, hinter dem Block hierfür steht was von gzip, mal schauen, wie ich das sauber rausziehe, da gibt es bestimmt eine eingebaute Funktion mit ..._decode(), prüfe das auf php.net. Prüfe hier ebenfalls, ob es eine Funktion für das erstellen eines Arrays gibt, explode kommt hier wohl nicht in Frage, wegen fehlender einzigartiger Trennzeichen, aber vielleicht doch, oder etwas ähnliches?

Der JSON-Part ist für Bonus-Aufgabe 3 relevant, darum kümmere ich mich später, als erstes gilt es Aufgabe 1 und 2 zu lösen.

Geht nicht mit explode(), hier funktioniert lt. Php.net nur ein Komma als Separator, aber preg_split ginge. Schreibe ein erstes Test-Script, das den String in eine Variable lädt, aufgrund der Größe erstmal nur einen Teil, wahrscheinlich mit file_get_contents und teste, ob da was Gescheites bei rauskommt. Ergebnis: Ja, der komplette String sprengt das Memory Limit muss ich ggf. später anpassen, für den Test arbeite ich mit einem Teilstring von 100.000 Zeichen (als Argument in file_get_contents definiert).

Problem: auch wenn ich jetzt mit einem kleineren Datensatz arbeite, wird, wenn ich alles in ein Array lade, spätestens dann wahrscheinlich wieder das Memory Limit gesprengt. Frag google, ob es effizientere Methoden gibt, große Textdateien zu verarbeiten.

Mit fgets() kann ich über die Logfile Zeile für Zeile iterieren. Um die Tests von nun an kurz zu halten, habe ich die Logfile erstmal auf knapp über 200 Einträge gekürzt. Ich habe eine While-Schleife mit Ausgabe einer Zählvariablen zum Test drüberlaufen lassen. Funktioniert wo weit. Nun kann ich die einzelnen Datensätze zerpflücken. Die Frage ist immer noch, wie ich die Daten Sinnvoll zwischenspeichere. Alles auf einmal im Speicher ist too much, hier macht in meinen Augen nur eine DB sinn. Das erlaubt dann später auch recht performante SQL-Abfragen und die benötigten Daten zu aggregieren.




