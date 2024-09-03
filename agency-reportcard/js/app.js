$(document).foundation()

const deptHeaders = document.querySelectorAll('.dept');

deptHeaders.forEach(function (deptHeader) {
  deptHeader.addEventListener('click', function () {
    const chevron = this.querySelector('.chevron');
    chevron.classList.toggle('rotate');
  });
});
