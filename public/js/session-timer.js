/**
 * Session dashboard: live elapsed timer + auto-expiry notice.
 * Each .elapsed-badge must have data-start (MySQL DATETIME) and
 * optionally data-duration (game duration in minutes).
 */
(function () {
    'use strict';

    function updateElapsed() {
        document.querySelectorAll('.elapsed-badge').forEach(function (badge) {
            var start    = new Date(badge.dataset.start.replace(' ', 'T'));
            var duration = parseInt(badge.dataset.duration, 10) || 0;
            var now      = new Date();
            var diffMs   = now - start;
            if (diffMs < 0) diffMs = 0;

            var totalSeconds  = Math.floor(diffMs / 1000);
            var totalMinutes  = Math.floor(totalSeconds / 60);
            var h = Math.floor(totalMinutes / 60);
            var m = totalMinutes % 60;
            var s = totalSeconds % 60;

            if (duration > 0 && totalMinutes >= duration) {
                badge.textContent = 'Time\'s up!';
                badge.className   = 'badge elapsed-badge badge-danger';
                // Show the End button prominently
                var row = badge.closest('tr');
                if (row) {
                    var endBtn = row.querySelector('.btn-danger');
                    if (endBtn) endBtn.classList.add('pulse');
                }
            } else {
                badge.textContent = h + 'h ' + m + 'm ' + s + 's';
                if (duration > 0) {
                    var pct = totalMinutes / duration;
                    badge.className = 'badge elapsed-badge ' +
                        (pct < 0.7 ? 'badge-success' : pct < 1 ? 'badge-warning' : 'badge-danger');
                } else {
                    badge.className = 'badge elapsed-badge ' +
                        (totalMinutes < 60 ? 'badge-success' : totalMinutes < 120 ? 'badge-warning' : 'badge-danger');
                }
            }
        });
    }

    updateElapsed();
    setInterval(updateElapsed, 1000);
}());
