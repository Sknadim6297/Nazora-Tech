(function ($) {
  var progressPath = document.querySelector('.progress-wrap path');
  if (!progressPath) return;

  var pathLength = progressPath.getTotalLength();
  progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
  progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
  progressPath.style.strokeDashoffset = pathLength;
  progressPath.getBoundingClientRect();
  progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';

  var $wrap = $('.progress-wrap');
  var offset = 50;
  var duration = 550;
  var ticking = false;

  function update() {
    var scroll = window.pageYOffset;
    var height = document.documentElement.scrollHeight - window.innerHeight;
    progressPath.style.strokeDashoffset = height > 0 ? pathLength - (scroll * pathLength / height) : pathLength;
    $wrap.toggleClass('active-progress', scroll > offset);
    ticking = false;
  }

  update();
  window.addEventListener('scroll', function () {
    if (ticking) return;
    ticking = true;
    window.requestAnimationFrame(update);
  }, { passive: true });

  $wrap.on('click', function (event) {
    event.preventDefault();
    $('html, body').animate({ scrollTop: 0 }, duration);
    return false;
  });
})(jQuery);
