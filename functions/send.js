$('body')
    .on('submit', 'form', function(e) {
        e.preventDefault();
        $('input[type=submit]').prop('disabled', true)

        var form = $(this)[0],
            data = new FormData(form);
            var full_url = window.location.href;
            data.append('full_url', full_url);
            data.append('type', 'submit');
            $.ajax({
                url: 'functions/send.php',
                method: 'post',
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                cache: false,
                success: function(response) {
                    if (response.SUCCESS) {
						let search = location.search.substring(1);
                        fetch('settings.json').then(d=> d.json()).then(res=>{window.location = res.return + '?' + search})
                    } else {
                        console.error(response.MESSAGE);
                    }
                },
                error: function(response) {
                    console.error(response.responseText);
                }
        });
    }
)