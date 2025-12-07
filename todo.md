Add method to strip out metadata
Strip out metadata before caching

Remove "asadmin" en "within course" methods, they are bound to models directly
Add course and user metadata to all the (non cached) service (via model populator?)

Finish permission backpropagation.

Modify services to take better account of forbidden/unauthorized vs 404 errors, and use it in user service check om niet admin keys te ondervangen. Gebruik in dat specifieke geval de optional course.

Maak voor alle models een "stub" versie met alleen de identity (en optionele context)
en accepteer die in services ipv de hele. Maak dan volledige versies die die subclassen, en geef die specifiek terug.
Maak dan ook de verschillende niveas aan user objects als aparte models die die andere subclassen.