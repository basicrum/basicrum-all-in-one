<div class="float-right controls-container mr-4">
    <div data-days="30" class="period-control">30 Days</div>
    <div data-days="60" class="period-control">60 Days</div>
    <div data-days="90" class="period-control">90 Days</div>
</div>
<script type="application/javascript">
(function () {
    var defaultDays = '30';

    var periodControls = document.getElementsByClassName('period-control');

    for (var i=0, len = periodControls.length|0; i < len; i=i+1|0) {
        var days = periodControls[i].getAttribute('data-days');

        if (days === defaultDays) {
            periodControls[i].classList.add('active');
        }

        periodControls[i].addEventListener('click', function (event) {

            // If the clicked element doesn't have the right selector, bail
            if (event.target.matches('.active')) return;

            var newDays = event.target.getAttribute('data-days');

            BASIC_RUM_APP.plugins.widget.reloadPeriod(newDays);

            var periodControls = document.getElementsByClassName('period-control');

            for (var i=0, len = periodControls.length|0; i < len; i=i+1|0) {
                periodControls[i].classList.remove('active');
            }

            event.target.classList.add('active');

        }, false);
    }
})();
</script>