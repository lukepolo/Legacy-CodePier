Vue.directive('cronjob', {
    inserted: function (el) {
        $(el).cron({
            onChange () {
                const cronTiming = $(this).cron('value')
                $('#cron-preview').text(cronTiming)
                $('input[name="cron_timing"]').val(cronTiming)
            }
        })
    }
})
