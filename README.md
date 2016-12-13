# PVM-15
Code zum "Partizipativen Virtuellen Museum"

http://pvm.uni-leipzig.de

Das Projekt "Partizipatives Virtuelles Museum" war ein Gemeinschaftsprojekt mit
dem Institut für Kunstpädagogik an der Uni Leipzig. Das Projekt wurde im Rahmen
der
[Laboruniversität](http://studienart.gko.uni-leipzig.de/pvm/ueber-das-projekt-2/)
gefördert.

Das virtuelle Museum basiert auf Wordpress. Die zusätzliche Funktionalität
(Umgang mit Werken und Projekten) ist im WP-Plugin "pvmkit" zusammengefasst. 

Hauptentwickler: Sebastian Günther. Weitere Beiträge von Alexander Lieder und
Hans-Gert Gräbe.

**Dokumente** - LaTeX-Quellen verschiedener Dokumente zum Projekt. 

**Code/esteem_pvm_child** - Child Theme von
  [esteem](https://de.wordpress.org/themes/esteem/), enthält Modifikationen des
  Themas für verschiedene Darstellungsaufgaben. 

**Code/pvmkit**  - Plugin, das die Hauptfunktionalitäten der Plattform beinhaltet. 

**Code/pvm2rdf** - Plugin, das u.a. die Anzeige der RDF-Datenbasis der im PVM
veröffentlichten Werke als WP-Shortcode zur Verfügung stellt.  Werke (RDF-Typ
pvm:Werk) bestehen aus einzelnen Objekten (RDF-Typ pvm:Objekt) und zugeordneten
Eigenschaften usw. 

Der Shortcode für die Anzeige der Werke ist auf der Seite
http://pvm.uni-leipzig.de/testseite/ ausgerollt. 