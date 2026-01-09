(function() {
  'use strict';
window.addEventListener('load', function () {
  var forms = document.getElementsByClassName('suppliers-form');

  Array.prototype.forEach.call(forms, function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
});
})();