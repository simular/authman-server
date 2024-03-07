// JS file for Auth

import '@main';

u.formValidation()
  .then(() => {
    u.$ui.disableOnSubmit('#login-form');
    u.$ui.disableOnSubmit('#login-form-extra');
  });
