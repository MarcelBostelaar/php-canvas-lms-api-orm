[x] Add method to strip out metadata
[] Strip out metadata before caching

[x] Remove "asadmin" en "within course" methods, they are bound to models directly
[x] Add course and user metadata to all the (non cached) service (via model populator?)

[x] Finish permission backpropagation.
[] Implement permission backpropagation where needed.

[x] Modify services to take better account of forbidden/unauthorized vs 404 errors, and use it in user service check om niet admin keys te ondervangen. Gebruik in dat specifieke geval de optional course.
[x] Add php stan
[x] Return standaard een generic api result class met 200, 401, 403 en 404 status en value.
[x] Maak handler een transformer die T -> U doet, die je op elkaar kan chainen.
[x] Maak de providerinterface generic voor de return type
[x] Cache versies hebben de standaard versie nodig
         Zo kan de end-user een eigen handler geven die bv alle errors gewoon in een throw omzetten en het gebruik makkelijker maken.

[x] ~~re-implement getclientid om zo de user the identificeren ipv de api key te gebruiken (want die verloopt na een uur). doe dit door de endpoint van de user zelf op te vragen en zn domain+id(?) op te slaan als clientID.~~ Niet nodig, permissions ophalen is niet expensive. Gehashe api key is prima.

[] Geef ensureX callables mee aan de cached providers. Of late bind ze (minder DPI proof). Of maak gewoon een losse functionaliteit/class die alles ensured voor een bepaalde ding en laat dit bij de gebruiker van de cache. doe late binding met een callable voor caching.

[x] Maak plural field protected
[x] ~~Check of we uberhaupt nog een non-nullable fields veld nodig hebben nu we de identity kern via traits doen~~
[x] Maak voor alle models een "stub" versie met alleen de identity (en optionele context) en accepteer die in services ipv de hele. Maak dan volledige versies die die subclassen, en geef die specifiek terug.
[x] Maak voor user een stub versie.
[x] Maak dan ook de verschillende niveas aan user objects als aparte models die die andere subclassen.

[x] Maak de permission manager ook dependency injected.
[x] Maak de types van de caching dingen generic.

[] Look alle core providers door en kijk waar je de requests kan verbeteren door b.v. meer results per pagina te vragen.