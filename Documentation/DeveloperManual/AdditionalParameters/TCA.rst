.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.


.. _tca-additional-parameters:

Using TCA for additional parameters
-----------------------------------

The simple JavaScript wizard presented in previous section is fine as long as you want to store a single additional
parameter, e.g., a number or some string. But when you wish to store some complex settings such as various options or
business record references, this quickly becomes less than ideal and has the drawback that you are on your own to
serialize/deserialize your configuration options in the additional parameters text area. Besides, the serialized
configuration is shown in clear in the text area.

Fortunately there is an alternative. This extension lets you define custom TCA to be used for additional parameters.
Once defined, this custom TCA will *replace* the text area with any field you want:

.. figure:: ../../Images/tca.png
   :alt: TCA-based wizard

Upon saving, this extension will take care of automatically and transparently serializing your custom fields and their
corresponding values into the standard ``tx_directmailuserfunc_params`` database field (the one that is mapped to the
standard text area) as JSON. You will automatically get decoded values in your itemsProcFunc.

In order to replace the text area, you have to write a method ``getWizardFields`` in your class that returns an array
of table configuration (TCA):

.. code-block:: php

   public static function getWizardFields(string $methodName) : ?array
   {
       return [
           'columns' => [
               'field1' => [
                   // snip
               ],
               'field2' => [
                   // snip
               ],
           ],
           'types' => [
               '5' => [
                   'showitem' => 'field1, field2, ...'
               ]
           ],
           // If needed:
           'palettes' => [
               // snip
           ],
       ];
   }

Please refer to `['columns'] section in TCA reference <https://docs.typo3.org/typo3cms/TCAReference/Columns/Index.html>`__
for information on how to define your custom fields.

.. note::

   Type number "5" corresponds to a list of recipients defined as an external provider, what we are dealing with here.

.. tip::

   Method ``getWizardFields`` should return an empty array if no additional parameters are needed and ``NULL`` if the
   standard additional parameters text area should be kept (useful when the user class is used for both TCA and non
   TCA-based additional parameters).
