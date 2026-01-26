.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.


.. _developer-manual:

Developer manual
================

Simplest class and method you may write to dynamically create a
recipient list.

.. code-block:: php

   class user_testList {
       public function myRecipientList(array &$params, $pObj) {
           $params['lists']['PLAINLIST'][] = array(
               'name' => 'John Doo',
               'email' => 'john.doo@gmail.com',
           );
           $params['lists']['PLAINLIST'][] = array(
               'name' => 'Foo Bar',
               'email' => 'foo.bar@yahoo.fr',
           );
       }
   }

If you wish to provide records from tables ``tt_address`` or ``fe_users``,
this is easily done with:

.. code-block:: php

   // Add tt_address record with uid 12 to the list
   $params['lists']['tt_address'][] = 12;

   // Add fe_users record with uid = 14 to the list
   $params['lists']['fe_users'][] = 14;


.. toctree::
   :maxdepth: 3
   :titlesonly:
   :glob:

   RegisteringTheUserFunction/Index
   AdditionalParameters/Index
   AdditionalParameters/Wizard
   AdditionalParameters/TCA
   SampleUserFunctions/Index
