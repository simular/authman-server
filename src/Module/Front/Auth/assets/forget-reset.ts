// JS file for ForgetReset

import '@main';

u.formValidation()
  .then(() => {
    u.$ui.disableOnSubmit('#reset-form');
  });
