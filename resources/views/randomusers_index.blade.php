@extends('layouts.randomusers')

@section('content')
    <h1>Random Users</h1>

    <form action="#" method="GET">
        @csrf

        <label for="fields">Fields:</label>
        <select name="fields[]" id="fields" multiple>
            <option value="name" selected>Full Name</option>
            <option value="phone" selected>Phone</option>
            <option value="email" selected>Email</option>
            <option value="location" selected>Country</option>
        </select>

        <label for="user_qty">User Numbers:</label>
        <input type="text" name="user_qty" id="user_qty" required value="10">

        <label for="format">Format:</label>
        <select name="format" id="format">
            <option value="xml">XML</option>
            <option value="json" selected>JSON</option>
        </select>

        <label for="sort_by">Sort By:</label>
        <select name="sort_by" id="sort_by">
            <option value="last" selected>Last Name</option>
            <option value="phone">Phone</option>
            <option value="email">Email</option>
            <option value="country">Country</option>
        </select>
        <label for="sort_order">Sort Order:</label>
        <select name="sort_order" id="sort_order">
            <option value="asc" selected>Ascending</option>
            <option value="desc">Descending</option>
        </select>

        <button type="submit">Submit</button>
    </form>

    <label for="results">Data:</label>
    <textarea id="results" rows="40" cols="100"></textarea>

    <script>
        $("form").on("submit", function (event) {
            event.preventDefault();
            let data = $(this).serialize()
            let url = '{{ route('api.randomusers') }}';
            $( "#results" ).text('');

            $.get(url, data)
                .done(function (res) {
                    let format = $('#format').val();
                    if (format === 'json') {
                        $("#results").text(JSON.stringify(res, null, 2));
                    } else {
                        $("#results").text(formatXml(res));
                    }
                    console.log(res);
                })
                .fail(function (e) {
                    alert("Response Error: " + e.responseJSON.message);
                    console.log(e);
                });
        });

        function formatXml(xml) {
            let xmlString = new XMLSerializer().serializeToString(xml);

            let formattedXml = '';
            let reg = /(>)(<)(\/*)/g;
            xmlString = xmlString.replace(reg, '$1\r\n$2$3');

            let pad = 0;
            xmlString.split('\r\n').forEach(function (node) {
                let indent = 0;
                if (node.match(/.+<\/\w[^>]*>$/)) {
                    indent = 0;
                } else if (node.match(/^<\/\w/)) {
                    if (pad !== 0) {
                        pad -= 1;
                    }
                } else if (node.match(/^<\w[^>]*[^\/]>.*$/)) {
                    indent = 1;
                } else {
                    indent = 0;
                }

                let padding = '';
                for (let i = 0; i < pad; i++) {
                    padding += '  ';
                }

                formattedXml += padding + node + '\r\n';
                pad += indent;
            });

            return formattedXml;
        }
    </script>
@endsection
