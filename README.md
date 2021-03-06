# C3PO Module
## Create Custom Ctools Plugins as Objects

_https://github.com/KeyboardCowboy/drupal7-c3po_

This module places OOP wrappers around ctools plugins.  The object-oriented
structure of a plugin gives developers the following benefits:
 
1. Reduces the amount of code needed to create a custom plugin by leveraging
   class inheritance.

1. Makes it easier to write PHPUnit tests for plugins.
 
1. Enforces good practices and helps prepare developers for D8 plugin
   development. 

This module doesn't do anything out of the box.  It simply provides a framework
for writing plugins in OOP.
 
## Examples
- Access Plugins (selection rules): `[path-to-modules]/c3po/plugins/access/c3po_example.inc`
- Content Type Plugins: `[path-to-modules]/c3po/plugins/content_types/c3po_example.inc`
