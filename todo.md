[x] Add method to strip out metadata
[] Strip out metadata before caching

[x] Remove "asadmin" en "within course" methods, they are bound to models directly
[x] Add course and user metadata to all the (non cached) service (via model populator?)

[x] Finish permission backpropagation.
[] Implement permission backpropagation where needed.

[] Modify services to take better account of forbidden/unauthorized vs 404 errors, and use it in user service check om niet admin keys te ondervangen. Gebruik in dat specifieke geval de optional course.

[] re-implement getclientid om zo de user the identificeren ipv de api key te gebruiken (want die verloopt na een uur).
    doe dit door de endpoint van de user zelf op te vragen en zn domain+id(?) op te slaan als clientID.
[] Geef ensureX callables mee aan de cached providers. Of late bind ze (minder DPI proof). Of maak gewoon een losse functionaliteit/class die alles ensured voor een bepaalde ding en laat dit bij de gebruiker van de cache. doe late binding met een callable voor caching.

[x] Maak plural field protected
[x] ~~Check of we uberhaupt nog een non-nullable fields veld nodig hebben nu we de identity kern via traits doen~~
[] ~~Maak voor alle models een "stub" versie met alleen de identity (en optionele context) en accepteer die in services ipv de hele. Maak dan volledige versies die die subclassen, en geef die specifiek terug.~~
[x] Maak voor user een stub versie.
[x] Maak dan ook de verschillende niveas aan user objects als aparte models die die andere subclassen.

[x] Maak de permission manager ook dependency injected.
[x] Maak de types van de caching dingen generic.

[] Loop alle core providers door en kijk waar je de requests kan verbeteren door b.v. meer results per pagina te vragen.