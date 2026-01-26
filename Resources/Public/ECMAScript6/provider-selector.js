/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

import FormEngineValidation from '@typo3/backend/form-engine-validation.js';

class ProviderSelector {
  constructor(options) {
    this.options = options || {};

    const providerField = document.getElementById(this.options['providerId']);
    const selectorField = document.querySelector('*[data-formengine-input-name="' + this.options['selectorName'] + '"]');
    const hiddenSelectorField = document.querySelector('input[name="' + this.options['selectorName'] + '"]');

    providerField.addEventListener('change', function (e) {
      selectorField.value = e.target.value;
      hiddenSelectorField.value = e.target.value;
      FormEngineValidation.markFieldAsChanged(selectorField);
      FormEngineValidation.validate();
      // TODO: show the reload confirmation wizard before reloading the form
      TYPO3.FormEngine.requestFormEngineUpdate();
    });
  }
}

export default ProviderSelector;
