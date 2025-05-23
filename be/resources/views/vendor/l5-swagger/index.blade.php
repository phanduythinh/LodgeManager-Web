<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>{{ config('l5-swagger.documentations.' . $documentation . '.api.title') }}</title>
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'images/favicon-32x32.png') }}"
        sizes="32x32" />
    <link rel="icon" type="image/png" href="{{ l5_swagger_asset($documentation, 'images/favicon-16x16.png') }}"
        sizes="16x16" />
    <link href="{{ l5_swagger_asset($documentation, 'css/typography.css') }}" media="all" rel="stylesheet"
        type="text/css" />
    <link href="{{ l5_swagger_asset($documentation, 'css/reset.css') }}" media="all" rel="stylesheet"
        type="text/css" />
    <link href="{{ l5_swagger_asset($documentation, 'css/screen.css') }}" media="all" rel="stylesheet"
        type="text/css" />
    <link href="{{ l5_swagger_asset($documentation, 'css/reset.css') }}" media="print" rel="stylesheet"
        type="text/css" />
    <link href="{{ l5_swagger_asset($documentation, 'css/print.css') }}" media="print" rel="stylesheet"
        type="text/css" />

    <script src="{{ l5_swagger_asset($documentation, 'lib/jquery-1.8.0.min.js') }}" type="text/javascript"></script>
    <script src="{{ l5_swagger_asset($documentation, 'lib/jquery.slideto.min.js') }}" type="text/javascript"></script>
    <script src="{{ l5_swagger_asset($documentation, 'lib/jquery.wiggle.min.js') }}" type="text/javascript"></script>
    <script src="{{ l5_swagger_asset($documentation, 'lib/jquery.ba-bbq.min.js') }}" type="text/javascript"></script>
    <script src="{{ l5_swagger_asset($documentation, 'lib/handlebars-4.0.5.js') }}" type="text/javascript"></script>
    <script src="{{ l5_swagger_asset($documentation, 'lib/lodash.min.js') }}" type="text/javascript"></script>
    <script src="{{ l5_swagger_asset($documentation, 'lib/backbone-min.js') }}" type="text/javascript"></script>
    <script src="{{ l5_swagger_asset($documentation, 'swagger-ui.js') }}" type="text/javascript"></script>
    <script src="{{ l5_swagger_asset($documentation, 'lib/highlight.9.1.0.pack.js') }}" type="text/javascript"></script>
    <script src="{{ l5_swagger_asset($documentation, 'lib/highlight.9.1.0.pack_extended.js') }}" type="text/javascript">
    </script>
    <script src="{{ l5_swagger_asset($documentation, 'lib/jsoneditor.min.js') }}" type="text/javascript"></script>
    <script src="{{ l5_swagger_asset($documentation, 'lib/marked.js') }}" type="text/javascript"></script>
    <script src="{{ l5_swagger_asset($documentation, 'lib/swagger-oauth.js') }}" type="text/javascript"></script>

    <!-- Some basic translations -->
    <script src="{{ l5_swagger_asset($documentation, 'lang/translator.js') }}" type="text/javascript"></script>
    <script src="{{ l5_swagger_asset($documentation, 'lang/vi.js') }}" type="text/javascript"></script>
    <script src="{{ l5_swagger_asset($documentation, 'lang/en.js') }}" type="text/javascript"></script>

    <script type="text/javascript">
        $(function() {
            var url = window.location.search.match(/url=([^&]+)/);
            if (url && url.length > 1) {
                url = decodeURIComponent(url[1]);
            } else {
                url =
                    "{{ l5_swagger_asset($documentation, 'docs/' . $documentation . '/' . config('l5-swagger.documentations.' . $documentation . '.paths.docs_json')) }}";
            }

            window.swaggerUi = new SwaggerUi({
                url: url,
                dom_id: "swagger-ui-container",
                supportedSubmitMethods: ['get', 'post', 'put', 'delete', 'patch'],
                onComplete: function(swaggerApi, swaggerUi) {
                    if (typeof initOAuth == "function") {
                        initOAuth({
                            clientId: "your-client-id",
                            clientSecret: "your-client-secret-if-required",
                            realm: "your-realms",
                            appName: "your-app-name",
                            scopeSeparator: " ",
                            additionalQueryStringParams: {}
                        });
                    }

                    if (window.SwaggerTranslator) {
                        window.SwaggerTranslator.translate();
                    }

                    $('pre code').each(function(i, e) {
                        hljs.highlightBlock(e);
                    });

                    addApiKeyAuthorization();
                },
                onFailure: function(data) {
                    log("Unable to Load SwaggerUI");
                },
                docExpansion: "{{ config('l5-swagger.defaults.ui.display.doc_expansion') }}",
                apisSorter: "{{ config('l5-swagger.defaults.ui.display.apis_sorter') }}",
                defaultModelRendering: '{{ config('l5-swagger.defaults.ui.display.default_model_rendering') }}',
                showRequestHeaders: false
            });

            function addApiKeyAuthorization() {
                var key = encodeURIComponent($('#input_apiKey')[0].value);
                if (key && key.trim() != "") {
                    var apiKeyAuth = new SwaggerClient.ApiKeyAuthorization("api_key", key, "query");
                    window.swaggerUi.api.clientAuthorizations.add("api_key", apiKeyAuth);
                }
            }

            $('#input_apiKey').change(addApiKeyAuthorization);

            // if you have an apiKey you would like to pre-populate on the page for demonstration purposes...
            /*
              var apiKey = "myApiKeyXXXX123456789";
              $('#input_apiKey').val(apiKey);
            */

            window.swaggerUi.load();

            function log() {
                if ('console' in window) {
                    console.log.apply(console, arguments);
                }
            }
        });
    </script>
</head>

<body class="swagger-section">
    <div id='header'>
        <div class="swagger-ui-wrap">
            <a id="logo" href="http://swagger.io">swagger</a>
            <form id='api_selector'>
                <div class='input'><input placeholder="http://example.com/api" id="input_baseUrl" name="baseUrl"
                        type="text" /></div>
                <div class='input'><input placeholder="api_key" id="input_apiKey" name="apiKey" type="text" />
                </div>
                <div class='input'><a id="explore" href="#">Explore</a></div>
            </form>
        </div>
    </div>

    <div id="message-bar" class="swagger-ui-wrap">&nbsp;</div>
    <div id="swagger-ui-container" class="swagger-ui-wrap"></div>
</body>

</html>
