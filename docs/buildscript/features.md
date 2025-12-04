The buildscript automatically generates several traits and interfaces:

For models, it generates getters and setters for the defined properties via a trait.

For providers:
* It generates a trait containing plural versions of doSomethingInModel, as well as methods with names that use aliased plural forms.
* It generates an interface for all the methods present in the provider and its trait, keeping the interface up to date with the actual base implementation. This makes the regular provider the source of truth for the interface.