.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.


.. _wizard-additional-parameters:

Using a wizard for additional parameters
----------------------------------------

You may need to ask user for more than a single parameter. And as a single string is used to store and pass them to your
method, you will have to encode them in some way, either as a comma-separated list of values or as a JSON string or even
as XML. This means you hardly can expect user to remember the exact syntax to be used to specify all those parameters.

You already know of many extensions providing a wizard to help you prepare plugin configuration. With this extension,
you also have the opportunity to create a wizard. The wizard is typically written in JavaScript and basically supports
whatever you may do with a TCA wizard of type "userFunc".

Screenshot below shows an additional icon next to the parameter field:

.. figure:: ../../Images/additional_parameters.png
   :alt: Additional parameters

In order to display the parameter wizard icon (|wizard|), you have to write a method ``getWizard()`` in your class that
returns either the JavaScript code to be executed when user clicks on the icon:

.. |wizard| image:: ../../Images/wizard.png
   :alt: Wizard available

.. code-block:: php

   public function getWizard(string $methodName, array &$PA, array $row, bool $checkOnly = false) : ?string
   {
       $js = null;

       if ($methodName === 'myRecipientList') {
           if ($checkOnly) {
               // We just need to return some non-empty string to show the wizard button
               return 'ok';
           }

           $js = '
               var params = $(\'[data-formengine-input-name="' . $PA['itemFormElName'] . '"]\').val();
               if (params == "") params = 2;
           ';

           // Show a standard javascript prompt and assign result to the parameters field
           // This information will be saved with form and available in myRecipientList
           $js .= '
               var r = prompt("How many items do you want in your list?", params);
               if (r != null) {
                   $(\'[data-formengine-input-name="' . $PA['itemFormElName'] . '"]\').val(parseInt(r));'.
                   implode('', $PA['fieldChangeFunc']) .';
               }
           ';
       }

       return $js;
   }

Parameters of the getWizard method are:

- **$methodName** : Name of the method for which a wizard may be specified.

- **$PA** : The full TCA configuration for the parameter field. Passed by reference. This allows you to change the way
  the input field *itself* is rendered.

- **$row** : The corresponding database row.

- **$checkOnly** : If ``true``, you should only return an non-empty string if some JS is needed. This is used by the
  FormEngine to show the wizard button next to the parameter field. The actual JS should be returned if ``$checkOnly``
  is `false`.

.. caution::

   Make sure to always run JavaScript code stored in ``$PA['fieldChangeFunc']`` when updating the value as it takes
   care of telling TCEforms that the value has been updated.

.. tip::

   Your code will effectively be embedded and run from a jQuery context and the jQuery object itself will be available
   as the usual ``$`` variable.


If you run code written in method ``getWizard()`` above, you will get a standard JavaScript prompt that asks you the
number of recipients you want to get in your recipient list:

.. figure:: ../../Images/alert_basic.png
   :alt: Standard alert box
