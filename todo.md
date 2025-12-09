[x] Add method to strip out metadata
[] Strip out metadata before caching

[x] Remove "asadmin" en "within course" methods, they are bound to models directly
[x] Add course and user metadata to all the (non cached) service (via model populator?)

[x] Finish permission backpropagation.
[] Implement permission backpropagation where needed.

[] Modify services to take better account of forbidden/unauthorized vs 404 errors, and use it in user service check om niet admin keys te ondervangen. Gebruik in dat specifieke geval de optional course.

[] Maak plural field protected
[x] ~~Check of we uberhaupt nog een non-nullable fields veld nodig hebben nu we de identity kern via traits doen~~
[] Maak voor alle models een "stub" versie met alleen de identity (en optionele context)
en accepteer die in services ipv de hele. Maak dan volledige versies die die subclassen, en geef die specifiek terug.
[] Maak dan ook de verschillende niveas aan user objects als aparte models die die andere subclassen.

[x] Maak de permission manager ook dependency injected.
[x] Maak de types van de caching dingen generic.